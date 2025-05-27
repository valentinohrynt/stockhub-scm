<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')
                ->constrained()
                ->onDelete('restrict');
            $table->string('image_path')->nullable();

            // KANBAN OR JIT RELATED FIELDS
            $table->integer('signal_point')->default(0);
            $table->integer('average_daily_usage')->default(0);
            $table->integer('replenish_quantity')->default(0);
            $table->integer('lead_time')->default(0);
            $table->integer('safety_stock')->default(0);

            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
