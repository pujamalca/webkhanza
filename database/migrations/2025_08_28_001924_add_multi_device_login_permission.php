<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create multi_device_login permission if not exists
        if (Schema::hasTable('permissions')) {
            Permission::firstOrCreate([
                'name' => 'multi_device_login',
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove multi_device_login permission
        Permission::where('name', 'multi_device_login')->delete();
    }
};
