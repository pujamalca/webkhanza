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

        // Clear existing role-permission assignments to prevent conflicts
        \Illuminate\Support\Facades\DB::table('role_has_permissions')->delete();
        \Illuminate\Support\Facades\DB::table('model_has_roles')->delete();

        // Create permissions
        $permissions = [
            // Dashboard
            'dashboard_access',
            
            // Administrator Cluster
            'administrator_access',
            
            // User Management Menu
            'users_view',
            'users_create',
            'users_edit',
            'users_delete',
            'users_reset_device',
            
            // Role Management Menu
            'roles_view',
            'roles_create',
            'roles_edit',
            'roles_delete',
            
            // ERM Cluster
            'erm_access',
            
            // Pasien Menu
            'pasien_view',
            'pasien_create',
            'pasien_edit',
            'pasien_delete',
            'pasien_view_details',
            
            // Registrasi Menu
            'registrasi_view',
            'registrasi_create',
            'registrasi_edit',
            'registrasi_delete',
            'registrasi_view_details',
            
            // Rawat Jalan Menu
            'rawat_jalan_view',
            'rawat_jalan_create',
            'rawat_jalan_edit',
            'rawat_jalan_delete',
            'rawat_jalan_view_details',

            // SDM Cluster
            'sdm_access',
            
            // Pegawai Menu
            'pegawai_view',
            'pegawai_create',
            'pegawai_edit',
            'pegawai_delete',
            'pegawai_view_details',
            
            // Dokter Menu
            'dokter_view',
            'dokter_create',
            'dokter_edit',
            'dokter_delete',
            'dokter_view_details',
            
            // Petugas Menu
            'petugas_view',
            'petugas_create',
            'petugas_edit',
            'petugas_delete',
            'petugas_view_details',
            
            // Berkas Pegawai Menu
            'berkas_pegawai_view',
            'berkas_pegawai_create',
            'berkas_pegawai_edit',
            'berkas_pegawai_delete',
            'berkas_pegawai_download',
            'berkas_pegawai_view_details',
            
            // Master Data Creation (for createOptionForm in dropdowns)
            'master_bidang_create',
            'master_departemen_create', 
            'master_jabatan_create',
            'master_spesialis_create',
            
            // Pegawai Cluster - Absensi
            'view_own_absent',
            'view_all_absent',
            'create_absent',
            'edit_absent',
            'delete_absent',
            
            // Pegawai Cluster - Cuti
            'view_own_cuti',
            'view_all_cuti',
            'create_cuti',
            'approve_cuti',
            'edit_cuti',
            'delete_cuti',
            
            // System Management
            'system_settings_access',
            'system_logs_access',
            'activity_logs_view',
            
            // Multi Device Login
            'multi_device_login',
            
            // Website Management
            'website_management_access',
            'view_any_website_identity',
            'view_website_identity',
            'create_website_identity',
            'update_website_identity',
            
            // Blog Management
            'blog_management_access',
            'view_any_blog',
            'view_blog',
            'create_blog',
            'update_blog',
            'delete_blog',
            'publish_blog',
            
            // Blog Category Management
            'view_any_blog_category',
            'view_blog_category',
            'create_blog_category',
            'update_blog_category',
            'delete_blog_category',
            
            // Blog Tag Management
            'view_any_blog_tag',
            'view_blog_tag',
            'create_blog_tag',
            'update_blog_tag',
            'delete_blog_tag',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create roles and assign permissions safely
        $this->createRolesWithPermissions();

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Created roles: Super Admin, Admin, HRD Manager, Staff HRD, Supervisor, Manager, User');
        $this->command->info('Created ' . count($permissions) . ' permissions (including activity logs)');
    }

    private function createRolesWithPermissions(): void
    {
        // First, create all roles to ensure they exist
        $roleNames = ['Super Admin', 'Admin', 'HRD Manager', 'Staff HRD', 'Supervisor', 'Manager', 'User'];
        
        foreach ($roleNames as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }

        // Clear cache after role creation
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define role permissions mapping
        $rolePermissions = [
            'Super Admin' => Permission::all()->pluck('name')->toArray(),
            'Admin' => [
                'dashboard_access', 'administrator_access',
                'users_view', 'users_create', 'users_edit', 'users_reset_device',
                'roles_view', 'system_settings_access', 'system_logs_access', 'activity_logs_view', 'multi_device_login',
                'erm_access', 'pasien_view', 'pasien_create', 'pasien_edit', 'pasien_delete', 'pasien_view_details',
                'registrasi_view', 'registrasi_create', 'registrasi_edit', 'registrasi_delete', 'registrasi_view_details',
                'rawat_jalan_view', 'rawat_jalan_create', 'rawat_jalan_edit', 'rawat_jalan_delete', 'rawat_jalan_view_details',
                'sdm_access', 'pegawai_view', 'pegawai_create', 'pegawai_edit', 'pegawai_delete', 'pegawai_view_details',
                'dokter_view', 'dokter_create', 'dokter_edit', 'dokter_delete', 'dokter_view_details',
                'petugas_view', 'petugas_create', 'petugas_edit', 'petugas_delete', 'petugas_view_details',
                'berkas_pegawai_view', 'berkas_pegawai_create', 'berkas_pegawai_edit', 'berkas_pegawai_delete', 'berkas_pegawai_download', 'berkas_pegawai_view_details',
                'master_bidang_create', 'master_departemen_create', 'master_jabatan_create', 'master_spesialis_create',
                'view_all_absent', 'create_absent', 'edit_absent', 'delete_absent',
                'view_all_cuti', 'create_cuti', 'approve_cuti', 'edit_cuti', 'delete_cuti',
                // Website & Blog Management
                'website_management_access', 'view_any_website_identity', 'view_website_identity', 'create_website_identity', 'update_website_identity',
                'blog_management_access', 'view_any_blog', 'view_blog', 'create_blog', 'update_blog', 'delete_blog', 'publish_blog',
                'view_any_blog_category', 'view_blog_category', 'create_blog_category', 'update_blog_category', 'delete_blog_category',
                'view_any_blog_tag', 'view_blog_tag', 'create_blog_tag', 'update_blog_tag', 'delete_blog_tag',
            ],
            'HRD Manager' => [
                'dashboard_access', 'sdm_access',
                'pegawai_view', 'pegawai_create', 'pegawai_edit', 'pegawai_delete', 'pegawai_view_details',
                'dokter_view', 'dokter_create', 'dokter_edit', 'dokter_delete', 'dokter_view_details',
                'petugas_view', 'petugas_create', 'petugas_edit', 'petugas_delete', 'petugas_view_details',
                'berkas_pegawai_view', 'berkas_pegawai_create', 'berkas_pegawai_edit', 'berkas_pegawai_delete', 'berkas_pegawai_download', 'berkas_pegawai_view_details',
                'master_bidang_create', 'master_departemen_create', 'master_jabatan_create', 'master_spesialis_create',
                'view_all_absent', 'create_absent', 'edit_absent', 'delete_absent',
                'view_all_cuti', 'create_cuti', 'approve_cuti', 'edit_cuti', 'delete_cuti',
            ],
            'Staff HRD' => [
                'dashboard_access', 'sdm_access',
                'pegawai_view', 'pegawai_edit', 'pegawai_view_details',
                'dokter_view', 'dokter_edit', 'dokter_view_details',
                'petugas_view', 'petugas_edit', 'petugas_view_details',
                'berkas_pegawai_view', 'berkas_pegawai_create', 'berkas_pegawai_edit', 'berkas_pegawai_download', 'berkas_pegawai_view_details',
                'view_all_absent', 'create_absent', 'edit_absent',
                'view_all_cuti', 'create_cuti', 'edit_cuti',
            ],
            'Supervisor' => [
                'dashboard_access', 'sdm_access',
                'pegawai_view', 'pegawai_view_details',
                'dokter_view', 'dokter_view_details',
                'petugas_view', 'petugas_view_details',
                'berkas_pegawai_view', 'berkas_pegawai_view_details',
                'view_all_absent', 'view_all_cuti',
            ],
            'Manager' => [
                'dashboard_access', 'administrator_access',
                'users_view', 'users_edit', 'users_reset_device', 'activity_logs_view',
                'erm_access', 'pasien_view', 'pasien_view_details',
                'registrasi_view', 'registrasi_view_details',
                'rawat_jalan_view', 'rawat_jalan_view_details',
                'sdm_access', 'pegawai_view', 'pegawai_view_details',
                'dokter_view', 'dokter_view_details',
                'petugas_view', 'petugas_view_details',
                'berkas_pegawai_view', 'berkas_pegawai_view_details',
                'view_all_absent', 'edit_absent',
                'view_all_cuti', 'approve_cuti', 'edit_cuti',
            ],
            'User' => [
                'dashboard_access',
                'view_own_absent', 'create_absent',
                'view_own_cuti', 'create_cuti',
            ],
        ];

        // Assign permissions to existing roles
        foreach ($rolePermissions as $roleName => $permissions) {
            try {
                // Get existing role (we know it exists from above)
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                
                if (!$role) {
                    $this->command->error("Role {$roleName} not found!");
                    continue;
                }

                // Clear existing permissions for this role
                \Illuminate\Support\Facades\DB::table('role_has_permissions')
                    ->where('role_id', $role->id)
                    ->delete();

                // Filter permissions to only include existing ones
                $validPermissions = Permission::whereIn('name', $permissions)->get();
                
                if ($validPermissions->isNotEmpty()) {
                    // Use raw DB insert to avoid any Laravel model issues
                    $insertData = [];
                    foreach ($validPermissions as $permission) {
                        $insertData[] = [
                            'role_id' => $role->id,
                            'permission_id' => $permission->id
                        ];
                    }
                    
                    if (!empty($insertData)) {
                        \Illuminate\Support\Facades\DB::table('role_has_permissions')->insert($insertData);
                    }
                }

            } catch (\Exception $e) {
                $this->command->error("Error assigning permissions to role {$roleName}: " . $e->getMessage());
            }
        }
        
        // Clear cache after all assignments
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
