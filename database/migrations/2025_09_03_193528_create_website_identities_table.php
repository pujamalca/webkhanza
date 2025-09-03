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
        Schema::create('website_identities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama website');
            $table->text('description')->comment('Deskripsi singkat website');
            $table->string('logo')->nullable()->comment('Path file logo website');
            $table->string('favicon')->nullable()->comment('Path file favicon website');
            $table->string('email')->comment('Email kontak');
            $table->string('phone')->comment('Nomor telepon');
            $table->text('address')->comment('Alamat lengkap');
            $table->string('tagline')->comment('Tagline atau motto website');
            $table->timestamps();
        });

        // Insert default data untuk singleton pattern
        \DB::table('website_identities')->insert([
            'name' => 'WebKhanza',
            'description' => 'Sistem Manajemen Pegawai dan Absensi',
            'email' => 'admin@webkhanza.local',
            'phone' => '021-12345678',
            'address' => 'Jalan Contoh No. 123, Jakarta, Indonesia',
            'tagline' => 'Sistem Terpadu untuk Manajemen Pegawai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_identities');
    }
};
