<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CatatanMedisPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permission for managing all medical notes (catatan medis)
        $permission = Permission::firstOrCreate([
            'name' => 'manage_all_medical_notes',
            'guard_name' => 'web'
        ]);

        $this->command->info('Permission "manage_all_medical_notes" created successfully.');
    }
}