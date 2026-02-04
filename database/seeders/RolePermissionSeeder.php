<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $role = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'admin',
        ]);

        $permission = Permission::firstOrCreate([
            'name' => 'kyc.review',
            'guard_name' => 'admin',
        ]);

        $role->givePermissionTo($permission);

        $admin = Admin::first();
        if ($admin) {
            $admin->assignRole($role);
        }
    }
}
