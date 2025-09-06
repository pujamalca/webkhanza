<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kesehatan Umum',
                'slug' => 'kesehatan-umum',
                'description' => 'Artikel seputar kesehatan umum, tips hidup sehat, dan pencegahan penyakit',
                'color' => '#22C55E',
                'icon' => 'fas fa-heart',
                'meta_title' => 'Kesehatan Umum - Tips dan Informasi Kesehatan',
                'meta_description' => 'Temukan artikel kesehatan umum, tips hidup sehat, dan informasi pencegahan penyakit terkini.',
                'meta_keywords' => ['kesehatan', 'tips sehat', 'pencegahan penyakit', 'hidup sehat'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pelayanan Medis',
                'slug' => 'pelayanan-medis',
                'description' => 'Informasi tentang layanan medis, prosedur medis, dan fasilitas kesehatan',
                'color' => '#3B82F6',
                'icon' => 'fas fa-stethoscope',
                'meta_title' => 'Pelayanan Medis - Informasi Layanan Kesehatan',
                'meta_description' => 'Pelajari tentang berbagai layanan medis, prosedur medis, dan fasilitas kesehatan yang tersedia.',
                'meta_keywords' => ['pelayanan medis', 'layanan kesehatan', 'prosedur medis', 'fasilitas kesehatan'],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Berita Rumah Sakit',
                'slug' => 'berita-rumah-sakit',
                'description' => 'Berita terkini, pengumuman, dan update dari rumah sakit',
                'color' => '#EF4444',
                'icon' => 'fas fa-newspaper',
                'meta_title' => 'Berita Rumah Sakit - Update dan Pengumuman Terkini',
                'meta_description' => 'Dapatkan berita terkini, pengumuman penting, dan update dari rumah sakit.',
                'meta_keywords' => ['berita rumah sakit', 'pengumuman', 'update', 'informasi'],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Edukasi Pasien',
                'slug' => 'edukasi-pasien',
                'description' => 'Edukasi dan panduan untuk pasien dan keluarga',
                'color' => '#F59E0B',
                'icon' => 'fas fa-graduation-cap',
                'meta_title' => 'Edukasi Pasien - Panduan dan Informasi untuk Pasien',
                'meta_description' => 'Akses edukasi dan panduan lengkap untuk pasien dan keluarga tentang berbagai kondisi kesehatan.',
                'meta_keywords' => ['edukasi pasien', 'panduan kesehatan', 'informasi pasien', 'keluarga'],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Teknologi Kesehatan',
                'slug' => 'teknologi-kesehatan',
                'description' => 'Informasi tentang teknologi terbaru dalam dunia kesehatan',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-laptop-medical',
                'meta_title' => 'Teknologi Kesehatan - Inovasi dalam Dunia Medis',
                'meta_description' => 'Jelajahi teknologi terbaru dan inovasi dalam dunia kesehatan dan kedokteran.',
                'meta_keywords' => ['teknologi kesehatan', 'inovasi medis', 'teknologi medis', 'digitalisasi kesehatan'],
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }
    }
}