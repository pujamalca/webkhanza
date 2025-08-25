<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'user_create',
            'user_read', 
            'user_update',
            'user_delete',
            'user_reset_device',
            
            // Role Management
            'role_create',
            'role_read',
            'role_update', 
            'role_delete',
            
            // System Management
            'system_settings',
            'system_logs',
            
            // Dashboard
            'dashboard_access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['guard_name' => 'web']
        );
        $superAdmin->givePermissionTo(Permission::all());
        
        // Admin - Most permissions except system critical
        $admin = Role::firstOrCreate(
            ['name' => 'Admin'], 
            ['guard_name' => 'web']
        );
        $admin->givePermissionTo([
            'user_create', 'user_read', 'user_update', 'user_reset_device',
            'role_read',
            'dashboard_access'
        ]);
        
        // Manager - User management only
        $manager = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['guard_name' => 'web']
        );
        $manager->givePermissionTo([
            'user_read', 'user_update', 'user_reset_device',
            'dashboard_access'
        ]);
        
        // User - Basic access only
        $user = Role::firstOrCreate(
            ['name' => 'User'],
            ['guard_name' => 'web']
        );
        $user->givePermissionTo([
            'dashboard_access'
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Created roles: Super Admin, Admin, Manager, User');
        $this->command->info('Created ' . count($permissions) . ' permissions');
    }
}
