<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SoapieRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create additional SOAPIE-specific permissions (using existing ones where possible)
        $newPermissions = [
            // SOAPIE Template permissions (new)
            'view_soapie_templates',
            'create_soapie_templates',
            'edit_own_soapie_templates',
            'edit_all_soapie_templates',
            'delete_soapie_templates',
            'create_public_soapie_templates',

            // TTV (Vital Signs) permissions (new)
            'fill_ttv_from_previous',
        ];

        foreach ($newPermissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Update existing roles with new permissions
        $this->updateExistingRoles();
    }

    private function updateExistingRoles(): void
    {
        // Get existing roles
        $adminRole = Role::where('name', 'Admin')->first();
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $doctorRole = Role::where('name', 'Dokter')->first();

        // Give template and TTV permissions to existing admin roles
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'view_soapie_templates',
                'create_soapie_templates',
                'edit_all_soapie_templates',
                'delete_soapie_templates',
                'create_public_soapie_templates',
                'fill_ttv_from_previous',
            ]);
        }

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(Permission::all());
        }

        // Give permissions to existing Dokter role
        if ($doctorRole) {
            $doctorRole->givePermissionTo([
                'view_soapie_templates',
                'create_soapie_templates',
                'edit_own_soapie_templates',
                'delete_soapie_templates',
                'fill_ttv_from_previous',
            ]);
        }
    }
}