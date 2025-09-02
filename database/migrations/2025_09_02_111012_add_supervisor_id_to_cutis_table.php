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
        Schema::table('cutis', function (Blueprint $table) {
            // supervisor_id already added in previous migration
            $table->enum('supervisor_status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('supervisor_id')
                  ->comment('Status persetujuan supervisor');
            $table->timestamp('supervisor_approved_at')->nullable()
                  ->after('supervisor_status')
                  ->comment('Waktu persetujuan supervisor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            // supervisor_id will be dropped by the previous migration
            $table->dropColumn(['supervisor_status', 'supervisor_approved_at']);
        });
    }
};
