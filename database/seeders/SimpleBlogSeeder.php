<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimpleBlogSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Kesehatan Umum',
                'slug' => 'kesehatan-umum',
                'description' => 'Tips dan informasi kesehatan untuk masyarakat umum',
                'color' => '#10B981',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknologi Medis',
                'slug' => 'teknologi-medis', 
                'description' => 'Perkembangan teknologi dalam dunia medis',
                'color' => '#3B82F6',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pengumuman',
                'slug' => 'pengumuman',
                'description' => 'Pengumuman dan berita terkini dari rumah sakit',
                'color' => '#F59E0B',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            DB::table('blog_categories')->insert($category);
        }

        // Create tags
        $tags = [
            ['name' => 'COVID-19', 'slug' => 'covid-19', 'color' => '#EF4444', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vaksinasi', 'slug' => 'vaksinasi', 'color' => '#10B981', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pencegahan', 'slug' => 'pencegahan', 'color' => '#8B5CF6', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Telemedicine', 'slug' => 'telemedicine', 'color' => '#3B82F6', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kesehatan Mental', 'slug' => 'kesehatan-mental', 'color' => '#EC4899', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nutrisi', 'slug' => 'nutrisi', 'color' => '#F59E0B', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($tags as $tag) {
            DB::table('blog_tags')->insert($tag);
        }

        // Create sample blogs (assuming user with id = 1 exists)
        $userId = DB::table('users')->first()->id ?? 1;
        
        $blogs = [
            [
                'title' => 'Tips Menjaga Kesehatan di Era Digital',
                'slug' => 'tips-menjaga-kesehatan-di-era-digital',
                'excerpt' => 'Dalam era digital ini, penting untuk menjaga keseimbangan antara teknologi dan kesehatan fisik maupun mental.',
                'content' => '<h2>Pentingnya Menjaga Kesehatan Digital</h2><p>Era digital telah mengubah cara kita bekerja, belajar, dan berinteraksi. Namun, kemudahan teknologi juga membawa tantangan baru untuk kesehatan kita.</p>',
                'blog_category_id' => 1,
                'user_id' => $userId,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'reading_time' => 5,
                'views_count' => 150,
                'likes_count' => 25,
                'shares_count' => 8,
                'is_featured' => true,
                'allow_comments' => true,
                'meta_title' => 'Tips Menjaga Kesehatan di Era Digital - WebKhanza',
                'meta_description' => 'Pelajari cara menjaga kesehatan fisik dan mental di era digital. Tips praktis untuk keseimbangan teknologi dan kesehatan.',
                'meta_keywords' => json_encode(['kesehatan digital', 'screen time', 'ergonomis', 'digital detox']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pentingnya Vaksinasi untuk Kesehatan Keluarga',
                'slug' => 'pentingnya-vaksinasi-untuk-kesehatan-keluarga',
                'excerpt' => 'Vaksinasi merupakan salah satu cara paling efektif untuk melindungi keluarga dari berbagai penyakit menular.',
                'content' => '<h2>Mengapa Vaksinasi Penting?</h2><p>Vaksinasi adalah proses pemberian vaksin untuk memberikan kekebalan tubuh terhadap penyakit tertentu. Ini merupakan salah satu pencapaian terbesar dalam bidang kesehatan masyarakat.</p>',
                'blog_category_id' => 1,
                'user_id' => $userId,
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'reading_time' => 7,
                'views_count' => 200,
                'likes_count' => 45,
                'shares_count' => 15,
                'is_featured' => true,
                'allow_comments' => true,
                'meta_title' => 'Pentingnya Vaksinasi untuk Kesehatan Keluarga',
                'meta_description' => 'Pahami pentingnya vaksinasi untuk melindungi keluarga. Informasi lengkap tentang jenis vaksin dan manfaatnya.',
                'meta_keywords' => json_encode(['vaksinasi', 'imunisasi', 'kesehatan keluarga', 'pencegahan penyakit']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Telemedicine: Revolusi Pelayanan Kesehatan Modern',
                'slug' => 'telemedicine-revolusi-pelayanan-kesehatan-modern',
                'excerpt' => 'Telemedicine menghadirkan solusi pelayanan kesehatan yang mudah diakses dan efisien di era digital.',
                'content' => '<h2>Apa itu Telemedicine?</h2><p>Telemedicine adalah praktik kedokteran yang menggunakan teknologi telekomunikasi untuk memberikan layanan kesehatan jarak jauh.</p>',
                'blog_category_id' => 2,
                'user_id' => $userId,
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'reading_time' => 6,
                'views_count' => 300,
                'likes_count' => 35,
                'shares_count' => 12,
                'is_featured' => false,
                'allow_comments' => true,
                'meta_title' => 'Telemedicine: Revolusi Pelayanan Kesehatan Modern',
                'meta_description' => 'Pelajari tentang telemedicine dan bagaimana teknologi mengubah cara kita mengakses layanan kesehatan.',
                'meta_keywords' => json_encode(['telemedicine', 'teknologi medis', 'konsultasi online', 'digital health']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($blogs as $blog) {
            DB::table('blogs')->insert($blog);
        }

        // Create blog-tag relationships
        $blogTagRelations = [
            ['blog_id' => 1, 'blog_tag_id' => 5, 'created_at' => now(), 'updated_at' => now()], // kesehatan mental
            ['blog_id' => 1, 'blog_tag_id' => 3, 'created_at' => now(), 'updated_at' => now()], // pencegahan
            ['blog_id' => 2, 'blog_tag_id' => 2, 'created_at' => now(), 'updated_at' => now()], // vaksinasi
            ['blog_id' => 2, 'blog_tag_id' => 3, 'created_at' => now(), 'updated_at' => now()], // pencegahan
            ['blog_id' => 3, 'blog_tag_id' => 4, 'created_at' => now(), 'updated_at' => now()], // telemedicine
        ];

        foreach ($blogTagRelations as $relation) {
            DB::table('blog_blog_tag')->insert($relation);
        }

        $this->command->info('Blog seeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 3 blog categories');
        $this->command->info('- 6 blog tags');
        $this->command->info('- 3 blog posts with SEO optimization');
    }
}