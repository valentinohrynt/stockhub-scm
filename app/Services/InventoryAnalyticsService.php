<?php

namespace App\Services;

use App\Models\RawMaterial;
use App\Models\StockMovementLog;
use App\Models\JitNotification;
use Illuminate\Support\Facades\DB;
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

            if ($updatedMaterial->stock <= $updatedMaterial->signal_point && $updatedMaterial->signal_point > 0) { 
                JitNotification::firstOrCreate(
                    [
                        'raw_material_id' => $updatedMaterial->id,
                        'status' => 'unread',
                        // 'message' => "STOK HABIS: Stok {$updatedMaterial->name} kritis ({$updatedMaterial->stock} / {$updatedMaterial->signal_point}) setelah pembaruan analitik. Segera pesan ulang {$updatedMaterial->replenish_quantity} unit." 
                    ],
                    [
                        'message' => "STOK HABIS: Stok {$updatedMaterial->name} kritis ({$updatedMaterial->stock} {$updatedMaterial->stock_unit} / {$updatedMaterial->signal_point} {$updatedMaterial->stock_unit}) setelah pembaruan analitik. Segera pesan ulang {$updatedMaterial->replenish_quantity} {$updatedMaterial->stock_unit}.",
                    ]
                );
                $notificationsTriggered++;
                Log::info("InventoryAnalyticsService: JIT Notification triggered for {$updatedMaterial->name}. Stock: {$updatedMaterial->stock}, Signal: {$updatedMaterial->signal_point}");
            } else {
                 Log::info("InventoryAnalyticsService: JIT Notification NOT triggered for {$updatedMaterial->name}. Stock: {$updatedMaterial->stock}, Signal: {$updatedMaterial->signal_point}");
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