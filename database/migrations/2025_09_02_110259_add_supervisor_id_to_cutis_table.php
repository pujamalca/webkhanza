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
            $table->string('supervisor_id', 20)->nullable()->after('employee_id')
                  ->comment('Atasan yang harus menyetujui cuti');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};
