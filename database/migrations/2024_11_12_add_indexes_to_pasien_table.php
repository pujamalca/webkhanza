<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pasien')) {
            // Get existing indexes using raw query
            $indexes = DB::select("SHOW INDEX FROM pasien");
            $indexNames = array_unique(array_column($indexes, 'Key_name'));

            Schema::table('pasien', function (Blueprint $table) use ($indexNames) {
                // Add indexes for fast searching
                if (!in_array('idx_pasien_no_rm', $indexNames)) {
                    $table->index('no_rkm_medis', 'idx_pasien_no_rm');
                }
                if (!in_array('idx_pasien_nama', $indexNames)) {
                    $table->index('nm_pasien', 'idx_pasien_nama');
                }
                if (!in_array('idx_pasien_nik', $indexNames)) {
                    $table->index('no_ktp', 'idx_pasien_nik');
                }
                if (!in_array('idx_pasien_bpjs', $indexNames)) {
                    $table->index('no_peserta', 'idx_pasien_bpjs');
                }

                // Composite index for common searches
                if (!in_array('idx_pasien_nama_rm', $indexNames)) {
                    $table->index(['nm_pasien', 'no_rkm_medis'], 'idx_pasien_nama_rm');
                }
                if (!in_array('idx_pasien_rm_nama', $indexNames)) {
                    $table->index(['no_rkm_medis', 'nm_pasien'], 'idx_pasien_rm_nama');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pasien')) {
            // Get existing indexes using raw query
            $indexes = DB::select("SHOW INDEX FROM pasien");
            $indexNames = array_unique(array_column($indexes, 'Key_name'));

            Schema::table('pasien', function (Blueprint $table) use ($indexNames) {
                if (in_array('idx_pasien_no_rm', $indexNames)) {
                    $table->dropIndex('idx_pasien_no_rm');
                }
                if (in_array('idx_pasien_nama', $indexNames)) {
                    $table->dropIndex('idx_pasien_nama');
                }
                if (in_array('idx_pasien_nik', $indexNames)) {
                    $table->dropIndex('idx_pasien_nik');
                }
                if (in_array('idx_pasien_bpjs', $indexNames)) {
                    $table->dropIndex('idx_pasien_bpjs');
                }
                if (in_array('idx_pasien_nama_rm', $indexNames)) {
                    $table->dropIndex('idx_pasien_nama_rm');
                }
                if (in_array('idx_pasien_rm_nama', $indexNames)) {
                    $table->dropIndex('idx_pasien_rm_nama');
                }
            });
        }
    }
};