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
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                // Change subject_id and causer_id to varchar to accommodate string IDs
                if (Schema::hasColumn('activity_log', 'subject_id')) {
                    $table->string('subject_id', 255)->nullable()->change();
                }
                if (Schema::hasColumn('activity_log', 'causer_id')) {
                    $table->string('causer_id', 255)->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('activity_log')) {
            Schema::table('activity_log', function (Blueprint $table) {
                // Revert back to bigint
                if (Schema::hasColumn('activity_log', 'subject_id')) {
                    $table->unsignedBigInteger('subject_id')->nullable()->change();
                }
                if (Schema::hasColumn('activity_log', 'causer_id')) {
                    $table->unsignedBigInteger('causer_id')->nullable()->change();
                }
            });
        }
    }
};
