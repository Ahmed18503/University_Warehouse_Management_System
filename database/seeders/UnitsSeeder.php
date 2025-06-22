<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            [
                'name' => 'كيلو',
                'symbol' => 'kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'جرام',
                'symbol' => 'g',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'لتر',
                'symbol' => 'L',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'قطعة',
                'symbol' => 'pc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'دستة',
                'symbol' => 'dz',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'صندوق',
                'symbol' => 'box',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
