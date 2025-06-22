<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'Safaricom PLC',
                'email' => 'contact@safaricom.co.ke',
                'address' => 'Nairobi, Kenya, Safaricom House, Waiyaki Way',
                'phone_number' => '+254 722 123 456',
                'registration_number' => 'PVT-00001234',
                'tax_id' => 'P0123456789Q',
                'bank_id' => 1, // Adjust this ID based on your actual `banks` table
                'account_number' => '1234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kenya Airways',
                'email' => 'info@kenya-airways.com',
                'address' => 'Nairobi, Kenya, JKIA',
                'phone_number' => '+254 20 327 4848',
                'registration_number' => 'PVT-00005678',
                'tax_id' => 'PVT87654321',
                'bank_id' => 2, // Adjust this ID based on your actual `banks` table
                'account_number' => '0987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'East African Breweries Limited',
                'email' => 'contact@eabl.com',
                'address' => 'Nairobi, Kenya, Ruaraka',
                'phone_number' => '+254 20 360 6000',
                'registration_number' => 'PVT-00009123',
                'tax_id' => 'PVT12398765',
                'bank_id' => 3, // Adjust this ID based on your actual `banks` table
                'account_number' => '1122334455',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bidco Africa Limited',
                'email' => 'info@bidco.co.ke',
                'address' => 'Thika, Kenya, Bidco Road',
                'phone_number' => '+254 67 203 4567',
                'registration_number' => 'PVT-00002345',
                'tax_id' => 'PVT34567890',
                'bank_id' => 4, // Adjust this ID based on your actual `banks` table
                'account_number' => '2233445566',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nation Media Group',
                'email' => 'info@nationmedia.com',
                'address' => 'Nairobi, Kenya, Nation Centre',
                'phone_number' => '+254 20 328 8000',
                'registration_number' => 'PVT-00004567',
                'tax_id' => 'PVT56789012',
                'bank_id' => 5, // Adjust this ID based on your actual `banks` table
                'account_number' => '3344556677',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
