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
            'dashboard_access',
            'administrator_access',
            'users_view', 'users_create', 'users_edit', 'users_reset_device',
            'roles_view',
            'system_settings_access', 'system_logs_access', 'activity_logs_view',
            'erm_access',
            'pasien_view', 'pasien_create', 'pasien_edit', 'pasien_delete', 'pasien_view_details',
            'registrasi_view', 'registrasi_create', 'registrasi_edit', 'registrasi_delete', 'registrasi_view_details',
            'rawat_jalan_view', 'rawat_jalan_create', 'rawat_jalan_edit', 'rawat_jalan_delete', 'rawat_jalan_view_details',
            'sdm_access',
            'pegawai_view', 'pegawai_create', 'pegawai_edit', 'pegawai_delete', 'pegawai_view_details',
            'dokter_view', 'dokter_create', 'dokter_edit', 'dokter_delete', 'dokter_view_details',
            'petugas_view', 'petugas_create', 'petugas_edit', 'petugas_delete', 'petugas_view_details',
            'berkas_pegawai_view', 'berkas_pegawai_create', 'berkas_pegawai_edit', 'berkas_pegawai_delete', 'berkas_pegawai_download', 'berkas_pegawai_view_details',
            'master_bidang_create', 'master_departemen_create', 'master_jabatan_create', 'master_spesialis_create',
        ]);
        
        // HRD Manager - Full SDM access only
        $hrdManager = Role::firstOrCreate(
            ['name' => 'HRD Manager'],
            ['guard_name' => 'web']
        );
        $hrdManager->givePermissionTo([
            'dashboard_access',
            'sdm_access',
            'pegawai_view', 'pegawai_create', 'pegawai_edit', 'pegawai_delete', 'pegawai_view_details',
            'dokter_view', 'dokter_create', 'dokter_edit', 'dokter_delete', 'dokter_view_details',
            'petugas_view', 'petugas_create', 'petugas_edit', 'petugas_delete', 'petugas_view_details',
            'berkas_pegawai_view', 'berkas_pegawai_create', 'berkas_pegawai_edit', 'berkas_pegawai_delete', 'berkas_pegawai_download', 'berkas_pegawai_view_details',
            'master_bidang_create', 'master_departemen_create', 'master_jabatan_create', 'master_spesialis_create',
        ]);
        
        // Staff HRD - Limited SDM operations
        $staffHRD = Role::firstOrCreate(
            ['name' => 'Staff HRD'],
            ['guard_name' => 'web']
        );
        $staffHRD->givePermissionTo([
            'dashboard_access',
            'sdm_access',
            'pegawai_view', 'pegawai_edit', 'pegawai_view_details',
            'dokter_view', 'dokter_edit', 'dokter_view_details',
            'petugas_view', 'petugas_edit', 'petugas_view_details',
            'berkas_pegawai_view', 'berkas_pegawai_create', 'berkas_pegawai_edit', 'berkas_pegawai_download', 'berkas_pegawai_view_details',
        ]);
        
        // Supervisor - Read access to specific menus
        $supervisor = Role::firstOrCreate(
            ['name' => 'Supervisor'],
            ['guard_name' => 'web']
        );
        $supervisor->givePermissionTo([
            'dashboard_access',
            'sdm_access',
            'pegawai_view', 'pegawai_view_details',
            'dokter_view', 'dokter_view_details',
            'petugas_view', 'petugas_view_details',
            'berkas_pegawai_view', 'berkas_pegawai_view_details',
        ]);
        
        // Manager - User management and read access to SDM
        $manager = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['guard_name' => 'web']
        );
        $manager->givePermissionTo([
            'dashboard_access',
            'administrator_access',
            'users_view', 'users_edit', 'users_reset_device',
            'activity_logs_view',
            'erm_access',
            'pasien_view', 'pasien_view_details',
            'registrasi_view', 'registrasi_view_details',
            'rawat_jalan_view', 'rawat_jalan_view_details',
            'sdm_access',
            'pegawai_view', 'pegawai_view_details',
            'dokter_view', 'dokter_view_details',
            'petugas_view', 'petugas_view_details',
            'berkas_pegawai_view', 'berkas_pegawai_view_details',
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
        $this->command->info('Created roles: Super Admin, Admin, HRD Manager, Staff HRD, Supervisor, Manager, User');
        $this->command->info('Created ' . count($permissions) . ' permissions (including activity logs)');
    }
}
