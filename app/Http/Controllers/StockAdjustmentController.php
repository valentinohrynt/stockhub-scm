<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\RawMaterial;
use App\Models\JitNotification;
use App\Models\StockMovementLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\InventoryAnalyticsService;

class StockAdjustmentController extends Controller
{
    protected InventoryAnalyticsService $inventoryAnalyticsService;

    public function __construct(InventoryAnalyticsService $inventoryAnalyticsService)
    {
        $this->inventoryAnalyticsService = $inventoryAnalyticsService;
    }

    public function create()
    {
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('name')->get();
        return view('content.stock_adjustment.create', compact('rawMaterials'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:addition,deduction,initial_stock,correction,production_usage,breakage,transfer_out,transfer_in,manual_adjustment', // Pastikan tipe ini ada di enum migration
            'notes' => 'nullable|string|max:1000',
            'movement_date' => 'required|date',
        ]);

        $rawMaterial = RawMaterial::findOrFail($validatedData['raw_material_id']);
        $quantity = (int) $validatedData['quantity'];
        $type = $validatedData['type'];

        try {
            DB::transaction(function () use ($rawMaterial, $quantity, $type, $validatedData) {
                $currentStock = $rawMaterial->stock;
                $newStock = $currentStock;

                if ($type === 'addition' || $type === 'initial_stock' || $type === 'transfer_in' || ($type === 'correction' && $quantity > 0) || ($type === 'manual_adjustment' && $quantity > 0) ) {
                    $newStock += abs($quantity);
                } elseif ($type === 'deduction' || $type === 'production_usage' || $type === 'breakage' || $type === 'transfer_out' || ($type === 'correction' && $quantity < 0) || ($type === 'manual_adjustment' && $quantity < 0)) {
                    $absQuantity = abs($quantity);
                    if ($currentStock < $absQuantity) {
                        throw new \Exception("Stok tidak mencukupi untuk bahan baku '{$rawMaterial->name}'. Stok saat ini: {$currentStock}, dibutuhkan: {$absQuantity}.");
                    }
                    $newStock -= $absQuantity;
                } else {

                }


                $rawMaterial->stock = $newStock;
                $rawMaterial->save();

                StockMovementLog::create([
                    'raw_material_id' => $rawMaterial->id,
                    'user_id' => Auth::id(),
                    'type' => $type,
                    'quantity' => $quantity,
                    'unit_price_at_movement' => $rawMaterial->unit_price,
                    'notes' => $validatedData['notes'],
                    'movement_date' => $validatedData['movement_date'],
                ]);
            });

            $analyticsMessage = $this->inventoryAnalyticsService->runCalculations();
            Log::info("Inventory analytics recalculated after stock adjustment for material ID {$rawMaterial->id}. Result: {$analyticsMessage}");

            $updatedRawMaterial = $rawMaterial->fresh();

            $this->checkJitSignalForMaterial($updatedRawMaterial);

            return redirect()->route('stock_adjustments')
                             ->with('success', "Penyesuaian stok berhasil disimpan. " . $analyticsMessage);

        } catch (\Exception $e) {
            Log::error("Error during stock adjustment or analytics recalculation: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penyesuaian stok: ' . $e->getMessage());
        }
    }

    private function checkJitSignalForMaterial(RawMaterial $material): void
    {
        if (is_null($material->signal_point) || is_null($material->replenish_quantity) || $material->signal_point <= 0) {
            Log::info("JIT signal check skipped for {$material->name} due to invalid JIT parameters (signal_point: {$material->signal_point}, replenish_quantity: {$material->replenish_quantity}).");
            return;
        }

        if ($material->stock <= $material->signal_point) {
            $flaskApiUrl = env('FLASK_API_URL', 'https://cafehub-forecast-api.vercel.app');

            try {
                $payload = [
                    'product_name' => $material->name,
                    'current_stock' => $material->stock,
                    'signal_point' => $material->signal_point,
                    'replenish_quantity' => $material->replenish_quantity,
                ];

                $response = Http::timeout(10)->post("{$flaskApiUrl}/jit-signal-event", $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['action_required']) && $data['action_required'] === 'INITIATE_JIT_REPLENISHMENT') {
                        $existingNotification = JitNotification::where('raw_material_id', $material->id)
                                                              ->where('status', 'unread')
                                                              ->first();
                        if (!$existingNotification) {
                            JitNotification::create([
                                'raw_material_id' => $material->id,
                                'message' => "Stok {$material->name} mencapai titik kritis ({$material->stock} dari target {$material->signal_point}). Segera lakukan pemesanan ulang sebanyak {$material->replenish_quantity} unit.",
                                'status' => 'unread',
                            ]);
                            Log::info("JIT Notification triggered for {$material->name} after stock adjustment. Stock: {$material->stock}, Signal: {$material->signal_point}");
                        } else {
                            Log::info("JIT Notification for {$material->name} already exists and is unread. No new notification created.");
                        }
                    }
                } else {
                    Log::error("JIT API call failed for material ID {$material->id} after stock adjustment. Status: {$response->status()}, Body: " . $response->body());
                }
            } catch (Exception $e) {
                Log::error("Exception during JIT API call for material ID {$material->id} after stock adjustment: " . $e->getMessage());
            }
        } else {
            Log::info("JIT signal not triggered for {$material->name}. Stock ({$material->stock}) is above signal point ({$material->signal_point}).");
        }
    }

    public function index(Request $request)
    {
        $query = StockMovementLog::with(['rawMaterial', 'user'])->latest('movement_date');

        if ($request->filled('search_raw_material')) {
            $searchTerm = $request->input('search_raw_material');
            $query->whereHas('rawMaterial', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('movement_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('movement_date', '<=', $request->input('end_date'));
        }

        $stockMovements = $query->paginate(20)->withQueryString();
        $rawMaterials = RawMaterial::orderBy('name')->get();

        return view('content.stock_adjustment.index', compact('stockMovements', 'rawMaterials'));
    }
}