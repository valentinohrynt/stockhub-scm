<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InventoryAnalyticsService;

class UpdateInventoryAnalytics extends Command
{
    protected $signature = 'inventory:update-analytics';
    protected $description = 'Calculate and update JIT parameters for all raw materials using InventoryAnalyticsService.';


    public function handle(InventoryAnalyticsService $analyticsService) 
    {
        $this->info('Calling InventoryAnalyticsService to run calculations...');

        $resultMessage = $analyticsService->runCalculations();
        
        $this->info($resultMessage);
        
        return 0;
    }
}