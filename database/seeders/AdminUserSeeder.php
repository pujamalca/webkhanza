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
        // Check if admin user already exists
        $admin = User::where('email', 'admin@gmail.com')->first();

        if (!$admin) {
            // Check if id column is auto-increment using raw query
            $columns = \DB::select("SHOW COLUMNS FROM users WHERE Field = 'id'");
            $isAutoIncrement = false;

            if (!empty($columns)) {
                $extra = $columns[0]->Extra ?? '';
                $isAutoIncrement = stripos($extra, 'auto_increment') !== false;
            }

            try {
                if (!$isAutoIncrement) {
                    // For KHANZA desktop database where id is varchar/manual
                    $admin = User::create([
                        'id' => '1', // Manual ID for varchar/string type
                        'name' => 'admin',
                        'username' => 'admin',
                        'email' => 'admin@gmail.com',
                        'password' => Hash::make('admin'),
                        'is_logged_in' => false,
                        'device_token' => null,
                        'device_info' => null,
                        'logged_in_at' => null,
                        'last_login_at' => null,
                        'last_login_ip' => null,
                    ]);
                    $this->command->info('✓ Admin user created with manual ID: admin@gmail.com / admin');
                } else {
                    // For fresh installation where id is auto-increment
                    $admin = User::create([
                        'name' => 'admin',
                        'username' => 'admin',
                        'email' => 'admin@gmail.com',
                        'password' => Hash::make('admin'),
                        'is_logged_in' => false,
                        'device_token' => null,
                        'device_info' => null,
                        'logged_in_at' => null,
                        'last_login_at' => null,
                        'last_login_ip' => null,
                    ]);
                    $this->command->info('✓ Admin user created with auto-increment ID: admin@gmail.com / admin');
                }
            } catch (\Exception $e) {
                $this->command->error('Failed to create admin user: ' . $e->getMessage());
                $this->command->warn('You may need to create admin user manually or check your users table structure.');
                return;
            }
        } else {
            // Update existing admin user
            $admin->update([
                'password' => Hash::make('admin'),
                'is_logged_in' => false,
                'device_token' => null,
                'device_info' => null,
            ]);
            $this->command->info('✓ Admin user updated: admin@gmail.com / admin');
        }

        // Refresh the user to ensure we have latest data
        $admin->refresh();

        // Assign Super Admin role
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                // Remove old role assignments if they exist (in case user ID was changed)
                \DB::table('model_has_roles')
                    ->where('model_type', 'App\Models\User')
                    ->where('model_id', '!=', $admin->id)
                    ->whereIn('model_id', ['admin', '1']) // Clean up old IDs
                    ->delete();

                // Sync role to ensure it's assigned correctly
                $admin->syncRoles([$superAdminRole]);
                $this->command->info('✓ Admin user assigned Super Admin role');
            }
        }
    }
}
