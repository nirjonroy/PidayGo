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
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'admin',
        ]);

        $permission = Permission::firstOrCreate([
            'name' => 'kyc.review',
            'guard_name' => 'admin',
        ]);

        $sitePermission = Permission::firstOrCreate([
            'name' => 'site.manage',
            'guard_name' => 'admin',
        ]);

        $rolePermission = Permission::firstOrCreate([
            'name' => 'role.manage',
            'guard_name' => 'admin',
        ]);

        $permPermission = Permission::firstOrCreate([
            'name' => 'permission.manage',
            'guard_name' => 'admin',
        ]);

        $userPermission = Permission::firstOrCreate([
            'name' => 'user.manage',
            'guard_name' => 'admin',
        ]);

        $adminPermission = Permission::firstOrCreate([
            'name' => 'admin.manage',
            'guard_name' => 'admin',
        ]);

        $activityPermission = Permission::firstOrCreate([
            'name' => 'activity.view',
            'guard_name' => 'admin',
        ]);

        $stakingPermission = Permission::firstOrCreate([
            'name' => 'staking.manage',
            'guard_name' => 'admin',
        ]);

        $withdrawalPermission = Permission::firstOrCreate([
            'name' => 'withdrawal.review',
            'guard_name' => 'admin',
        ]);

        $reservePermission = Permission::firstOrCreate([
            'name' => 'reserve.manage',
            'guard_name' => 'admin',
        ]);

        $role->givePermissionTo($permission);
        $role->givePermissionTo($sitePermission);
        $role->givePermissionTo($rolePermission);
        $role->givePermissionTo($permPermission);
        $role->givePermissionTo($userPermission);
        $role->givePermissionTo($adminPermission);
        $role->givePermissionTo($activityPermission);
        $role->givePermissionTo($stakingPermission);
        $role->givePermissionTo($withdrawalPermission);
        $role->givePermissionTo($reservePermission);

        Admin::query()->each(function (Admin $admin) use ($role) {
            if (!$admin->hasRole($role)) {
                $admin->assignRole($role);
            }
        });
    }
}
