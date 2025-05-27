<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->string('stock_unit')->after('stock')->nullable();
            $table->string('usage_unit')->after('stock_unit')->nullable();
            $table->decimal('conversion_factor', 12, 5)->after('usage_unit')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropColumn(['stock_unit', 'usage_unit', 'conversion_factor']);
        });
    }
};