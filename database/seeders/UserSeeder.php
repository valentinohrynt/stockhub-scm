<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        \DB::table('users')->insert([
            [
                'name' => 'Administator',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin123'),
                'role_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'sitiaminah@gmail.com',
                'password' => bcrypt('supervisor123'),
                'role_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budisantoso@gmail.com',
                'password' => bcrypt('staff123'),
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
