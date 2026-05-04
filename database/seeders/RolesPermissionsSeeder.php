<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\PurchaseRoleService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $abilities = [
            'read',
            'write',
            'create',
        ];

        $permissions_by_role = [
            'administrator' => [
                'user management',
                'content management',
                'financial management',
                'reporting',
                'payroll',
                'disputes management',
                'api controls',
                'database management',
                'repository management',
            ],
            'developer' => [
                'api controls',
                'database management',
                'repository management',
            ],
            'analyst' => [
                'content management',
                'financial management',
                'reporting',
                'payroll',
            ],
            'support' => [
                'reporting',
            ],
            'trial' => [
            ],
        ];

        foreach (['web', 'mobile'] as $guardName) {
            foreach ($permissions_by_role['administrator'] as $permission) {
                foreach ($abilities as $ability) {
                    Permission::findOrCreate($ability . ' ' . $permission, $guardName);
                }
            }

            foreach ($permissions_by_role as $role => $permissions) {
                $full_permissions_list = [];
                foreach ($abilities as $ability) {
                    foreach ($permissions as $permission) {
                        $full_permissions_list[] = $ability . ' ' . $permission;
                    }
                }

                Role::findOrCreate($role, $guardName)->syncPermissions($full_permissions_list);
            }
        }

        $purchasePermissions = [
            PurchaseRoleService::PERMISSION_CREATE_REQUESTS,
            PurchaseRoleService::PERMISSION_VIEW_DEPARTMENT_REQUESTS,
            PurchaseRoleService::PERMISSION_APPROVE_REQUESTS,
            PurchaseRoleService::PERMISSION_MANAGE_PURCHASE_ORDERS,
        ];

        $purchasePermissionsByRole = [
            PurchaseRoleService::ROLE_REQUESTER => [
                PurchaseRoleService::PERMISSION_CREATE_REQUESTS,
                PurchaseRoleService::PERMISSION_VIEW_DEPARTMENT_REQUESTS,
            ],
            PurchaseRoleService::ROLE_MANAGER => [
                PurchaseRoleService::PERMISSION_CREATE_REQUESTS,
                PurchaseRoleService::PERMISSION_VIEW_DEPARTMENT_REQUESTS,
                PurchaseRoleService::PERMISSION_APPROVE_REQUESTS,
            ],
            PurchaseRoleService::ROLE_PURCHASING_MANAGER => [
                PurchaseRoleService::PERMISSION_CREATE_REQUESTS,
                PurchaseRoleService::PERMISSION_VIEW_DEPARTMENT_REQUESTS,
                PurchaseRoleService::PERMISSION_APPROVE_REQUESTS,
                PurchaseRoleService::PERMISSION_MANAGE_PURCHASE_ORDERS,
            ],
        ];

        foreach (['web', 'mobile'] as $guardName) {
            foreach ($purchasePermissions as $permission) {
                Permission::findOrCreate($permission, $guardName);
            }

            foreach ($purchasePermissionsByRole as $roleName => $permissions) {
                Role::findOrCreate($roleName, $guardName)->syncPermissions($permissions);
            }
        }

        User::find(1)?->assignRole('administrator');
        User::find(2)?->assignRole('developer');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
