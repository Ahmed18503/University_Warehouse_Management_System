<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class ManagerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managerRole = Role::firstOrCreate(
            ['title' => 'مدير'],
        );

        $permissions = [
            'approve obsolete conversion requests',
            'request obsolete conversion',
            'manage product deletion requests',
            'approve product deletion requests',
            'submit product deletion requests',
            'manage inventory transfers',
            'manage units',
            'manage products',
            'manage product categories',
            'manage roles',
            'manage users',
            'manage warehouses',
        ];

        $managerRole->permissions = json_encode($permissions);
        $managerRole->save();
    }
} 