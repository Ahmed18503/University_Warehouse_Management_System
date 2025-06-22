<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_categories')->insert([
            [
                'name' => 'مالي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مستديم',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مستهلك',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'عهدة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'كهنة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
