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
        User::updateOrCreate(
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

        $this->command->info('Admin user created/updated: admin@gmail.com / admin');
    }
}
