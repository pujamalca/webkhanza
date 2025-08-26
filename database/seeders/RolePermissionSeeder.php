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
            
            // SDM - Pegawai Management
            'pegawai_create',
            'pegawai_read',
            'pegawai_update',
            'pegawai_delete',
            
            // SDM - Dokter Management
            'dokter_create',
            'dokter_read',
            'dokter_update',
            'dokter_delete',
            
            // SDM - Petugas Management
            'petugas_create',
            'petugas_read',
            'petugas_update',
            'petugas_delete',
            
            // SDM - Berkas Pegawai Management
            'berkas_pegawai_create',
            'berkas_pegawai_read',
            'berkas_pegawai_update',
            'berkas_pegawai_delete',
            'berkas_pegawai_download',
            
            // Master Data (for createOptionForm)
            'bidang_create',
            'departemen_create',
            'jabatan_create',
            'spesialis_create',
            
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
            'pegawai_create', 'pegawai_read', 'pegawai_update', 'pegawai_delete',
            'dokter_create', 'dokter_read', 'dokter_update', 'dokter_delete',
            'petugas_create', 'petugas_read', 'petugas_update', 'petugas_delete',
            'berkas_pegawai_create', 'berkas_pegawai_read', 'berkas_pegawai_update', 'berkas_pegawai_delete', 'berkas_pegawai_download',
            'bidang_create', 'departemen_create', 'jabatan_create', 'spesialis_create',
            'dashboard_access'
        ]);
        
        // HRD Manager - Full SDM access
        $hrdManager = Role::firstOrCreate(
            ['name' => 'HRD Manager'],
            ['guard_name' => 'web']
        );
        $hrdManager->givePermissionTo([
            'pegawai_create', 'pegawai_read', 'pegawai_update', 'pegawai_delete',
            'dokter_create', 'dokter_read', 'dokter_update', 'dokter_delete',
            'petugas_create', 'petugas_read', 'petugas_update', 'petugas_delete',
            'berkas_pegawai_create', 'berkas_pegawai_read', 'berkas_pegawai_update', 'berkas_pegawai_delete', 'berkas_pegawai_download',
            'bidang_create', 'departemen_create', 'jabatan_create', 'spesialis_create',
            'dashboard_access'
        ]);
        
        // Staff HRD - Read and basic operations on SDM
        $staffHRD = Role::firstOrCreate(
            ['name' => 'Staff HRD'],
            ['guard_name' => 'web']
        );
        $staffHRD->givePermissionTo([
            'pegawai_read', 'pegawai_update',
            'dokter_read', 'dokter_update',
            'petugas_read', 'petugas_update',
            'berkas_pegawai_create', 'berkas_pegawai_read', 'berkas_pegawai_update', 'berkas_pegawai_download',
            'dashboard_access'
        ]);
        
        // Manager - User management and read access to SDM
        $manager = Role::firstOrCreate(
            ['name' => 'Manager'],
            ['guard_name' => 'web']
        );
        $manager->givePermissionTo([
            'user_read', 'user_update', 'user_reset_device',
            'pegawai_read', 'dokter_read', 'petugas_read', 'berkas_pegawai_read',
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
        $this->command->info('Created roles: Super Admin, Admin, HRD Manager, Staff HRD, Manager, User');
        $this->command->info('Created ' . count($permissions) . ' permissions');
    }
}
