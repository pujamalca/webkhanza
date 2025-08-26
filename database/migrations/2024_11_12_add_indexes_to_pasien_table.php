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
        Schema::table('pasien', function (Blueprint $table) {
            // Add indexes for fast searching
            $table->index('no_rkm_medis', 'idx_pasien_no_rm');
            $table->index('nm_pasien', 'idx_pasien_nama');  
            $table->index('no_ktp', 'idx_pasien_nik');
            $table->index('no_peserta', 'idx_pasien_bpjs');
            
            // Composite index for common searches
            $table->index(['nm_pasien', 'no_rkm_medis'], 'idx_pasien_nama_rm');
            $table->index(['no_rkm_medis', 'nm_pasien'], 'idx_pasien_rm_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropIndex('idx_pasien_no_rm');
            $table->dropIndex('idx_pasien_nama');
            $table->dropIndex('idx_pasien_nik'); 
            $table->dropIndex('idx_pasien_bpjs');
            $table->dropIndex('idx_pasien_nama_rm');
            $table->dropIndex('idx_pasien_rm_nama');
        });
    }
};