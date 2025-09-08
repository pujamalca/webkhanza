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
        Schema::create('registration_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Quick Registration Template');
            $table->string('kd_dokter'); // Default dokter
            $table->string('kd_poli'); // Default poliklinik  
            $table->string('kd_pj'); // Default cara bayar
            $table->decimal('biaya_reg', 10, 2)->default(0); // Default biaya registrasi
            $table->enum('status_lanjut', ['Ralan', 'Ranap'])->default('Ralan');
            $table->enum('stts_daftar', ['Baru', 'Lama'])->default('Lama');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign key constraints - without cascade to avoid issues
            $table->index('kd_dokter');
            $table->index('kd_poli'); 
            $table->index('kd_pj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_templates');
    }
};
