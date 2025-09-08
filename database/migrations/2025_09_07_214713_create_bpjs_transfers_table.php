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
        Schema::create('bpjs_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pasien'); // Required field
            $table->integer('jumlah_keluarga')->default(1); // Required field - jumlah anggota keluarga
            $table->string('no_peserta_lama')->nullable(); // No BPJS lama
            $table->string('nik')->nullable(); // NIK pasien
            $table->string('no_telepon', 15)->nullable(); // Nomor telepon
            $table->text('alamat')->nullable(); // Alamat lengkap
            $table->date('tanggal_rencana_pindah')->nullable(); // Tanggal rencana pindah
            $table->string('foto_bukti_mjkn')->nullable(); // Path foto bukti MJKn
            $table->string('foto_pasien')->nullable(); // Path foto pasien
            $table->boolean('is_edukasi_completed')->default(false); // Status edukasi umum
            $table->unsignedBigInteger('edukasi_completed_by')->nullable(); // User yang menyelesaikan edukasi
            $table->timestamp('edukasi_completed_at')->nullable(); // Waktu edukasi diselesaikan
            $table->unsignedBigInteger('created_by'); // User yang input
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            
            // Indexes
            $table->index(['nama_pasien']);
            $table->index(['nik']);
            $table->index(['no_peserta_lama']);
            $table->index(['created_by']);
            $table->index(['edukasi_completed_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpjs_transfers');
    }
};
