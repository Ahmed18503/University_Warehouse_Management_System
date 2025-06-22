<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'product_category_id' => 1, // Laptops
                'name' => 'Dell XPS 13',
                'code' => 'LAP-' . Str::random(6),
                'description' => 'High-performance laptop with an Intel i7 processor and 16GB RAM.',
                'unit_id' => 1, // Assuming 1 is for 'Piece'
                'price' => 150000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 2, // Smartphones
                'name' => 'Samsung Galaxy S23',
                'code' => 'PHN-' . Str::random(6),
                'description' => 'Latest smartphone with advanced camera features and 256GB storage.',
                'unit_id' => 1, // Piece
                'price' => 80000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 3, // Tablets
                'name' => 'Apple iPad Pro 11"',
                'code' => 'TAB-' . Str::random(6),
                'description' => 'Premium tablet with M2 chip and 128GB storage.',
                'unit_id' => 1, // Piece
                'price' => 90000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 4, // Accessories
                'name' => 'Wireless Earbuds',
                'code' => 'ACC-' . Str::random(6),
                'description' => 'Bluetooth 5.0 wireless earbuds with noise cancellation.',
                'unit_id' => 1, // Piece
                'price' => 3000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 5, // PC Components
                'name' => 'NVIDIA GeForce RTX 4080',
                'code' => 'GPU-' . Str::random(6),
                'description' => 'High-end graphics card for gaming and creative work.',
                'unit_id' => 1, // Piece
                'price' => 200000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 2, // Networking Equipment
                'name' => 'TP-Link Archer AX6000',
                'code' => 'NET-' . Str::random(6),
                'description' => 'Dual-band Wi-Fi 6 router with high-speed connectivity.',
                'unit_id' => 1, // Piece
                'price' => 15000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_category_id' => 3, // Gaming Consoles
                'name' => 'PlayStation 5',
                'code' => 'CON-' . Str::random(6),
                'description' => 'Next-generation gaming console with 4K resolution and dual sense controller.',
                'unit_id' => 1, // Piece
                'price' => 60000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
