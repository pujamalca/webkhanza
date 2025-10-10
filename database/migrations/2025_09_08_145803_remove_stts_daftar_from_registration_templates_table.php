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
        Schema::table('registration_templates', function (Blueprint $table) {
            if (Schema::hasColumn('registration_templates', 'stts_daftar')) {
                $table->dropColumn('stts_daftar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('registration_templates', 'stts_daftar')) {
                $table->enum('stts_daftar', ['Baru', 'Lama'])->default('Lama');
            }
        });
    }
};
