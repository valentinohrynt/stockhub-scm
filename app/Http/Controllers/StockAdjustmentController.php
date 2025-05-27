<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
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
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('content.stock_adjustment.create', compact('rawMaterials', 'products'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:addition,deduction,initial_stock,correction,production_usage,breakage,transfer_out,transfer_in,manual_adjustment',
            'raw_material_id' => 'required_if:type,addition,deduction,initial_stock,correction,breakage,transfer_out,transfer_in,manual_adjustment|nullable|exists:raw_materials,id',
            'product_id' => 'required_if:type,production_usage|nullable|exists:products,id',
            'quantity' => 'required|numeric|min:0.00001',
            'quantity_input_unit' => 'nullable|string|in:stock_unit,usage_unit',
            'notes' => 'nullable|string|max:1000',
            'movement_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $type = $validatedData['type'];
            $quantityInput = (float) $validatedData['quantity'];

            if ($type === 'production_usage') {
                $product = Product::with('billOfMaterial.rawMaterial')->findOrFail($validatedData['product_id']);
                $productsToProduce = $quantityInput;

                if ($product->billOfMaterial->isEmpty()) {
                    throw new \Exception("Product '{$product->name}' has no Bill of Materials.");
                }

                foreach ($product->billOfMaterial as $bomItem) {
                    if (!$bomItem->is_active) continue; 
                    $rawMaterial = $bomItem->rawMaterial;
                    if(!$rawMaterial || !$rawMaterial->is_active) continue; 

                    $quantityPerProductInUsageUnit = (float) $bomItem->quantity;
                    $totalUsageInUsageUnit = $quantityPerProductInUsageUnit * $productsToProduce;

                    if (!$rawMaterial->conversion_factor || $rawMaterial->conversion_factor <= 0) {
                        throw new \Exception("Invalid conversion factor for {$rawMaterial->name}.");
                    }
                    $totalUsageInStockUnit = $totalUsageInUsageUnit / $rawMaterial->conversion_factor;

                    if ($rawMaterial->stock < $totalUsageInStockUnit) {
                        throw new \Exception("Insufficient stock for {$rawMaterial->name}. Required: {$totalUsageInStockUnit} {$rawMaterial->stock_unit}, Available: {$rawMaterial->stock} {$rawMaterial->stock_unit}");
                    }
                    $rawMaterial->decrement('stock', $totalUsageInStockUnit);
                    StockMovementLog::create([
                        'raw_material_id' => $rawMaterial->id,
                        'user_id' => Auth::id(),
                        'type' => 'production_usage',
                        'quantity' => -$totalUsageInStockUnit,
                        'unit_price_at_movement' => $rawMaterial->unit_price,
                        'notes' => $validatedData['notes'] . " (For {$productsToProduce} units of {$product->name})",
                        'movement_date' => $validatedData['movement_date'],
                    ]);
                    $this->checkJitSignalForMaterial($rawMaterial->fresh());
                }
            } else {
                $rawMaterial = RawMaterial::findOrFail($validatedData['raw_material_id']);
                $inputUnit = $request->input('quantity_input_unit', 'stock_unit');
                $quantityInStockUnit = $quantityInput;

                if ($inputUnit === 'usage_unit' && $rawMaterial->usage_unit && $rawMaterial->stock_unit !== $rawMaterial->usage_unit) {
                    if (!$rawMaterial->conversion_factor || $rawMaterial->conversion_factor <= 0) {
                        throw new \Exception("Invalid conversion factor for {$rawMaterial->name} to convert from {$rawMaterial->usage_unit}.");
                    }
                    $quantityInStockUnit = $quantityInput / $rawMaterial->conversion_factor;
                }

                $currentStock = $rawMaterial->stock;
                $newStock = $currentStock;
                $actualMovementQuantity = 0;

                if (in_array($type, ['addition', 'initial_stock', 'transfer_in', 'correction', 'manual_adjustment'])) {
                    $newStock += $quantityInStockUnit;
                    $actualMovementQuantity = $quantityInStockUnit;
                } elseif (in_array($type, ['deduction', 'breakage', 'transfer_out'])) {
                    if ($currentStock < $quantityInStockUnit) {
                        throw new \Exception("Insufficient stock for {$rawMaterial->name}. Current: {$currentStock} {$rawMaterial->stock_unit}, To Deduct: {$quantityInStockUnit} {$rawMaterial->stock_unit}.");
                    }
                    $newStock -= $quantityInStockUnit;
                    $actualMovementQuantity = -$quantityInStockUnit;
                }

                $rawMaterial->stock = $newStock;
                $rawMaterial->save();

                StockMovementLog::create([
                    'raw_material_id' => $rawMaterial->id,
                    'user_id' => Auth::id(),
                    'type' => $type,
                    'quantity' => $actualMovementQuantity,
                    'unit_price_at_movement' => $rawMaterial->unit_price,
                    'notes' => $validatedData['notes'],
                    'movement_date' => $validatedData['movement_date'],
                ]);
                $this->checkJitSignalForMaterial($rawMaterial->fresh());
            }
            DB::commit();
            return redirect()->route('stock_adjustments')->with('success', "Stock adjustment saved successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Stock Adjustment Error: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
            return redirect()->back()->withInput()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    private function checkJitSignalForMaterial(RawMaterial $material): void
    {
        $this->inventoryAnalyticsService->runCalculations($material);
        $material->refresh();

        if (is_null($material->signal_point) || is_null($material->replenish_quantity) || $material->signal_point < 0) {
            Log::info("JIT signal check skipped for {$material->name}: Invalid JIT parameters.");
            return;
        }
        if ($material->stock <= $material->signal_point) {
            $flaskApiUrl = env('FLASK_API_URL', 'https://stockhub-jit-api.vercel.app');
            try {
                $payload = [
                    'product_name' => $material->name,
                    'current_stock' => $material->stock,
                    'stock_unit' => $material->stock_unit,
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
                                'message' => "Stock {$material->name} kritis ({$material->stock} {$material->stock_unit} / {$material->signal_point} {$material->stock_unit}). Segera pesan ulang {$material->replenish_quantity} {$material->stock_unit}.",
                                'status' => 'unread',
                            ]);
                            Log::info("JIT Notification for {$material->name}. Stock: {$material->stock} {$material->stock_unit}, Signal: {$material->signal_point} {$material->stock_unit}");
                        } else {
                            Log::info("JIT Notification for {$material->name} already unread.");
                        }
                    }
                } else {
                    Log::error("JIT API call failed for {$material->id}. Status: {$response->status()}, Body: " . $response->body());
                }
            } catch (Exception $e) {
                Log::error("JIT API call exception for {$material->id}: " . $e->getMessage());
            }
        } else {
            Log::info("JIT signal not triggered for {$material->name}. Stock ({$material->stock} {$material->stock_unit}) > Signal ({$material->signal_point} {$material->stock_unit}).");
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
        return view('content.stock_adjustment.index', compact('stockMovements'));
    }
}