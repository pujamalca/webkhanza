<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FilamentClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Filament cluster permissions and roles...');

        // Define cluster permissions
        $clusterPermissions = [
            // Administrator/UserRole cluster permissions
            'administrator_access' => 'Access Administrator cluster',
            'user_management' => 'Manage users',
            'role_management' => 'Manage roles and permissions',
            'activity_log_view' => 'View activity logs',
            'tracker_view' => 'View trackers',
            'sql_activity_view' => 'View SQL activity logs',
            
            // Website cluster permissions
            'website_management' => 'Access Website Management cluster',
            'website_identity_manage' => 'Manage website identity',
            'blog_management' => 'Manage blogs',
            'blog_category_management' => 'Manage blog categories',
            'blog_tag_management' => 'Manage blog tags',
            
            // SDM cluster permissions
            'sdm_access' => 'Access SDM cluster',
            'pegawai_management' => 'Manage employees',
            'dokter_management' => 'Manage doctors',
            'petugas_management' => 'Manage staff',
            'departemen_management' => 'Manage departments',
            'bidang_management' => 'Manage divisions',
            'jabatan_management' => 'Manage positions',
            'berkas_pegawai_management' => 'Manage employee documents',
            
            // Pegawai cluster permissions
            'pegawai_access' => 'Access Pegawai cluster',
            'absen_management' => 'Manage attendance',
            'cuti_management' => 'Manage leave requests',
            
            // ERM cluster permissions
            'erm_access' => 'Access ERM cluster',
            'pasien_management' => 'Manage patients',
            'registrasi_management' => 'Manage registrations',
            'rawat_jalan_management' => 'Manage outpatient care',
        ];

        // Create permissions if they don't exist
        foreach ($clusterPermissions as $permission => $description) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->command->info("✓ Permission created/updated: {$permission}");
        }

        // Create roles
        $roles = [
            'Super Admin' => [
                'description' => 'Has access to all system features',
                'permissions' => array_keys($clusterPermissions)
            ],
            'Administrator' => [
                'description' => 'System administrator with most privileges',
                'permissions' => [
                    'administrator_access',
                    'user_management',
                    'role_management',
                    'activity_log_view',
                    'website_management',
                    'website_identity_manage',
                    'blog_management',
                    'blog_category_management',
                    'blog_tag_management',
                ]
            ],
            'Manager' => [
                'description' => 'Department manager with access to employee management',
                'permissions' => [
                    'sdm_access',
                    'pegawai_management',
                    'departemen_management',
                    'bidang_management',
                    'jabatan_management',
                    'absen_management',
                    'cuti_management',
                ]
            ],
            'Doctor' => [
                'description' => 'Medical doctor with access to patient records',
                'permissions' => [
                    'erm_access',
                    'pasien_management',
                    'registrasi_management',
                    'rawat_jalan_management',
                ]
            ],
            'Staff' => [
                'description' => 'General staff with limited access',
                'permissions' => [
                    'pegawai_access',
                    'absen_management',
                ]
            ],
            'Content Manager' => [
                'description' => 'Manages website content and blogs',
                'permissions' => [
                    'website_management',
                    'blog_management',
                    'blog_category_management',
                    'blog_tag_management',
                ]
            ]
        ];

        foreach ($roles as $roleName => $roleData) {
            try {
                // Create role if it doesn't exist
                $role = Role::firstOrCreate(['name' => $roleName]);

                // Get permissions that actually exist
                $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
                
                // Only sync if permissions found
                if ($permissions->count() > 0) {
                    $role->syncPermissions($permissions);
                    $this->command->info("✓ Role created/updated: {$roleName} with " . count($permissions) . " permissions");
                } else {
                    $this->command->warn("⚠ No matching permissions found for role: {$roleName}");
                }
            } catch (\Exception $e) {
                $this->command->error("✗ Failed to create/update role {$roleName}: " . $e->getMessage());
            }
        }

        // Assign Super Admin role to first user if exists
        $firstUser = \App\Models\User::first();
        if ($firstUser && !$firstUser->hasRole('Super Admin')) {
            $firstUser->assignRole('Super Admin');
            $this->command->info("✓ Assigned Super Admin role to user: {$firstUser->name}");
        }

        $this->command->info('Filament cluster permissions and roles seeded successfully!');
    }
}