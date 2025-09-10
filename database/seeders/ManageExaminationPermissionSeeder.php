<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ManageExaminationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permission for managing all examinations
        $permission = Permission::firstOrCreate([
            'name' => 'manage_all_examinations',
            'guard_name' => 'web'
        ]);

        $this->command->info('Permission "manage_all_examinations" created successfully.');
    }
}