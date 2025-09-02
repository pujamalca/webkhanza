<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PegawaiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions for Absent (Absensi)
        $absentPermissions = [
            'view_own_absent' => 'Lihat absensi sendiri',
            'view_all_absent' => 'Lihat semua absensi',
            'create_absent' => 'Buat absensi',
            'edit_absent' => 'Edit absensi',
            'delete_absent' => 'Hapus absensi',
        ];

        // Create permissions for Cuti
        $cutiPermissions = [
            'view_own_cuti' => 'Lihat cuti sendiri',
            'view_all_cuti' => 'Lihat semua cuti',
            'create_cuti' => 'Buat pengajuan cuti',
            'approve_cuti' => 'Approve/reject cuti',
            'edit_cuti' => 'Edit cuti',
            'delete_cuti' => 'Hapus cuti',
        ];

        // Create all permissions
        $allPermissions = array_merge($absentPermissions, $cutiPermissions);

        foreach ($allPermissions as $permission => $description) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create or get roles with proper guard
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $hrRole = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Assign permissions to Admin role (full access)
        $adminRole->syncPermissions(array_keys($allPermissions));

        // Assign permissions to HR role (full access to employee data)
        $hrRole->syncPermissions([
            'view_all_absent',
            'view_all_cuti',
            'create_absent',
            'edit_absent', 
            'delete_absent',
            'create_cuti',
            'approve_cuti',
            'edit_cuti',
            'delete_cuti',
        ]);

        // Assign permissions to Manager role (view all, approve cuti)
        $managerRole->syncPermissions([
            'view_all_absent',
            'view_all_cuti',
            'approve_cuti',
            'edit_cuti',
        ]);

        // Assign permissions to Employee role (own data only)
        $employeeRole->syncPermissions([
            'view_own_absent',
            'create_absent',
            'view_own_cuti',
            'create_cuti',
        ]);

        $this->command->info('Pegawai permissions and role assignments created successfully!');
    }
}