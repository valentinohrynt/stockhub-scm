<?php

namespace App\Services;

use App\Models\RawMaterial;
use App\Models\StockMovementLog;
use App\Models\JitNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryAnalyticsService
{
    public function runCalculations(): string
    {
        Log::info('InventoryAnalyticsService: Starting calculation process...');

        $materials = RawMaterial::where('is_active', true)->get();
        if ($materials->isEmpty()) {
            $message = 'InventoryAnalyticsService: No active raw materials found to analyze.';
            Log::info($message);
            return $message;
        }
        
        $calculationPeriodDays = 30; 
        $updatedCount = 0;
        $notificationsTriggered = 0;

        foreach ($materials as $material) {
            $totalConsumedAbsolute = StockMovementLog::where('raw_material_id', $material->id)
                ->whereIn('type', ['deduction', 'production_usage', 'breakage']) 
                ->where('movement_date', '>=', now()->subDays($calculationPeriodDays)->toDateString())
                ->sum(DB::raw('ABS(quantity)')); 

            $newAverage = $totalConsumedAbsolute > 0 ? $totalConsumedAbsolute / $calculationPeriodDays : 0;

            $safetyStockDays = $material->safety_stock_days ?? 0;
            $calculatedSafetyStock = $newAverage * $safetyStockDays;

            $leadTime = $material->lead_time ?? 0;
            $calculatedSignalPoint = ($newAverage * $leadTime) + $calculatedSafetyStock;

            $material->update([
                'average_daily_usage' => round($newAverage, 2),
                'safety_stock'        => round($calculatedSafetyStock),
                'signal_point'        => round($calculatedSignalPoint),
            ]);
            
            $updatedCount++;

            $updatedMaterial = $material->fresh();

            if (is_null($updatedMaterial->signal_point) || is_null($updatedMaterial->replenish_quantity) || $updatedMaterial->signal_point < 0) {
                Log::info("InventoryAnalyticsService: JIT signal check skipped for {$updatedMaterial->name}: Invalid JIT parameters after calculation.");
            } 
            else if ($updatedMaterial->stock <= $updatedMaterial->signal_point && $updatedMaterial->signal_point > 0) {
                $flaskApiUrl = env('FLASK_API_URL', 'https://stockhub-jit-api.vercel.app');
                try {
                    $payload = [
                        'product_name'       => $updatedMaterial->name,
                        'current_stock'      => $updatedMaterial->stock,
                        'stock_unit'         => $updatedMaterial->stock_unit,
                        'signal_point'       => $updatedMaterial->signal_point,
                        'replenish_quantity' => $updatedMaterial->replenish_quantity,
                    ];
                    
                    Log::info("InventoryAnalyticsService: Calling JIT API for {$updatedMaterial->name}", $payload);
                    $response = Http::timeout(10)->post("{$flaskApiUrl}/jit-signal-event", $payload);

                    if ($response->successful()) {
                        $data = $response->json();
                        Log::info("InventoryAnalyticsService: JIT API success for {$updatedMaterial->name}. Response: ", $data);
                        if (isset($data['action_required']) && $data['action_required'] === 'INITIATE_JIT_REPLENISHMENT') {
                            $existingNotification = JitNotification::where('raw_material_id', $updatedMaterial->id)
                                                                    ->where('status', 'unread')
                                                                    ->first();
                            if (!$existingNotification) {
                                JitNotification::create([
                                    'raw_material_id' => $updatedMaterial->id,
                                    'message' => "STOK HABIS (API Confirmed): Stok {$updatedMaterial->name} kritis ({$updatedMaterial->stock} {$updatedMaterial->stock_unit} / {$updatedMaterial->signal_point} {$updatedMaterial->stock_unit}). Segera pesan ulang {$updatedMaterial->replenish_quantity} {$updatedMaterial->stock_unit}.",
                                    'status' => 'unread',
                                ]);
                                $notificationsTriggered++;
                                Log::info("InventoryAnalyticsService: JIT Notification created for {$updatedMaterial->name} based on API response. Stock: {$updatedMaterial->stock} {$updatedMaterial->stock_unit}, Signal: {$updatedMaterial->signal_point} {$updatedMaterial->stock_unit}");
                            } else {
                                Log::info("InventoryAnalyticsService: JIT Notification for {$updatedMaterial->name} already exists and is unread (API confirmed).");
                            }
                        } else {
                             Log::info("InventoryAnalyticsService: JIT API for {$updatedMaterial->name} did not require replenishment action.");
                        }
                    } else {
                        Log::error("InventoryAnalyticsService: JIT API call failed for {$updatedMaterial->id}. Status: {$response->status()}, Body: " . $response->body());
                    }
                } catch (Exception $e) {
                    Log::error("InventoryAnalyticsService: JIT API call exception for {$updatedMaterial->id}: " . $e->getMessage());
                }
            } else {
                 Log::info("InventoryAnalyticsService: JIT signal NOT triggered for {$updatedMaterial->name} after calculation. Stock ({$updatedMaterial->stock} {$updatedMaterial->stock_unit}) > Signal ({$updatedMaterial->signal_point} {$updatedMaterial->stock_unit}).");
            }
        }

        $message = "InventoryAnalyticsService: Calculation complete. Updated {$updatedCount} raw materials.";
        if ($notificationsTriggered > 0) {
            $message .= " Triggered {$notificationsTriggered} JIT notifications based on API confirmation.";
        }
        Log::info($message);

        return $message;
    }
}