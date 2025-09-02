<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SafeDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database SAFELY.
     * Hanya menyentuh tabel-tabel Laravel, tidak mengganggu data desktop app.
     */
    public function run(): void
    {
        // Disable foreign key checks for seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('🌱 Starting safe database seeding...');
        
        try {
            // Seed Laravel-specific tables only
            $this->call([
                RolePermissionSeeder::class,
                // Tambahkan seeder lain yang aman di sini jika diperlukan
            ]);

            $this->command->info('✅ Safe database seeding completed successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Error during seeding: ' . $e->getMessage());
            $this->command->error('💡 Desktop app data remains untouched.');
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}