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

class StockAdjustmentController extends Controller
{
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
            'type' => 'required|in:addition,deduction',
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

                if ($type === 'addition') {
                    $newStock += $quantity;
                } elseif ($type === 'deduction') {
                    if ($currentStock < $quantity) {
                        throw new \Exception("Stok tidak mencukupi untuk bahan baku '{$rawMaterial->name}'. Stok saat ini: {$currentStock}, dibutuhkan: {$quantity}.");
                    }
                    $newStock -= $quantity;
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

                if ($type === 'deduction') {
                    $this->checkJitSignalForMaterial($rawMaterial->fresh());
                }
            });

            return redirect()->route('stock_adjustments.create')->with('success', 'Penyesuaian stok berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error("Error during stock adjustment: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penyesuaian stok: ' . $e->getMessage());
        }
    }

    private function checkJitSignalForMaterial(RawMaterial $material): void
    {
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
                    JitNotification::firstOrCreate(
                        [
                            'raw_material_id' => $material->id,
                            'status' => 'unread',
                        ],
                        [
                            'message' => "Stok {$material->name} mencapai titik kritis ({$material->stock} dari target {$material->signal_point}). Segera lakukan pemesanan ulang sebanyak {$material->replenish_quantity} unit.",
                        ]
                    );
                    Log::info("JIT Notification triggered for {$material->name} due to manual stock deduction.");
                }
            } else {
                Log::error("JIT API call failed for material ID {$material->id} after manual deduction. Status: {$response->status()}, Body: " . $response->body());
            }
        } catch (Exception $e) {
            Log::error("Exception during JIT API call for material ID {$material->id} after manual deduction: " . $e->getMessage());
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