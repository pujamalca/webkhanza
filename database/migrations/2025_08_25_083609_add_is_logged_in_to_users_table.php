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
            if (!Schema::hasColumn('users', 'is_logged_in')) {
                $table->boolean('is_logged_in')->default(false);
            }
            if (!Schema::hasColumn('users', 'logged_in_at')) {
                $table->timestamp('logged_in_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_logged_in')) {
                $table->dropColumn('is_logged_in');
            }
            if (Schema::hasColumn('users', 'logged_in_at')) {
                $table->dropColumn('logged_in_at');
            }
        });
    }
};
