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
        Schema::create('referensi_mobilejkn_bpjs_erm', function (Blueprint $table) {
            $table->string('tanggal_periksa', 10);
            $table->time('jam_periksa');
            $table->string('no_rkm_medis', 15);
            $table->string('no_rawat', 17);
            $table->string('no_kartu', 25)->nullable();
            $table->string('kodepoli', 15)->nullable();
            $table->string('nama_poli')->nullable();
            $table->integer('nomor_referensi')->nullable();
            $table->integer('jenis_kunjungan')->nullable();
            $table->integer('taskid')->default(0);
            $table->datetime('taskid1')->nullable();
            $table->datetime('taskid2')->nullable();
            $table->datetime('taskid3')->nullable();
            $table->datetime('taskid4')->nullable();
            $table->datetime('taskid5')->nullable();
            $table->datetime('taskid6')->nullable();
            $table->datetime('taskid7')->nullable();
            $table->datetime('taskid99')->nullable();
            $table->enum('status_kirim', ['Belum', 'Sudah'])->default('Belum');
            $table->timestamps();

            $table->primary(['tanggal_periksa', 'jam_periksa', 'no_rkm_medis']);
            $table->index('no_rawat');
            $table->index('tanggal_periksa');
            $table->index('status_kirim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensi_mobilejkn_bpjs_erm');
    }
};
