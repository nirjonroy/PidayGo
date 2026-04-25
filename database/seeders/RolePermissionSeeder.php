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

        $homeSlidePermission = Permission::firstOrCreate([
            'name' => 'home.slide.manage',
            'guard_name' => 'admin',
        ]);

        $blogPermission = Permission::firstOrCreate([
            'name' => 'blog.manage',
            'guard_name' => 'admin',
        ]);

        $footerPermission = Permission::firstOrCreate([
            'name' => 'footer.manage',
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

        $levelPermission = Permission::firstOrCreate([
            'name' => 'level.manage',
            'guard_name' => 'admin',
        ]);

        $withdrawalPermission = Permission::firstOrCreate([
            'name' => 'withdrawal.review',
            'guard_name' => 'admin',
        ]);

        $depositPermission = Permission::firstOrCreate([
            'name' => 'deposit.review',
            'guard_name' => 'admin',
        ]);

        $depositAddressPermission = Permission::firstOrCreate([
            'name' => 'deposit.address.manage',
            'guard_name' => 'admin',
        ]);

        $notificationPermission = Permission::firstOrCreate([
            'name' => 'notification.manage',
            'guard_name' => 'admin',
        ]);

        $sellerPermission = Permission::firstOrCreate([
            'name' => 'seller.manage',
            'guard_name' => 'admin',
        ]);

        $nftPermission = Permission::firstOrCreate([
            'name' => 'nft.manage',
            'guard_name' => 'admin',
        ]);

        $bidPermission = Permission::firstOrCreate([
            'name' => 'bid.manage',
            'guard_name' => 'admin',
        ]);

        $supportPermission = Permission::firstOrCreate([
            'name' => 'support.manage',
            'guard_name' => 'admin',
        ]);

        $mailPermission = Permission::firstOrCreate([
            'name' => 'mail.manage',
            'guard_name' => 'admin',
        ]);

        $paymentSettingPermission = Permission::firstOrCreate([
            'name' => 'payment.settings.manage',
            'guard_name' => 'admin',
        ]);

        $reservePermission = Permission::firstOrCreate([
            'name' => 'reserve.manage',
            'guard_name' => 'admin',
        ]);

        $chainPermission = Permission::firstOrCreate([
            'name' => 'chain.manage',
            'guard_name' => 'admin',
        ]);

        $role->givePermissionTo($permission);
        $role->givePermissionTo($sitePermission);
        $role->givePermissionTo($homeSlidePermission);
        $role->givePermissionTo($blogPermission);
        $role->givePermissionTo($footerPermission);
        $role->givePermissionTo($rolePermission);
        $role->givePermissionTo($permPermission);
        $role->givePermissionTo($userPermission);
        $role->givePermissionTo($adminPermission);
        $role->givePermissionTo($activityPermission);
        $role->givePermissionTo($stakingPermission);
        $role->givePermissionTo($levelPermission);
        $role->givePermissionTo($withdrawalPermission);
        $role->givePermissionTo($depositPermission);
        $role->givePermissionTo($depositAddressPermission);
        $role->givePermissionTo($notificationPermission);
        $role->givePermissionTo($sellerPermission);
        $role->givePermissionTo($nftPermission);
        $role->givePermissionTo($bidPermission);
        $role->givePermissionTo($supportPermission);
        $role->givePermissionTo($mailPermission);
        $role->givePermissionTo($paymentSettingPermission);
        $role->givePermissionTo($reservePermission);
        $role->givePermissionTo($chainPermission);

        Admin::query()->each(function (Admin $admin) use ($role) {
            if (!$admin->hasRole($role)) {
                $admin->assignRole($role);
            }
        });
    }
}
