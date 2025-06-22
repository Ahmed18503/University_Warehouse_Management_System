<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warehouses = [
            ['name' => 'مخزن كلية الهندسة'],
            ['name' => 'مخزن كلية الطب'],
            ['name' => 'مخزن كلية العلوم'],
            ['name' => 'مخزن كلية الآداب'],
            ['name' => 'مخزن كلية الحقوق'],
            ['name' => 'مخزن كلية الاقتصاد وإدارة الأعمال'],
            ['name' => 'مخزن كلية الصيدلة'],
            ['name' => 'مخزن كلية طب الأسنان'],
            ['name' => 'مخزن كلية الشريعة'],
            ['name' => 'مخزن كلية التربية'],
            ['name' => 'مخزن كلية الفنون الجميلة'],
            ['name' => 'مخزن كلية الزراعة'],
            ['name' => 'مخزن كلية تكنولوجيا المعلومات'],
            ['name' => 'مخزن كلية التمريض'],
            ['name' => 'مخزن كلية العمارة'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate($warehouse);
        }
    }
} 