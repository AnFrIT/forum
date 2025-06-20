<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Forum permissions
            'view forum',
            'create topics',
            'edit own topics',
            'delete own topics',
            'create posts',
            'edit own posts',
            'delete own posts',
            'upload attachments',

            // Moderation permissions
            'edit any topics',
            'delete any topics',
            'edit any posts',
            'delete any posts',
            'lock topics',
            'pin topics',
            'approve topics',
            'approve posts',
            'ban users',
            'view reports',
            'manage reports',

            // Admin permissions
            'access admin panel',
            'manage categories',
            'manage users',
            'manage roles',
            'manage settings',
            'view statistics',
            'manage moderators',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles

        // User role
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view forum',
            'create topics',
            'edit own topics',
            'delete own topics',
            'create posts',
            'edit own posts',
            'delete own posts',
            'upload attachments',
        ]);

        // Moderator role
        $moderatorRole = Role::create(['name' => 'moderator']);
        $moderatorRole->givePermissionTo([
            'view forum',
            'create topics',
            'edit own topics',
            'delete own topics',
            'create posts',
            'edit own posts',
            'delete own posts',
            'upload attachments',
            'edit any topics',
            'delete any topics',
            'edit any posts',
            'delete any posts',
            'lock topics',
            'pin topics',
            'approve topics',
            'approve posts',
            'ban users',
            'view reports',
            'manage reports',
        ]);

        // Admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
