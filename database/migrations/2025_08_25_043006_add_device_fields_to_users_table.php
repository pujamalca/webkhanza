<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Device management fields
            if (!Schema::hasColumn('users', 'device_token')) {
                $table->string('device_token', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'device_info')) {
                $table->string('device_info', 500)->nullable();
            }

            // Login tracking fields
            if (!Schema::hasColumn('users', 'is_logged_in')) {
                $table->boolean('is_logged_in')->default(false);
            }
            if (!Schema::hasColumn('users', 'logged_in_at')) {
                $table->timestamp('logged_in_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable();
            }

            // Avatar field
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'device_token',
                'device_info',
                'is_logged_in',
                'logged_in_at',
                'last_login_at',
                'last_login_ip',
                'avatar_url'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
