<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            [
                'name' => 'Food',
                'type' => 'product',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Drink',
                'type' => 'product',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Daily Needs',
                'type' => 'raw_material',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Bulk Supplies',
                'type' => 'raw_material',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
