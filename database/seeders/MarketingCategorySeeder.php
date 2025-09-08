<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketingCategory;

class MarketingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Kategori untuk Data Pasien Marketing
            [
                'name' => 'Follow Up Pasien',
                'description' => 'Follow up kondisi pasien setelah pemeriksaan',
                'category_type' => 'patient_marketing',
                'is_active' => true,
            ],
            [
                'name' => 'Survey Kepuasan',
                'description' => 'Survey tingkat kepuasan terhadap pelayanan',
                'category_type' => 'patient_marketing',
                'is_active' => true,
            ],
            [
                'name' => 'Reminder Kontrol',
                'description' => 'Mengingatkan pasien untuk kontrol rutin',
                'category_type' => 'patient_marketing',
                'is_active' => true,
            ],
            [
                'name' => 'Promosi Layanan',
                'description' => 'Memberikan informasi promosi layanan kesehatan',
                'category_type' => 'patient_marketing',
                'is_active' => true,
            ],
            [
                'name' => 'Feedback Complaint',
                'description' => 'Menangani feedback dan keluhan pasien',
                'category_type' => 'patient_marketing',
                'is_active' => true,
            ],

            // Kategori untuk Pindah BPJS
            [
                'name' => 'Verifikasi Dokumen',
                'description' => 'Memverifikasi kelengkapan dokumen untuk pindah BPJS',
                'category_type' => 'bpjs_transfer',
                'is_active' => true,
            ],
            [
                'name' => 'Koordinasi Faskes Lama',
                'description' => 'Koordinasi dengan faskes lama untuk pemindahan',
                'category_type' => 'bpjs_transfer',
                'is_active' => true,
            ],
            [
                'name' => 'Update Data MJKn',
                'description' => 'Memastikan data MJKn sudah terupdate',
                'category_type' => 'bpjs_transfer',
                'is_active' => true,
            ],
            [
                'name' => 'Konfirmasi Perpindahan',
                'description' => 'Konfirmasi bahwa perpindahan BPJS telah berhasil',
                'category_type' => 'bpjs_transfer',
                'is_active' => true,
            ],
            [
                'name' => 'Follow Up Pasca Pindah',
                'description' => 'Follow up kondisi pasien setelah pindah BPJS',
                'category_type' => 'bpjs_transfer',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            MarketingCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}