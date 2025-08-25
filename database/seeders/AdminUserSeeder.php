<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'is_logged_in' => false,
                'device_token' => null,
                'device_info' => null,
                'logged_in_at' => null,
                'last_login_at' => null,
                'last_login_ip' => null,
            ]
        );

        // Assign Super Admin role
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                $admin->assignRole($superAdminRole);
                $this->command->info('Admin user assigned Super Admin role');
            }
        }

        $this->command->info('Admin user created/updated: admin@gmail.com / admin');
    }
}
