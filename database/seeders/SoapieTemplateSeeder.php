<?php

namespace Database\Seeders;

use App\Models\SoapieTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoapieTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'nama_template' => 'Pemeriksaan Umum - Kontrol Rutin',
                'subjective' => 'Pasien datang untuk kontrol rutin, merasa sehat, tidak ada keluhan khusus. Nafsu makan baik, tidur cukup, BAB/BAK normal.',
                'objective' => 'Keadaan umum baik, kesadaran compos mentis. TD: 120/80 mmHg, Nadi: 80x/menit, RR: 20x/menit, Suhu: 36.5°C. Pemeriksaan fisik dalam batas normal.',
                'assessment' => 'Pasien dalam kondisi sehat, pemeriksaan rutin normal.',
                'plan' => 'Edukasi pola hidup sehat, kontrol rutin 6 bulan lagi jika tidak ada keluhan.',
                'intervention' => 'Pemberian edukasi diet seimbang dan olahraga teratur.',
                'evaluation' => 'Pasien memahami edukasi yang diberikan dan akan menjaga pola hidup sehat.',
                'nip' => 'admin',
                'is_public' => true,
                'kategori' => 'Umum',
                'keterangan' => 'Template untuk pemeriksaan kontrol rutin pasien sehat'
            ],
            [
                'nama_template' => 'Hipertensi - Kontrol',
                'subjective' => 'Pasien hipertensi kontrol rutin, sudah minum obat teratur. Keluhan pusing sesekali terutama pagi hari.',
                'objective' => 'Keadaan umum baik, TD: 140/90 mmHg, Nadi: 88x/menit. Pemeriksaan jantung dan paru dalam batas normal.',
                'assessment' => 'Hipertensi stage 1, terkontrol dengan medikasi.',
                'plan' => 'Lanjutkan terapi antihipertensi, diet rendah garam, kontrol 1 bulan.',
                'intervention' => 'Edukasi diet DASH, monitoring tekanan darah harian.',
                'evaluation' => 'Tekanan darah terkontrol, pasien patuh terapi.',
                'nip' => 'admin',
                'is_public' => true,
                'kategori' => 'Kardiovaskular',
                'keterangan' => 'Template untuk pasien hipertensi kontrol rutin'
            ],
            [
                'nama_template' => 'Diabetes Mellitus - Kontrol',
                'subjective' => 'Pasien DM tipe 2 kontrol rutin, minum obat teratur. Keluhan sering haus dan buang air kecil, terutama malam hari.',
                'objective' => 'Keadaan umum baik, BB: 70kg, TB: 165cm, BMI: 25.7. GDS: 180 mg/dL. Pemeriksaan kaki tidak ada luka.',
                'assessment' => 'Diabetes Mellitus tipe 2, kontrol glikemik belum optimal.',
                'plan' => 'Evaluasi terapi antidiabetik, periksa HbA1c, diet diabetik, kontrol 2 minggu.',
                'intervention' => 'Edukasi diet diabetik, senam kaki diabetik, monitoring gula darah.',
                'evaluation' => 'Pasien memahami pentingnya kontrol gula darah dan diet.',
                'nip' => 'admin',
                'is_public' => true,
                'kategori' => 'Endokrin',
                'keterangan' => 'Template untuk pasien diabetes mellitus kontrol'
            ],
            [
                'nama_template' => 'ISPA - Dewasa',
                'subjective' => 'Pasien mengeluh batuk berdahak sejak 3 hari, pilek, demam ringan, nyeri tenggorokan.',
                'objective' => 'Keadaan umum baik, Suhu: 37.8°C, Nadi: 90x/menit. Pemeriksaan tenggorok hiperemis, tidak ada eksudat. Paru vesikuler, tidak ada ronki.',
                'assessment' => 'Infeksi Saluran Pernapasan Atas (Common Cold).',
                'plan' => 'Simtomatik: paracetamol 3x500mg, antitusif, banyak minum air putih, istirahat cukup.',
                'intervention' => 'Edukasi menjaga kebersihan, mencuci tangan, isolasi mandiri.',
                'evaluation' => 'Gejala membaik dalam 3-5 hari, kembali jika gejala memberat.',
                'nip' => 'admin',
                'is_public' => true,
                'kategori' => 'Respirasi',
                'keterangan' => 'Template untuk ISPA pada dewasa'
            ],
            [
                'nama_template' => 'Gastritis Akut',
                'subjective' => 'Pasien mengeluh nyeri ulu hati sejak 2 hari, mual, kembung, nyeri bertambah saat lapar.',
                'objective' => 'Keadaan umum baik, TD: 110/70 mmHg. Abdomen supel, nyeri tekan epigastrium (+), bising usus normal.',
                'assessment' => 'Gastritis akut.',
                'plan' => 'PPI 1x40mg sebelum makan, antasid 3x1, diet lunak, hindari makanan pedas dan asam.',
                'intervention' => 'Edukasi pola makan teratur, hindari stress, tidak merokok.',
                'evaluation' => 'Nyeri berkurang, nafsu makan membaik, kontrol jika keluhan berlanjut.',
                'nip' => 'admin',
                'is_public' => true,
                'kategori' => 'Gastroenterologi',
                'keterangan' => 'Template untuk gastritis akut'
            ]
        ];

        foreach ($templates as $template) {
            SoapieTemplate::create($template);
        }
    }
}
