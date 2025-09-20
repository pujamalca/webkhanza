<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class InputTindakanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permission for managing all input tindakan
        $permission = Permission::firstOrCreate([
            'name' => 'manage_all_input_tindakan',
            'guard_name' => 'web'
        ]);

        $this->command->info('Permission "manage_all_input_tindakan" created successfully.');
    }
}