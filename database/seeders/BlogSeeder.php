<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first() ?: User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@webkhanza.com',
        ]);

        $categories = [
            [
                'name' => 'Kesehatan Umum',
                'description' => 'Tips dan informasi kesehatan untuk masyarakat umum',
                'color' => '#10B981',
                'status' => 'active',
            ],
            [
                'name' => 'Teknologi Medis',
                'description' => 'Perkembangan teknologi dalam dunia medis',
                'color' => '#3B82F6',
                'status' => 'active',
            ],
            [
                'name' => 'Pengumuman',
                'description' => 'Pengumuman dan berita terkini dari rumah sakit',
                'color' => '#F59E0B',
                'status' => 'active',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            $createdCategories[] = BlogCategory::create($category);
        }

        $tags = [
            ['name' => 'COVID-19', 'color' => '#EF4444', 'status' => 'active'],
            ['name' => 'Vaksinasi', 'color' => '#10B981', 'status' => 'active'],
            ['name' => 'Pencegahan', 'color' => '#8B5CF6', 'status' => 'active'],
            ['name' => 'Telemedicine', 'color' => '#3B82F6', 'status' => 'active'],
            ['name' => 'Kesehatan Mental', 'color' => '#EC4899', 'status' => 'active'],
            ['name' => 'Nutrisi', 'color' => '#F59E0B', 'status' => 'active'],
        ];

        $createdTags = [];
        foreach ($tags as $tag) {
            $tag['slug'] = Str::slug($tag['name']);
            $createdTags[] = BlogTag::create($tag);
        }

        $blogPosts = [
            [
                'title' => 'Tips Menjaga Kesehatan di Era Digital',
                'excerpt' => 'Dalam era digital ini, penting untuk menjaga keseimbangan antara teknologi dan kesehatan fisik maupun mental.',
                'content' => '<h2>Pentingnya Menjaga Kesehatan Digital</h2>

<p>Era digital telah mengubah cara kita bekerja, belajar, dan berinteraksi. Namun, kemudahan teknologi juga membawa tantangan baru untuk kesehatan kita. Berikut adalah beberapa tips untuk menjaga kesehatan di era digital:</p>

<h3>1. Atur Waktu Screen Time</h3>
<p>Batasi waktu penggunaan perangkat elektronik, terutama sebelum tidur. Cahaya biru dari layar dapat mengganggu kualitas tidur.</p>

<h3>2. Jaga Postur Tubuh</h3>
<p>Pastikan posisi duduk yang ergonomis saat bekerja di depan komputer. Atur tinggi monitor sejajar dengan mata dan gunakan kursi yang mendukung punggung.</p>

<h3>3. Lakukan Digital Detox</h3>
<p>Sisihkan waktu untuk terlepas dari perangkat digital, misalnya saat makan atau sebelum tidur.</p>

<h3>4. Tetap Aktif Bergerak</h3>
<p>Lakukan peregangan atau olahraga ringan setiap 30 menit sekali untuk mengurangi kelelahan mata dan otot.</p>

<p>Dengan menerapkan tips-tips di atas, kita dapat memanfaatkan teknologi dengan bijak tanpa mengorbankan kesehatan.</p>',
                'category' => 0, // Kesehatan Umum
                'tags' => [4, 2], // Kesehatan Mental, Pencegahan
                'status' => 'published',
                'is_featured' => true,
                'meta_title' => 'Tips Menjaga Kesehatan di Era Digital - WebKhanza',
                'meta_description' => 'Pelajari cara menjaga kesehatan fisik dan mental di era digital. Tips praktis untuk keseimbangan teknologi dan kesehatan.',
                'meta_keywords' => ['kesehatan digital', 'screen time', 'ergonomis', 'digital detox'],
            ],
            [
                'title' => 'Pentingnya Vaksinasi untuk Kesehatan Keluarga',
                'excerpt' => 'Vaksinasi merupakan salah satu cara paling efektif untuk melindungi keluarga dari berbagai penyakit menular.',
                'content' => '<h2>Mengapa Vaksinasi Penting?</h2>

<p>Vaksinasi adalah proses pemberian vaksin untuk memberikan kekebalan tubuh terhadap penyakit tertentu. Ini merupakan salah satu pencapaian terbesar dalam bidang kesehatan masyarakat.</p>

<h3>Manfaat Vaksinasi:</h3>
<ul>
<li><strong>Perlindungan Individu:</strong> Melindungi diri sendiri dari penyakit berbahaya</li>
<li><strong>Kekebalan Kelompok:</strong> Melindungi masyarakat secara keseluruhan</li>
<li><strong>Mencegah Wabah:</strong> Mengurangi risiko penyebaran penyakit</li>
<li><strong>Melindungi Generasi Mendatang:</strong> Beberapa penyakit dapat dieliminasi melalui vaksinasi</li>
</ul>

<h3>Jenis Vaksinasi Penting:</h3>
<p><strong>Untuk Anak-anak:</strong> BCG, DPT, Polio, Campak, Hepatitis B</p>
<p><strong>Untuk Dewasa:</strong> Influenza tahunan, COVID-19, Tetanus</p>

<h3>Keamanan Vaksin</h3>
<p>Semua vaksin telah melalui uji klinis yang ketat sebelum disetujui untuk penggunaan umum. Efek samping yang serius sangat jarang terjadi.</p>

<p>Konsultasikan dengan dokter untuk mengetahui jadwal vaksinasi yang tepat untuk Anda dan keluarga.</p>',
                'category' => 0, // Kesehatan Umum
                'tags' => [1, 2], // Vaksinasi, Pencegahan
                'status' => 'published',
                'is_featured' => true,
                'meta_title' => 'Pentingnya Vaksinasi untuk Kesehatan Keluarga',
                'meta_description' => 'Pahami pentingnya vaksinasi untuk melindungi keluarga. Informasi lengkap tentang jenis vaksin dan manfaatnya.',
                'meta_keywords' => ['vaksinasi', 'imunisasi', 'kesehatan keluarga', 'pencegahan penyakit'],
            ],
            [
                'title' => 'Telemedicine: Revolusi Pelayanan Kesehatan Modern',
                'excerpt' => 'Telemedicine menghadirkan solusi pelayanan kesehatan yang mudah diakses dan efisien di era digital.',
                'content' => '<h2>Apa itu Telemedicine?</h2>

<p>Telemedicine adalah praktik kedokteran yang menggunakan teknologi telekomunikasi untuk memberikan layanan kesehatan jarak jauh. Ini memungkinkan pasien berkonsultasi dengan dokter tanpa harus datang ke klinik atau rumah sakit.</p>

<h3>Keunggulan Telemedicine:</h3>
<ul>
<li><strong>Aksesibilitas:</strong> Layanan kesehatan dapat diakses dari mana saja</li>
<li><strong>Efisiensi Waktu:</strong> Mengurangi waktu tunggu dan perjalanan</li>
<li><strong>Cost-Effective:</strong> Lebih ekonomis untuk konsultasi rutin</li>
<li><strong>Kontinuitas Perawatan:</strong> Memudahkan follow-up pasien</li>
</ul>

<h3>Jenis Layanan Telemedicine:</h3>

<h4>1. Teleconsultation</h4>
<p>Konsultasi langsung dengan dokter melalui video call atau chat.</p>

<h4>2. Telemonitoring</h4>
<p>Pemantauan kondisi kesehatan pasien secara jarak jauh menggunakan perangkat digital.</p>

<h4>3. Telepharmacy</h4>
<p>Layanan konsultasi farmasi dan pengiriman obat.</p>

<h3>Teknologi Pendukung:</h3>
<ul>
<li>Aplikasi mobile kesehatan</li>
<li>Wearable devices</li>
<li>IoT medical devices</li>
<li>AI untuk diagnosis awal</li>
</ul>

<h3>Masa Depan Telemedicine</h3>
<p>Dengan perkembangan AI dan 5G, telemedicine akan semakin canggih dengan kemampuan diagnosis yang lebih akurat dan real-time monitoring.</p>

<p>Telemedicine bukan pengganti pemeriksaan fisik, namun menjadi komplemen yang sangat berharga dalam sistem pelayanan kesehatan modern.</p>',
                'category' => 1, // Teknologi Medis
                'tags' => [3], // Telemedicine
                'status' => 'published',
                'is_featured' => false,
                'meta_title' => 'Telemedicine: Revolusi Pelayanan Kesehatan Modern',
                'meta_description' => 'Pelajari tentang telemedicine dan bagaimana teknologi mengubah cara kita mengakses layanan kesehatan.',
                'meta_keywords' => ['telemedicine', 'teknologi medis', 'konsultasi online', 'digital health'],
            ],
            [
                'title' => 'Pengumuman: Layanan Baru Laboratorium 24 Jam',
                'excerpt' => 'WebKhanza dengan bangga mengumumkan pembukaan layanan laboratorium 24 jam untuk melayani kebutuhan pemeriksaan mendesak.',
                'content' => '<h2>Layanan Laboratorium 24 Jam Kini Tersedia</h2>

<p>Dalam upaya meningkatkan kualitas pelayanan kesehatan, WebKhanza dengan bangga mengumumkan pembukaan layanan laboratorium 24 jam yang akan beroperasi mulai tanggal 1 Oktober 2025.</p>

<h3>Layanan yang Tersedia:</h3>

<h4>Pemeriksaan Darurat 24/7:</h4>
<ul>
<li>Pemeriksaan darah lengkap</li>
<li>Tes COVID-19 (PCR dan Antigen)</li>
<li>Tes kehamilan</li>
<li>Tes gula darah</li>
<li>Urinalisis</li>
</ul>

<h4>Layanan Khusus:</h4>
<ul>
<li>Tes narkoba untuk keperluan kerja</li>
<li>Pemeriksaan pre-operasi darurat</li>
<li>Monitoring pasien rawat inap</li>
</ul>

<h3>Keunggulan Layanan:</h3>
<ul>
<li><strong>Teknologi Terdepan:</strong> Menggunakan peralatan laboratorium terbaru</li>
<li><strong>Hasil Cepat:</strong> Hasil tersedia dalam 1-2 jam untuk tes darurat</li>
<li><strong>Tim Profesional:</strong> Dikelola oleh teknisi laboratorium bersertifikat</li>
<li><strong>Quality Control:</strong> Sistem kontrol kualitas yang ketat</li>
</ul>

<h3>Cara Mengakses Layanan:</h3>
<ol>
<li>Datang langsung ke laboratorium WebKhanza</li>
<li>Booking online melalui aplikasi WebKhanza</li>
<li>Melalui rujukan dari dokter</li>
<li>Layanan home service untuk area tertentu</li>
</ol>

<h3>Informasi Tarif:</h3>
<p>Tarif layanan laboratorium 24 jam disesuaikan dengan kompleksitas pemeriksaan. Untuk informasi lebih detail, hubungi customer service kami di 021-XXXXXXX.</p>

<h3>Promo Pembukaan:</h3>
<p><strong>Diskon 20%</strong> untuk semua pemeriksaan laboratorium selama bulan Oktober 2025!</p>

<p>Kami berkomitmen untuk terus memberikan pelayanan kesehatan terbaik bagi masyarakat. Dengan layanan laboratorium 24 jam ini, kami berharap dapat membantu diagnosis dan penanganan medis yang lebih cepat dan akurat.</p>',
                'category' => 2, // Pengumuman
                'tags' => [], // No tags
                'status' => 'published',
                'is_featured' => true,
                'meta_title' => 'Layanan Baru Laboratorium 24 Jam - WebKhanza',
                'meta_description' => 'WebKhanza membuka layanan laboratorium 24 jam dengan teknologi terdepan dan hasil cepat untuk kebutuhan medis darurat.',
                'meta_keywords' => ['laboratorium 24 jam', 'tes darurat', 'webkhanza', 'layanan kesehatan'],
            ],
            [
                'title' => 'Nutrisi Seimbang: Kunci Hidup Sehat dan Produktif',
                'excerpt' => 'Pelajari pentingnya nutrisi seimbang dan bagaimana mengatur pola makan yang tepat untuk hidup yang lebih sehat.',
                'content' => '<h2>Mengapa Nutrisi Seimbang Penting?</h2>

<p>Nutrisi seimbang adalah fondasi dari kesehatan yang optimal. Tubuh membutuhkan berbagai macam zat gizi untuk berfungsi dengan baik, mulai dari pertumbuhan, perbaikan sel, hingga menjaga sistem kekebalan tubuh.</p>

<h3>Komponen Nutrisi Seimbang:</h3>

<h4>1. Karbohidrat (45-65% total kalori)</h4>
<ul>
<li><strong>Fungsi:</strong> Sumber energi utama tubuh</li>
<li><strong>Sumber baik:</strong> Beras merah, oats, quinoa, ubi jalar</li>
<li><strong>Hindari:</strong> Gula sederhana berlebihan, tepung putih</li>
</ul>

<h4>2. Protein (10-35% total kalori)</h4>
<ul>
<li><strong>Fungsi:</strong> Membangun dan memperbaiki jaringan tubuh</li>
<li><strong>Sumber baik:</strong> Ikan, ayam tanpa kulit, kacang-kacangan, tahu, tempe</li>
<li><strong>Kebutuhan:</strong> 0.8-1 gram per kg berat badan</li>
</ul>

<h4>3. Lemak (20-35% total kalori)</h4>
<ul>
<li><strong>Fungsi:</strong> Penyerapan vitamin, produksi hormon</li>
<li><strong>Lemak baik:</strong> Alpukat, minyak zaitun, kacang-kacangan, ikan salmon</li>
<li><strong>Batasi:</strong> Lemak jenuh dan trans fat</li>
</ul>

<h3>Tips Menerapkan Pola Makan Seimbang:</h3>

<h4>Prinsip "Isi Piringku":</h4>
<ul>
<li>1/2 piring: Sayuran dan buah</li>
<li>1/4 piring: Protein hewani/nabati</li>
<li>1/4 piring: Karbohidrat kompleks</li>
</ul>

<h4>Jadwal Makan Teratur:</h4>
<ul>
<li>3 kali makan utama</li>
<li>2 kali snack sehat</li>
<li>Minum air 8 gelas per hari</li>
</ul>

<h3>Manfaat Nutrisi Seimbang:</h3>
<ul>
<li><strong>Energi Optimal:</strong> Tubuh berenergi sepanjang hari</li>
<li><strong>Berat Badan Ideal:</strong> Membantu mencapai dan mempertahankan berat badan sehat</li>
<li><strong>Imunitas Kuat:</strong> Sistem kekebalan tubuh yang lebih baik</li>
<li><strong>Mood Stabil:</strong> Gula darah yang stabil mempengaruhi mood positif</li>
<li><strong>Pencegahan Penyakit:</strong> Mengurangi risiko diabetes, jantung, dan kanker</li>
</ul>

<h3>Menu Sehari Contoh:</h3>

<h4>Sarapan:</h4>
<p>Oatmeal dengan buah berry dan kacang almond + segelas susu rendah lemak</p>

<h4>Makan Siang:</h4>
<p>Nasi merah + ikan panggang + tumis sayuran + buah potong</p>

<h4>Makan Malam:</h4>
<p>Sup ayam + roti gandum + salad sayuran</p>

<h4>Snack:</h4>
<p>Buah segar, yogurt, atau kacang-kacangan</p>

<p>Ingatlah bahwa perubahan pola makan yang berkelanjutan lebih baik daripada diet ekstrem jangka pendek. Konsultasikan dengan ahli gizi untuk rencana makan yang sesuai dengan kondisi kesehatan Anda.</p>',
                'category' => 0, // Kesehatan Umum
                'tags' => [5], // Nutrisi
                'status' => 'published',
                'is_featured' => false,
                'meta_title' => 'Nutrisi Seimbang: Kunci Hidup Sehat dan Produktif',
                'meta_description' => 'Panduan lengkap nutrisi seimbang untuk hidup sehat. Pelajari komponen gizi, tips pola makan, dan menu sehat harian.',
                'meta_keywords' => ['nutrisi seimbang', 'pola makan sehat', 'gizi seimbang', 'menu sehat'],
            ],
        ];

        foreach ($blogPosts as $index => $post) {
            $blog = Blog::create([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'blog_category_id' => $createdCategories[$post['category']]->id,
                'user_id' => $admin->id,
                'status' => $post['status'],
                'published_at' => now()->subDays(rand(1, 30)),
                'is_featured' => $post['is_featured'],
                'allow_comments' => true,
                'views_count' => rand(100, 1000),
                'likes_count' => rand(10, 100),
                'shares_count' => rand(5, 50),
                'meta_title' => $post['meta_title'],
                'meta_description' => $post['meta_description'],
                'meta_keywords' => $post['meta_keywords'],
                'sort_order' => $index + 1,
            ]);

            if (!empty($post['tags'])) {
                $tagIds = [];
                foreach ($post['tags'] as $tagIndex) {
                    $tagIds[] = $createdTags[$tagIndex]->id;
                }
                $blog->tags()->attach($tagIds);
            }
        }

        $this->command->info('Blog seeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 3 blog categories');
        $this->command->info('- 6 blog tags');  
        $this->command->info('- 5 blog posts with SEO optimization');
    }
}