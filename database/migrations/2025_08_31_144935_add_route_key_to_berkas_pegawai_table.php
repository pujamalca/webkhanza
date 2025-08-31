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
        Schema::table('berkas_pegawai', function (Blueprint $table) {
            $table->string('route_key', 100)->nullable()->after('berkas');
            $table->index('route_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berkas_pegawai', function (Blueprint $table) {
            $table->dropIndex(['route_key']);
            $table->dropColumn('route_key');
        });
    }
};
