<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebsiteIdentity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class EssentialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding essential data...');

        // 1. Create Website Identity if not exists
        $this->createWebsiteIdentity();

        // 2. Create essential permissions
        $this->createEssentialPermissions();

        // 3. Create basic roles
        $this->createBasicRoles();

        // 4. Ensure admin user has proper permissions
        $this->assignAdminPermissions();

        $this->command->info('✓ Essential data seeded successfully!');
    }

    private function createWebsiteIdentity(): void
    {
        if (WebsiteIdentity::count() == 0) {
            WebsiteIdentity::create([
                'name' => 'WebKhanza',
                'tagline' => 'Sistem Informasi Kesehatan Terpadu',
                'description' => 'Platform terintegrasi untuk manajemen rumah sakit, klinik, dan fasilitas kesehatan dengan teknologi modern dan user-friendly interface.',
                'address' => 'Jl. Kesehatan Raya No. 123, Jakarta Pusat, DKI Jakarta 10110, Indonesia',
                'phone' => '+62 21 8012-3456',
                'email' => 'info@webkhanza.com',
                'social_media' => json_encode([
                    'website' => 'https://webkhanza.com',
                    'facebook' => 'https://facebook.com/webkhanza',
                    'twitter' => 'https://twitter.com/webkhanza',
                    'instagram' => 'https://instagram.com/webkhanza',
                    'linkedin' => 'https://linkedin.com/company/webkhanza',
                    'youtube' => 'https://youtube.com/@webkhanza',
                ]),
                'colors' => json_encode([
                    'primary' => '#2563eb',
                    'secondary' => '#1e40af',
                    'accent' => '#dc2626',
                ]),
                'primary_color' => '#2563eb',
                'secondary_color' => '#1e40af',
                'accent_color' => '#dc2626',
                'landing_template' => 'default'
            ]);
            $this->command->info('✓ Website identity created');
        } else {
            $this->command->info('✓ Website identity already exists');
        }
    }

    private function createEssentialPermissions(): void
    {
        $permissions = [
            'administrator_access',
            'website_management',
            'blog_management',
            'sdm_access',
            'pegawai_access',
            'erm_access'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        $this->command->info('✓ Essential permissions created');
    }

    private function createBasicRoles(): void
    {
        // Create Super Admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        
        // Give Super Admin all permissions
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);
        
        $this->command->info('✓ Super Admin role created with all permissions');

        // Create basic roles
        $roles = [
            'Administrator' => ['administrator_access', 'website_management', 'blog_management'],
            'Manager' => ['sdm_access', 'pegawai_access'],
            'Staff' => ['pegawai_access'],
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $permissions = Permission::whereIn('name', $permissionNames)->get();
            if ($permissions->count() > 0) {
                $role->syncPermissions($permissions);
                $this->command->info("✓ {$roleName} role created");
            }
        }
    }

    private function assignAdminPermissions(): void
    {
        $adminUsers = User::whereIn('email', ['admin@webkhanza.com', 'admin@example.com', 'admin@admin.com'])
            ->orWhere('name', 'Admin')
            ->get();

        foreach ($adminUsers as $user) {
            if (!$user->hasRole('Super Admin')) {
                $user->assignRole('Super Admin');
                $this->command->info("✓ Assigned Super Admin role to: {$user->name}");
            }
        }

        // Also assign to first user if no admin found
        if ($adminUsers->isEmpty()) {
            $firstUser = User::first();
            if ($firstUser && !$firstUser->hasRole('Super Admin')) {
                $firstUser->assignRole('Super Admin');
                $this->command->info("✓ Assigned Super Admin role to first user: {$firstUser->name}");
            }
        }
    }
}