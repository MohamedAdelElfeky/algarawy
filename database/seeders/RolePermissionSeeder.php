<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view_dashboard',
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'manage_settings',
            'view_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

//         $adminRole->syncPermissions($permissions);
//
//         $userRole->syncPermissions(['view_dashboard']);
//
//         $admin = User::where('email', 'admin@admin.com')->first();
//         if ($admin) {
//             $admin->assignRole('admin', 'web');
//         }
//
//         $user = User::where('email', 'user@user.com')->first();
//         if ($user) {
//             $user->assignRole('user', 'web');
//         }
    }
}
