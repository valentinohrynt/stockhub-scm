<?php

namespace App\Services;

use App\Models\RawMaterial;
use App\Models\StockMovementLog;
use App\Models\JitNotification;
use Illuminate\Support\Facades\Log;

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
            $totalConsumed = StockMovementLog::where('raw_material_id', $material->id)
                ->whereIn('type', ['deduction', 'production_usage'])
                ->where('movement_date', '>=', now()->subDays($calculationPeriodDays)->toDateString())
                ->sum('quantity');
            
            $newAverage = $totalConsumed > 0 ? $totalConsumed / $calculationPeriodDays : 0;

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

            if ($updatedMaterial->stock <= $updatedMaterial->signal_point && $updatedMaterial->signal_point > 0) { 
                JitNotification::firstOrCreate(
                    [
                        'raw_material_id' => $updatedMaterial->id,
                        'status' => 'unread',
                    ],
                    [
                        'message' => "STOK HABIS: Stok {$updatedMaterial->name} kritis ({$updatedMaterial->stock} / {$updatedMaterial->signal_point}) setelah pembaruan analitik. Segera pesan ulang {$updatedMaterial->replenish_quantity} unit.",
                    ]
                );
                $notificationsTriggered++;
                Log::info("InventoryAnalyticsService: JIT Notification triggered for {$updatedMaterial->name}.");
            }
        }

        $message = "InventoryAnalyticsService: Calculation complete. Updated {$updatedCount} raw materials.";
        if ($notificationsTriggered > 0) {
            $message .= " Triggered {$notificationsTriggered} JIT notifications.";
        }
        Log::info($message);

        return $message;
    }
}