<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class WebsiteIdentityPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache untuk permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("🔄 Cleared permission cache");

        // Create permission untuk website identity management
        $permission = Permission::firstOrCreate([
            'name' => 'manage_website_identity',
            'guard_name' => 'web'
        ]);

        $this->command->info("✅ Permission 'manage_website_identity' created/updated (ID: {$permission->id})");

        // Debug: List all roles
        $this->command->info("📋 Available roles:");
        foreach (Role::all() as $role) {
            $this->command->info("   - {$role->name} (ID: {$role->id})");
        }

        // Try different role name variations
        $roleNames = ['Super Admin', 'super-admin', 'super_admin', 'Admin', 'admin'];
        $assignedRoles = [];

        foreach ($roleNames as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role && !in_array($role->id, $assignedRoles)) {
                try {
                    if (!$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                        $this->command->info("✅ Permission assigned to '{$role->name}' role");
                        $assignedRoles[] = $role->id;
                    } else {
                        $this->command->info("ℹ️ '{$role->name}' already has this permission");
                        $assignedRoles[] = $role->id;
                    }
                } catch (\Exception $e) {
                    $this->command->error("❌ Failed to assign permission to '{$role->name}': " . $e->getMessage());
                }
            }
        }

        if (empty($assignedRoles)) {
            $this->command->warn("⚠️ No admin roles found to assign permission");
            $this->command->info("💡 You can manually assign permission using:");
            $this->command->info("   php artisan tinker");
            $this->command->info("   \$role = Role::find(1); // Replace with your admin role ID");
            $this->command->info("   \$role->givePermissionTo('manage_website_identity');");
        } else {
            $this->command->info("🚀 Website Identity permissions setup completed!");
        }
    }
}