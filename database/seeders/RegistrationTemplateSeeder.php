<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegistrationTemplate;
use App\Models\Dokter;
use App\Models\Poliklinik;
use App\Models\Penjab;

class RegistrationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Sample template for DPP - adjust with actual data
        $dokterUmum = Dokter::first(); // Get first available doctor
        $poliUmum = Poliklinik::first(); // Get first available poli
        $penjabUmum = Penjab::where('png_jawab', 'LIKE', '%UMUM%')->first() 
                     ?? Penjab::first(); // Find UMUM or get first

        if ($dokterUmum && $poliUmum && $penjabUmum) {
            RegistrationTemplate::create([
                'name' => 'Template DPP Umum',
                'kd_dokter' => $dokterUmum->kd_dokter,
                'kd_poli' => $poliUmum->kd_poli,
                'kd_pj' => $penjabUmum->kd_pj,
                'biaya_reg' => 10000,
                'status_lanjut' => 'Ralan',
                'stts_daftar' => 'Lama',
                'is_active' => true,
                'description' => 'Template default untuk registrasi DPP dengan dokter umum'
            ]);
        }

        // Sample BPJS template if available
        $penjabBpjs = Penjab::where('png_jawab', 'LIKE', '%BPJS%')->first();
        
        if ($dokterUmum && $poliUmum && $penjabBpjs) {
            RegistrationTemplate::create([
                'name' => 'Template DPP BPJS',
                'kd_dokter' => $dokterUmum->kd_dokter,
                'kd_poli' => $poliUmum->kd_poli,
                'kd_pj' => $penjabBpjs->kd_pj,
                'biaya_reg' => 0,
                'status_lanjut' => 'Ralan',
                'stts_daftar' => 'Lama',
                'is_active' => true,
                'description' => 'Template untuk pasien BPJS dengan biaya gratis'
            ]);
        }
    }
}