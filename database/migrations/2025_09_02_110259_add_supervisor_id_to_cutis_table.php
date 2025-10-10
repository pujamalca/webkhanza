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
            // Add supervisor_id column
            if (!Schema::hasColumn('cutis', 'supervisor_id')) {
                $table->string('supervisor_id', 20)->nullable()->after('employee_id')
                      ->comment('Atasan yang harus menyetujui cuti');
                $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
            }

            // Add supervisor status fields
            if (!Schema::hasColumn('cutis', 'supervisor_status')) {
                $table->enum('supervisor_status', ['pending', 'approved', 'rejected'])
                      ->default('pending')
                      ->after('supervisor_id')
                      ->comment('Status persetujuan supervisor');
            }
            if (!Schema::hasColumn('cutis', 'supervisor_approved_at')) {
                $table->timestamp('supervisor_approved_at')->nullable()
                      ->after('supervisor_status')
                      ->comment('Waktu persetujuan supervisor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            if (Schema::hasColumn('cutis', 'supervisor_id')) {
                $table->dropForeign(['supervisor_id']);
            }

            $columns = ['supervisor_id', 'supervisor_status', 'supervisor_approved_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('cutis', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
