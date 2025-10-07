<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RawatJalanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar permission untuk Rawat Jalan
        $permissions = [
            'rawat_jalan_pemeriksaan_access' => 'Akses Tab Pemeriksaan Ralan',
            'rawat_jalan_input_tindakan_access' => 'Akses Tab Input Tindakan',
            'rawat_jalan_diagnosa_access' => 'Akses Tab Diagnosa',
            'rawat_jalan_catatan_access' => 'Akses Tab Catatan Pasien',
            'rawat_jalan_resep_access' => 'Akses Tab Resep Obat',
            'rawat_jalan_labor_access' => 'Akses Tab Permintaan Labor',
            'rawat_jalan_resume_access' => 'Akses Tab Resume Pasien',
            'manage_all_medical_notes' => 'Manage All Medical Notes (Admin)',
            'manage_all_input_tindakan' => 'Manage All Input Tindakan (Admin)',
        ];

        // Buat semua permission
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ]);

            $this->command->info("Permission \"{$name}\" created successfully.");
        }

        // Berikan semua permission ke role Super Admin (jika ada)
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(array_keys($permissions));
            $this->command->info("All Rawat Jalan permissions assigned to Super Admin role.");
        }

        // Berikan semua permission ke role Admin (jika ada)
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
            $this->command->info("All Rawat Jalan permissions assigned to Admin role.");
        }

        // Berikan permission terbatas ke role Dokter (jika ada)
        $dokterRole = Role::where('name', 'Dokter')->first();
        if ($dokterRole) {
            $dokterRole->givePermissionTo([
                'rawat_jalan_pemeriksaan_access',
                'rawat_jalan_diagnosa_access',
                'rawat_jalan_catatan_access',
                'rawat_jalan_resep_access',
                'rawat_jalan_resume_access',
            ]);
            $this->command->info("Selected Rawat Jalan permissions assigned to Dokter role.");
        }

        // Berikan permission terbatas ke role Perawat (jika ada)
        $perawatRole = Role::where('name', 'Perawat')->first();
        if ($perawatRole) {
            $perawatRole->givePermissionTo([
                'rawat_jalan_pemeriksaan_access',
                'rawat_jalan_input_tindakan_access',
                'rawat_jalan_catatan_access',
            ]);
            $this->command->info("Selected Rawat Jalan permissions assigned to Perawat role.");
        }
    }
}
