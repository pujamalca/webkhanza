<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Note: RolePermissionSeeder may have issues with permission tables
            // Run individually if needed: php artisan db:seed --class=RolePermissionSeeder
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            CoreDataSeeder::class, // Essential data seperti WebsiteIdentity - VERIFIED WORKING
            BlogCategorySeeder::class, // VERIFIED WORKING
            BlogSeeder::class,
        ]);
    }
}
