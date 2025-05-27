<?php

namespace App\Traits;

use App\Models\RawMaterial;
use Illuminate\Http\Request;

trait CalculatesJitParameters
{
    protected function calculateJitParameters(Request $request, RawMaterial $material): array
    {
        $leadTime = (int) $request->input('lead_time', $material->lead_time);
        $safetyStockDays = (int) $request->input('safety_stock_days', 0);

        $avgDailyUsage = (int) $material->average_daily_usage;

        $calculatedSafetyStock = $avgDailyUsage * $safetyStockDays;
        
        $calculatedSignalPoint = ($avgDailyUsage * $leadTime) + $calculatedSafetyStock;

        return [
            'safety_stock' => $calculatedSafetyStock,
            'signal_point' => $calculatedSignalPoint,
        ];
    }
}