<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first user as author
        $user = User::first();
        if (!$user) {
            $this->command->error('No users found. Please seed users first.');
            return;
        }

        // Get all categories
        $categories = BlogCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('No blog categories found. Please run BlogCategorySeeder first.');
            return;
        }

        $blogContents = [
            // Kesehatan Umum - Blog 1
            [
                'title' => 'Tips Menjaga Kesehatan di Musim Hujan',
                'excerpt' => 'Musim hujan seringkali membawa berbagai penyakit. Ikuti tips berikut untuk tetap sehat selama musim penghujan.',
                'content' => '<h2>Menjaga Kesehatan di Musim Hujan</h2>
                
<p>Musim hujan identik dengan peningkatan kasus flu, batuk, dan penyakit lainnya. Berikut tips untuk menjaga kesehatan:</p>

<h3>1. Jaga Kebersihan Diri</h3>
<ul>
<li>Cuci tangan dengan sabun secara teratur</li>
<li>Mandi dengan air hangat</li>
<li>Ganti pakaian basah segera</li>
</ul>

<h3>2. Konsumsi Makanan Bergizi</h3>
<p>Perbanyak konsumsi vitamin C dari buah-buahan segar seperti jeruk, pepaya, dan kiwi.</p>

<h3>3. Jaga Kebersihan Lingkungan</h3>
<p>Pastikan rumah tetap bersih dan tidak lembab untuk mencegah pertumbuhan bakteri dan jamur.</p>

<p>Dengan menerapkan tips di atas, Anda dapat tetap sehat selama musim hujan.</p>',
                'meta_title' => 'Tips Kesehatan Musim Hujan - Panduan Lengkap',
                'meta_description' => 'Panduan lengkap menjaga kesehatan di musim hujan. Tips praktis mencegah penyakit dan tetap fit.',
                'meta_keywords' => ['kesehatan', 'musim hujan', 'tips sehat', 'pencegahan penyakit']
            ],
            
            // Kesehatan Umum - Blog 2
            [
                'title' => 'Pentingnya Olahraga Rutin untuk Kesehatan Mental',
                'excerpt' => 'Olahraga tidak hanya baik untuk fisik, tetapi juga memberikan dampak positif bagi kesehatan mental dan emosional.',
                'content' => '<h2>Olahraga dan Kesehatan Mental</h2>

<p>Penelitian menunjukkan bahwa olahraga rutin memiliki efek positif yang signifikan terhadap kesehatan mental.</p>

<h3>Manfaat Olahraga untuk Mental:</h3>
<ul>
<li><strong>Mengurangi Stres:</strong> Olahraga melepaskan endorfin yang mengurangi stres</li>
<li><strong>Meningkatkan Mood:</strong> Aktivitas fisik membantu mengatasi depresi ringan</li>
<li><strong>Meningkatkan Kepercayaan Diri:</strong> Pencapaian dalam olahraga meningkatkan self-esteem</li>
<li><strong>Tidur Lebih Berkualitas:</strong> Olahraga membantu mengatur pola tidur</li>
</ul>

<h3>Jenis Olahraga yang Direkomendasikan:</h3>
<ul>
<li>Jogging atau lari pagi</li>
<li>Yoga dan meditasi</li>
<li>Berenang</li>
<li>Bersepeda</li>
<li>Olahraga tim seperti badminton</li>
</ul>

<p>Mulailah dengan 30 menit olahraga 3-4 kali seminggu untuk merasakan manfaatnya.</p>',
                'meta_title' => 'Olahraga untuk Kesehatan Mental - Manfaat dan Tips',
                'meta_description' => 'Pelajari bagaimana olahraga rutin dapat meningkatkan kesehatan mental, mengurangi stres, dan meningkatkan mood.',
                'meta_keywords' => ['olahraga', 'kesehatan mental', 'endorfin', 'stres', 'mood']
            ],

            // Pelayanan Medis - Blog 1
            [
                'title' => 'Layanan Pemeriksaan Kesehatan Comprehensive di WebKhanza',
                'excerpt' => 'WebKhanza menyediakan paket pemeriksaan kesehatan menyeluruh dengan teknologi terdepan dan tim medis berpengalaman.',
                'content' => '<h2>Medical Check-Up Comprehensive</h2>

<p>Pemeriksaan kesehatan rutin adalah investasi terbaik untuk masa depan yang sehat. WebKhanza menyediakan berbagai paket pemeriksaan yang disesuaikan dengan kebutuhan Anda.</p>

<h3>Paket Pemeriksaan Tersedia:</h3>

<h4>1. Paket Basic Health Check</h4>
<ul>
<li>Pemeriksaan fisik lengkap</li>
<li>Laboratorium darah rutin</li>
<li>Urine lengkap</li>
<li>EKG istirahat</li>
<li>Rontgen thorax</li>
</ul>

<h4>2. Paket Executive Health Check</h4>
<ul>
<li>Semua pemeriksaan paket Basic</li>
<li>USG abdomen</li>
<li>Treadmill test</li>
<li>Konsultasi dengan dokter spesialis</li>
<li>Pemeriksaan mata lengkap</li>
</ul>

<h3>Keunggulan Layanan:</h3>
<ul>
<li><strong>Tim Profesional:</strong> Dokter spesialis dan perawat bersertifikat</li>
<li><strong>Peralatan Modern:</strong> Teknologi medis terkini</li>
<li><strong>Hasil Cepat:</strong> Laporan lengkap dalam 24 jam</li>
<li><strong>Follow-up Care:</strong> Konsultasi lanjutan jika diperlukan</li>
</ul>

<p>Jadwalkan pemeriksaan kesehatan Anda hari ini melalui aplikasi WebKhanza atau hubungi customer service kami.</p>',
                'meta_title' => 'Medical Check-Up WebKhanza - Pemeriksaan Kesehatan Comprehensive',
                'meta_description' => 'Layanan medical check-up lengkap di WebKhanza dengan teknologi modern dan tim profesional. Booking sekarang!',
                'meta_keywords' => ['medical check up', 'pemeriksaan kesehatan', 'webkhanza', 'laboratorium']
            ],

            // Pelayanan Medis - Blog 2
            [
                'title' => 'Layanan IGD 24 Jam: Siap Melayani Keadaan Darurat',
                'excerpt' => 'Unit Gawat Darurat WebKhanza beroperasi 24/7 dengan tim medis siaga dan fasilitas lengkap untuk menangani kasus emergensi.',
                'content' => '<h2>Instalasi Gawat Darurat (IGD) WebKhanza</h2>

<p>IGD WebKhanza berkomitmen memberikan pelayanan gawat darurat terbaik dengan respons cepat dan penanganan profesional.</p>

<h3>Fasilitas IGD:</h3>
<ul>
<li><strong>Ruang Resusitasi:</strong> Equipped dengan ventilator dan defibrilator</li>
<li><strong>Ruang Observasi:</strong> 24 bed dengan monitoring lengkap</li>
<li><strong>Ruang Tindakan:</strong> Untuk prosedur minor</li>
<li><strong>CT Scan 24 Jam:</strong> Untuk diagnosis cepat</li>
<li><strong>Laboratorium Darurat:</strong> Hasil dalam 30 menit</li>
</ul>

<h3>Kasus yang Ditangani:</h3>
<h4>Kasus Merah (Life Threatening):</h4>
<ul>
<li>Serangan jantung</li>
<li>Stroke akut</li>
<li>Trauma mayor</li>
<li>Sesak napas berat</li>
</ul>

<h4>Kasus Kuning (Urgent):</h4>
<ul>
<li>Demam tinggi pada anak</li>
<li>Nyeri dada</li>
<li>Trauma ringan-sedang</li>
<li>Keracunan</li>
</ul>

<h3>Tim Medis 24/7:</h3>
<ul>
<li>Dokter umum terlatih emergensi</li>
<li>Dokter spesialis on-call</li>
<li>Perawat ICU/ACLS certified</li>
<li>Radiographer siaga</li>
</ul>

<h3>Akses IGD:</h3>
<p><strong>Alamat:</strong> Jalan Kesehatan No. 123, Jakarta<br>
<strong>Telepon Darurat:</strong> 021-EMERGENCY<br>
<strong>Ambulans:</strong> Tersedia 24 jam dengan paramedis</p>

<p>Dalam keadaan darurat, segera hubungi 119 atau datang langsung ke IGD WebKhanza. Tim kami siap memberikan pertolongan terbaik.</p>',
                'meta_title' => 'IGD 24 Jam WebKhanza - Layanan Gawat Darurat',
                'meta_description' => 'IGD WebKhanza melayani 24/7 dengan tim medis profesional dan fasilitas lengkap untuk penanganan darurat.',
                'meta_keywords' => ['IGD', 'gawat darurat', 'emergency', '24 jam', 'webkhanza']
            ],

            // Berita Rumah Sakit - Blog 1
            [
                'title' => 'Pembukaan Poliklinik Jantung Terpadu WebKhanza',
                'excerpt' => 'WebKhanza dengan bangga mengumumkan pembukaan Poliklinik Jantung Terpadu dengan tim kardiolog berpengalaman dan teknologi terdepan.',
                'content' => '<h2>Poliklinik Jantung Terpadu Kini Hadir</h2>

<p>Dalam rangka meningkatkan kualitas layanan kardiovaskular, WebKhanza dengan bangga memperkenalkan Poliklinik Jantung Terpadu yang akan beroperasi mulai 15 Oktober 2025.</p>

<h3>Layanan yang Tersedia:</h3>

<h4>Konsultasi & Pemeriksaan:</h4>
<ul>
<li>Konsultasi kardiologi</li>
<li>EKG 12 lead</li>
<li>Echocardiography</li>
<li>Holter monitoring</li>
<li>Treadmill test</li>
</ul>

<h4>Tindakan Invasif:</h4>
<ul>
<li>Kateterisasi jantung</li>
<li>Pemasangan ring (stent)</li>
<li>Ablasi jantung</li>
<li>Pemasangan alat pacu jantung</li>
</ul>

<h3>Tim Dokter Spesialis:</h3>
<ul>
<li><strong>Dr. Ahmad Kardio, Sp.JP:</strong> Ahli Kardiologi Intervensi</li>
<li><strong>Dr. Sari Jantung, Sp.JP:</strong> Ahli Kardiologi Pediatrik</li>
<li><strong>Dr. Budi Vaskular, Sp.JP:</strong> Ahli Elektrofisiologi</li>
</ul>

<h3>Teknologi Terdepan:</h3>
<ul>
<li>Cath Lab dengan teknologi terbaru</li>
<li>Echo 4D real-time</li>
<li>CT Angiografi 256 slice</li>
<li>Sistem monitoring kardiak canggih</li>
</ul>

<h3>Program Khusus:</h3>
<ul>
<li><strong>Heart Health Screening:</strong> Program deteksi dini penyakit jantung</li>
<li><strong>Cardiac Rehabilitation:</strong> Program pemulihan pasca serangan jantung</li>
<li><strong>Heart Education Program:</strong> Edukasi kesehatan jantung untuk masyarakat</li>
</ul>

<h3>Jadwal Operasional:</h3>
<p><strong>Senin-Jumat:</strong> 08:00 - 20:00<br>
<strong>Sabtu:</strong> 08:00 - 16:00<br>
<strong>Minggu:</strong> 08:00 - 12:00 (Emergency only)</p>

<h3>Promo Grand Opening:</h3>
<p><strong>Diskon 25%</strong> untuk semua pemeriksaan jantung selama bulan Oktober!<br>
<em>*Syarat dan ketentuan berlaku</em></p>

<p>Untuk informasi lebih lanjut dan pendaftaran, hubungi Customer Service WebKhanza atau kunjungi website resmi kami.</p>',
                'meta_title' => 'Poliklinik Jantung Terpadu WebKhanza - Layanan Kardiovaskular Terdepan',
                'meta_description' => 'Poliklinik Jantung Terpadu WebKhanza hadir dengan tim kardiolog berpengalaman dan teknologi terdepan. Promo opening 25%!',
                'meta_keywords' => ['poliklinik jantung', 'kardiologi', 'webkhanza', 'layanan jantung', 'grand opening']
            ],

            // Berita Rumah Sakit - Blog 2
            [
                'title' => 'WebKhanza Raih Akreditasi JCI dan Sertifikat ISO 9001:2015',
                'excerpt' => 'Pencapaian gemilang WebKhanza dalam meraih akreditasi internasional JCI dan sertifikat ISO 9001:2015 sebagai komitmen kualitas pelayanan.',
                'content' => '<h2>Pencapaian Akreditasi Bergengsi</h2>

<p>WebKhanza dengan bangga mengumumkan pencapaian akreditasi Joint Commission International (JCI) dan sertifikasi ISO 9001:2015, menjadikan kami rumah sakit berstandar internasional.</p>

<h3>Tentang Akreditasi JCI:</h3>
<p>JCI adalah standar akreditasi rumah sakit tertinggi di dunia yang mengukur kualitas dan keselamatan pasien. Dari 2,847 rumah sakit di Indonesia, hanya 49 yang telah terakreditasi JCI.</p>

<h3>Manfaat untuk Pasien:</h3>
<ul>
<li><strong>Keselamatan Terjamin:</strong> Protokol keselamatan pasien internasional</li>
<li><strong>Kualitas Tinggi:</strong> Standar pelayanan setara rumah sakit dunia</li>
<li><strong>Akses Global:</strong> Kemudahan klaim asuransi internasional</li>
<li><strong>Continuous Improvement:</strong> Peningkatan kualitas berkelanjutan</li>
</ul>

<h3>Area yang Dinilai JCI:</h3>
<ol>
<li><strong>Patient Safety Goals:</strong> Sasaran keselamatan pasien</li>
<li><strong>Access to Care:</strong> Akses pelayanan yang mudah</li>
<li><strong>Patient Assessment:</strong> Asesmen pasien yang komprehensif</li>
<li><strong>Patient Care:</strong> Asuhan pasien yang bermutu</li>
<li><strong>Medication Management:</strong> Pengelolaan obat yang aman</li>
<li><strong>Infection Prevention:</strong> Pencegahan dan pengendalian infeksi</li>
</ol>

<h3>ISO 9001:2015 - Quality Management System:</h3>
<p>Sertifikasi ini memastikan bahwa sistem manajemen mutu WebKhanza memenuhi standar internasional dalam:</p>
<ul>
<li>Fokus pada kepuasan pasien</li>
<li>Pendekatan proses yang sistematis</li>
<li>Keterlibatan seluruh karyawan</li>
<li>Perbaikan berkelanjutan</li>
</ul>

<h3>Komitmen Berkelanjutan:</h3>
<p>Pencapaian ini bukan akhir perjalanan, tetapi awal komitmen kami untuk terus memberikan pelayanan kesehatan berkualitas tinggi dengan standar internasional.</p>

<h3>Ucapan Terima Kasih:</h3>
<p>Terima kasih kepada seluruh tim WebKhanza, pasien setia, dan mitra yang telah mendukung pencapaian ini. Mari bersama-sama membangun masa depan kesehatan yang lebih baik.</p>

<h3>Informasi:</h3>
<p>Untuk informasi lebih lanjut tentang layanan berstandar internasional kami, kunjungi website resmi WebKhanza atau hubungi Customer Care di 021-WEBKHANZA.</p>',
                'meta_title' => 'WebKhanza Raih Akreditasi JCI dan ISO 9001:2015',
                'meta_description' => 'WebKhanza berhasil meraih akreditasi JCI dan sertifikat ISO 9001:2015, menjadi rumah sakit berstandar internasional.',
                'meta_keywords' => ['akreditasi JCI', 'ISO 9001', 'webkhanza', 'standar internasional', 'kualitas pelayanan']
            ],

            // Edukasi Pasien - Blog 1
            [
                'title' => 'Panduan Persiapan Operasi: Yang Harus Anda Ketahui',
                'excerpt' => 'Panduan lengkap persiapan sebelum menjalani operasi, mulai dari persiapan fisik hingga mental untuk hasil yang optimal.',
                'content' => '<h2>Persiapan Menjelang Operasi</h2>

<p>Persiapan yang baik sebelum operasi sangat penting untuk kesuksesan tindakan dan mempercepat pemulihan. Berikut panduan lengkap yang perlu Anda ketahui.</p>

<h3>Persiapan Fisik:</h3>

<h4>1. Puasa Sebelum Operasi</h4>
<ul>
<li><strong>Makanan padat:</strong> Berhenti 8 jam sebelum operasi</li>
<li><strong>Cairan bening:</strong> Berhenti 2 jam sebelum operasi</li>
<li><strong>Susu/jus:</strong> Berhenti 6 jam sebelum operasi</li>
</ul>

<h4>2. Kebersihan Diri</h4>
<ul>
<li>Mandi dengan sabun antibakteri malam sebelumnya</li>
<li>Bersihkan kuku dan lepas cat kuku</li>
<li>Lepas semua perhiasan dan aksesori</li>
<li>Jangan menggunakan kosmetik atau parfum</li>
</ul>

<h4>3. Obat-obatan</h4>
<ul>
<li>Berhenti minum obat pengencer darah sesuai instruksi dokter</li>
<li>Laporkan semua obat dan suplemen yang dikonsumsi</li>
<li>Minum obat penting dengan sedikit air jika diizinkan dokter</li>
</ul>

<h3>Persiapan Mental:</h3>

<h4>Mengatasi Kecemasan:</h4>
<ul>
<li>Diskusikan kekhawatiran dengan tim medis</li>
<li>Praktikkan teknik relaksasi dan pernapasan</li>
<li>Minta dukungan keluarga dan teman</li>
<li>Fokus pada hasil positif</li>
</ul>

<h4>Edukasi Prosedur:</h4>
<ul>
<li>Pahami prosedur yang akan dilakukan</li>
<li>Tanyakan risiko dan manfaat operasi</li>
<li>Diskusikan proses pemulihan</li>
<li>Pahami instruksi pasca operasi</li>
</ul>

<h3>Persiapan Administratif:</h3>
<ul>
<li>Lengkapi dokumen informed consent</li>
<li>Siapkan kartu identitas dan asuransi</li>
<li>Konfirmasi jadwal operasi</li>
<li>Atur transportasi pulang</li>
</ul>

<h3>Hari Operasi:</h3>
<ul>
<li>Datang tepat waktu sesuai jadwal</li>
<li>Kenakan pakaian yang nyaman dan mudah dibuka</li>
<li>Jangan membawa barang berharga</li>
<li>Dampingi keluarga untuk support system</li>
</ul>

<h3>Tips Khusus untuk Berbagai Jenis Operasi:</h3>

<h4>Operasi Mata:</h4>
<p>Hindari makeup mata dan lensa kontak</p>

<h4>Operasi Gigi:</h4>
<p>Sikat gigi dengan lembut, hindari obat kumur beralkohol</p>

<h4>Operasi Perut:</h4>
<p>Persiapan usus sesuai instruksi dokter</p>

<h3>Yang Harus Dilaporkan:</h3>
<ul>
<li>Demam atau tanda infeksi</li>
<li>Perubahan kondisi kesehatan</li>
<li>Kehamilan (untuk wanita)</li>
<li>Alergi obat atau makanan</li>
</ul>

<p>Ingatlah bahwa tim medis WebKhanza siap membantu Anda di setiap langkah. Jangan ragu untuk bertanya jika ada yang tidak jelas.</p>',
                'meta_title' => 'Panduan Persiapan Operasi - Tips Lengkap untuk Pasien',
                'meta_description' => 'Panduan lengkap persiapan operasi mulai dari persiapan fisik, mental, hingga administratif untuk hasil optimal.',
                'meta_keywords' => ['persiapan operasi', 'panduan pasien', 'tips operasi', 'edukasi kesehatan']
            ],

            // Edukasi Pasien - Blog 2
            [
                'title' => 'Cara Merawat Luka Pasca Operasi di Rumah',
                'excerpt' => 'Panduan praktis merawat luka operasi di rumah untuk mempercepat penyembuhan dan mencegah komplikasi.',
                'content' => '<h2>Perawatan Luka Pasca Operasi</h2>

<p>Perawatan luka yang baik di rumah sangat penting untuk mempercepat penyembuhan dan mencegah komplikasi. Ikuti panduan berikut untuk perawatan optimal.</p>

<h3>Prinsip Dasar Perawatan Luka:</h3>
<ul>
<li><strong>Kebersihan:</strong> Jaga luka tetap bersih dan kering</li>
<li><strong>Observasi:</strong> Pantau tanda-tanda infeksi</li>
<li><strong>Perlindungan:</strong> Lindungi luka dari trauma</li>
<li><strong>Nutrisi:</strong> Konsumsi makanan bergizi untuk penyembuhan</li>
</ul>

<h3>Langkah-langkah Perawatan:</h3>

<h4>1. Persiapan Perawatan</h4>
<ul>
<li>Cuci tangan dengan sabun dan air mengalir</li>
<li>Siapkan alat: kasa steril, plester, cairan NaCl 0.9%</li>
<li>Gunakan sarung tangan sekali pakai jika tersedia</li>
</ul>

<h4>2. Membersihkan Luka</h4>
<ul>
<li>Lepas balutan lama dengan hati-hati</li>
<li>Bersihkan luka dengan NaCl 0.9% atau air matang</li>
<li>Keringkan area sekitar luka dengan kasa bersih</li>
<li>Jangan menggunakan alkohol atau betadine tanpa instruksi</li>
</ul>

<h4>3. Menutup Luka</h4>
<ul>
<li>Tutup dengan kasa steril</li>
<li>Rekatkan dengan plester hypoallergenic</li>
<li>Jangan menutup terlalu ketat</li>
<li>Ganti balutan sesuai instruksi (biasanya 1-2 hari sekali)</li>
</ul>

<h3>Tanda-tanda Normal vs Abnormal:</h3>

<h4>Normal:</h4>
<ul>
<li>Sedikit nyeri yang berangsur berkurang</li>
<li>Luka kering atau sedikit cairan bening</li>
<li>Tepi luka menyatu</li>
<li>Warna merah muda pada area penyembuhan</li>
</ul>

<h4>Waspada (Segera Hubungi Dokter):</h4>
<ul>
<li>Nyeri bertambah parah</li>
<li>Luka bernanah atau berbau</li>
<li>Demam >38°C</li>
<li>Luka terbuka kembali</li>
<li>Kemerahan yang menyebar</li>
<li>Pembengkakan yang bertambah</li>
</ul>

<h3>Tips Mempercepat Penyembuhan:</h3>

<h4>Nutrisi Optimal:</h4>
<ul>
<li><strong>Protein:</strong> Telur, ikan, daging tanpa lemak</li>
<li><strong>Vitamin C:</strong> Jeruk, pepaya, tomat</li>
<li><strong>Zinc:</strong> Kacang-kacangan, biji-bijian</li>
<li><strong>Air:</strong> Minum 8 gelas per hari</li>
</ul>

<h4>Lifestyle:</h4>
<ul>
<li>Istirahat cukup (7-8 jam per hari)</li>
<li>Hindari merokok dan alkohol</li>
<li>Jangan mengangkat beban berat</li>
<li>Batasi aktivitas sesuai instruksi dokter</li>
</ul>

<h3>Kapan Balutan Tidak Diperlukan:</h3>
<ul>
<li>Luka sudah kering dan tertutup</li>
<li>Tidak ada risiko gesekan atau trauma</li>
<li>Sudah 7-10 hari pasca operasi (tergantung jenis operasi)</li>
<li>Atas instruksi dokter</li>
</ul>

<h3>Aktivitas yang Harus Dihindari:</h3>
<ul>
<li>Berenang hingga luka sembuh total</li>
<li>Olahraga berat selama 4-6 minggu</li>
<li>Menggaruk atau menyentuh luka</li>
<li>Terkena sinar matahari langsung</li>
</ul>

<h3>Kontrol Rutin:</h3>
<p>Jangan lupa untuk kontrol sesuai jadwal yang diberikan dokter untuk memantau penyembuhan dan melepas jahitan jika diperlukan.</p>

<h3>Kontak Darurat:</h3>
<p><strong>IGD WebKhanza:</strong> 021-EMERGENCY<br>
<strong>Poliklinik Bedah:</strong> 021-SURGERY<br>
<strong>WhatsApp Konsultasi:</strong> 0812-WEBKHANZA</p>

<p>Ingat, setiap pasien memiliki kondisi yang unik. Selalu ikuti instruksi spesifik dari tim medis WebKhanza untuk perawatan optimal.</p>',
                'meta_title' => 'Cara Merawat Luka Pasca Operasi - Panduan Lengkap',
                'meta_description' => 'Panduan praktis merawat luka operasi di rumah, tanda bahaya, dan tips mempercepat penyembuhan.',
                'meta_keywords' => ['perawatan luka', 'pasca operasi', 'penyembuhan luka', 'panduan pasien']
            ],

            // Teknologi Kesehatan - Blog 1
            [
                'title' => 'Artificial Intelligence dalam Diagnosis Medis Modern',
                'excerpt' => 'Eksplorasi peran AI dalam revolutionizing diagnosis medis, dari radiologi hingga patologi, meningkatkan akurasi dan kecepatan diagnosis.',
                'content' => '<h2>AI: Masa Depan Diagnosis Medis</h2>

<p>Artificial Intelligence (AI) telah mengubah lanskap diagnosis medis dengan kemampuan analisis yang melampaui keterbatasan manusia. Teknologi ini membuka era baru dalam kedokteran presisi.</p>

<h3>Aplikasi AI dalam Diagnosis:</h3>

<h4>1. Radiologi & Medical Imaging</h4>
<ul>
<li><strong>CT Scan Analysis:</strong> Deteksi kanker paru dengan akurasi 94.4%</li>
<li><strong>MRI Brain Analysis:</strong> Identifikasi tumor dan stroke lebih cepat</li>
<li><strong>Mammography Screening:</strong> Deteksi dini kanker payudara</li>
<li><strong>Retinal Imaging:</strong> Diagnosis diabetic retinopathy</li>
</ul>

<h4>2. Patologi Digital</h4>
<ul>
<li>Analisis sampel jaringan otomatis</li>
<li>Klasifikasi sel kanker dengan presisi tinggi</li>
<li>Grading tumor berdasarkan histopatologi</li>
<li>Deteksi mikroorganisme patogen</li>
</ul>

<h4>3. Kardiologi</h4>
<ul>
<li>Interpretasi EKG real-time</li>
<li>Prediksi risiko serangan jantung</li>
<li>Analisis echocardiogram</li>
<li>Monitoring arrhythmia kontinyu</li>
</ul>

<h3>Keunggulan AI dalam Diagnosis:</h3>

<h4>Akurasi Tinggi:</h4>
<ul>
<li>Mengurangi human error</li>
<li>Konsistensi dalam interpretasi</li>
<li>Deteksi pattern yang subtle</li>
<li>Analisis quantitative objektif</li>
</ul>

<h4>Efisiensi Waktu:</h4>
<ul>
<li>Proses diagnosis lebih cepat</li>
<li>Prioritisasi kasus urgent</li>
<li>Workflow optimization</li>
<li>Reduced waiting time</li>
</ul>

<h4>Standardisasi:</h4>
<ul>
<li>Protokol diagnosis uniform</li>
<li>Quality assurance</li>
<li>Reproducible results</li>
<li>Evidence-based decisions</li>
</ul>

<h3>Implementasi AI di WebKhanza:</h3>

<h4>AI-Powered Radiology:</h4>
<ul>
<li><strong>Smart PACS:</strong> Sistem arsip gambar dengan AI analysis</li>
<li><strong>Auto-reporting:</strong> Generate laporan radiologi otomatis</li>
<li><strong>Critical Findings Alert:</strong> Notifikasi temuan kritis real-time</li>
</ul>

<h4>Laboratory AI:</h4>
<ul>
<li><strong>Smart Microscopy:</strong> Analisis sampel otomatis</li>
<li><strong>Result Validation:</strong> Verifikasi hasil lab dengan AI</li>
<li><strong>Trend Analysis:</strong> Analisis tren kesehatan pasien</li>
</ul>

<h3>Machine Learning Algorithms:</h3>

<h4>Deep Learning:</h4>
<ul>
<li><strong>Convolutional Neural Networks (CNN):</strong> Image recognition</li>
<li><strong>Recurrent Neural Networks (RNN):</strong> Sequential data analysis</li>
<li><strong>Transformer Models:</strong> Natural language processing</li>
</ul>

<h4>Specialized Models:</h4>
<ul>
<li><strong>U-Net:</strong> Medical image segmentation</li>
<li><strong>ResNet:</strong> Deep residual learning</li>
<li><strong>YOLO:</strong> Real-time object detection</li>
</ul>

<h3>Challenges & Solutions:</h3>

<h4>Data Quality:</h4>
<ul>
<li><strong>Challenge:</strong> Inconsistent data formats</li>
<li><strong>Solution:</strong> Data standardization protocols</li>
</ul>

<h4>Privacy & Security:</h4>
<ul>
<li><strong>Challenge:</strong> Patient data protection</li>
<li><strong>Solution:</strong> Federated learning, encryption</li>
</ul>

<h4>Regulatory Compliance:</h4>
<ul>
<li><strong>Challenge:</strong> FDA/CE marking requirements</li>
<li><strong>Solution:</strong> Rigorous validation studies</li>
</ul>

<h3>Future Prospects:</h3>

<h4>Emerging Technologies:</h4>
<ul>
<li><strong>Quantum Computing:</strong> Exponential processing power</li>
<li><strong>Edge AI:</strong> Real-time analysis di point-of-care</li>
<li><strong>Multimodal AI:</strong> Integration multiple data sources</li>
<li><strong>Explainable AI:</strong> Transparent decision-making</li>
</ul>

<h4>Personalized Medicine:</h4>
<ul>
<li>Genomic-guided therapy</li>
<li>Precision drug dosing</li>
<li>Individual risk prediction</li>
<li>Tailored treatment protocols</li>
</ul>

<h3>Ethical Considerations:</h3>
<ul>
<li><strong>Human-AI Collaboration:</strong> AI sebagai augmentation, bukan replacement</li>
<li><strong>Bias Mitigation:</strong> Ensure diverse training data</li>
<li><strong>Transparency:</strong> Explainable AI decisions</li>
<li><strong>Continuous Learning:</strong> Model updates dan improvement</li>
</ul>

<p>AI dalam diagnosis medis bukan tentang menggantikan dokter, tetapi memberdayakan mereka dengan tools yang lebih powerful untuk memberikan care yang lebih baik, cepat, dan akurat.</p>

<h3>Training & Education:</h3>
<p>WebKhanza berkomitmen untuk training berkelanjutan bagi tim medis dalam implementasi AI tools, memastikan optimal utilization untuk patient benefit.</p>',
                'meta_title' => 'AI dalam Diagnosis Medis - Revolusi Teknologi Kesehatan',
                'meta_description' => 'Eksplorasi peran Artificial Intelligence dalam diagnosis medis modern, dari radiologi hingga patologi, meningkatkan akurasi diagnosis.',
                'meta_keywords' => ['artificial intelligence', 'AI diagnosis', 'teknologi medis', 'machine learning', 'radiologi AI']
            ],

            // Teknologi Kesehatan - Blog 2
            [
                'title' => 'Internet of Medical Things (IoMT): Ekosistem Kesehatan Terhubung',
                'excerpt' => 'Jelajahi bagaimana IoMT mengubah delivery healthcare melalui perangkat medis yang terhubung, real-time monitoring, dan data-driven care.',
                'content' => '<h2>IoMT: Healthcare Connected Ecosystem</h2>

<p>Internet of Medical Things (IoMT) represents the convergence of medical devices, sensors, and cloud computing, creating an interconnected healthcare ecosystem yang revolutionizes patient care delivery.</p>

<h3>Definisi dan Komponen IoMT:</h3>

<h4>Core Components:</h4>
<ul>
<li><strong>Medical Sensors:</strong> Wearable dan implantable devices</li>
<li><strong>Connectivity:</strong> WiFi, Bluetooth, 5G, NFC</li>
<li><strong>Cloud Infrastructure:</strong> Data storage dan processing</li>
<li><strong>Analytics Platform:</strong> AI/ML untuk insight generation</li>
<li><strong>User Interface:</strong> Mobile apps dan web portals</li>
</ul>

<h3>Kategori Perangkat IoMT:</h3>

<h4>1. Wearable Devices</h4>
<ul>
<li><strong>Smartwatches:</strong> Heart rate, activity, sleep monitoring</li>
<li><strong>Fitness Trackers:</strong> Steps, calories, exercise tracking</li>
<li><strong>Smart Patches:</strong> Continuous medication delivery</li>
<li><strong>Smart Contact Lenses:</strong> Glucose monitoring (in development)</li>
</ul>

<h4>2. Remote Patient Monitoring</h4>
<ul>
<li><strong>Home Blood Pressure Monitors:</strong> Hypertension management</li>
<li><strong>Glucose Meters:</strong> Diabetes monitoring</li>
<li><strong>Smart Inhalers:</strong> Asthma medication compliance</li>
<li><strong>Cardiac Monitors:</strong> ECG dan arrhythmia detection</li>
</ul>

<h4>3. Hospital IoMT Systems</h4>
<ul>
<li><strong>Asset Tracking:</strong> Equipment dan medication location</li>
<li><strong>Environmental Monitoring:</strong> Temperature, humidity, air quality</li>
<li><strong>Patient Flow Systems:</strong> Bed management, queue optimization</li>
<li><strong>Smart Infusion Pumps:</strong> Medication delivery precision</li>
</ul>

<h3>Real-time Data Streaming:</h3>

<h4>Data Collection:</h4>
<ul>
<li><strong>Vital Signs:</strong> HR, BP, SpO2, temperature</li>
<li><strong>Biomarkers:</strong> Glucose, lactate, electrolytes</li>
<li><strong>Activity Data:</strong> Movement, exercise, sleep patterns</li>
<li><strong>Environmental:</strong> Air quality, allergens, UV exposure</li>
</ul>

<h4>Data Transmission Protocols:</h4>
<ul>
<li><strong>MQTT:</strong> Lightweight messaging protocol</li>
<li><strong>CoAP:</strong> Constrained Application Protocol</li>
<li><strong>HTTP/HTTPS:</strong> Standard web protocols</li>
<li><strong>HL7 FHIR:</strong> Healthcare data exchange standard</li>
</ul>

<h3>Implementasi IoMT di WebKhanza:</h3>

<h4>Patient Monitoring System:</h4>
<ul>
<li><strong>Central Dashboard:</strong> Real-time patient status overview</li>
<li><strong>Alert System:</strong> Automated notifications untuk critical values</li>
<li><strong>Trend Analysis:</strong> Historical data visualization</li>
<li><strong>Predictive Analytics:</strong> Early warning systems</li>
</ul>

<h4>Chronic Disease Management:</h4>
<ul>
<li><strong>Diabetes Care Program:</strong> Continuous glucose monitoring integration</li>
<li><strong>Hypertension Management:</strong> Home BP monitoring network</li>
<li><strong>Heart Failure Monitoring:</strong> Weight dan symptom tracking</li>
<li><strong>COPD Management:</strong> Spirometry dan medication adherence</li>
</ul>

<h3>Data Analytics & AI Integration:</h3>

<h4>Machine Learning Applications:</h4>
<ul>
<li><strong>Anomaly Detection:</strong> Identify unusual patterns</li>
<li><strong>Predictive Modeling:</strong> Health outcome predictions</li>
<li><strong>Risk Stratification:</strong> Patient priority classification</li>
<li><strong>Personalization:</strong> Individualized treatment recommendations</li>
</ul>

<h4>Real-time Processing:</h4>
<ul>
<li><strong>Stream Analytics:</strong> Continuous data processing</li>
<li><strong>Edge Computing:</strong> Local data processing untuk low latency</li>
<li><strong>Complex Event Processing:</strong> Multi-source data correlation</li>
<li><strong>Automated Response:</strong> Rule-based actions</li>
</ul>

<h3>Security & Privacy Framework:</h3>

<h4>Data Protection:</h4>
<ul>
<li><strong>End-to-End Encryption:</strong> Data in transit dan at rest</li>
<li><strong>Authentication:</strong> Multi-factor user verification</li>
<li><strong>Authorization:</strong> Role-based access control</li>
<li><strong>Audit Trails:</strong> Complete activity logging</li>
</ul>

<h4>Compliance Standards:</h4>
<ul>
<li><strong>HIPAA:</strong> Patient privacy protection</li>
<li><strong>GDPR:</strong> European data protection regulation</li>
<li><strong>FDA Regulations:</strong> Medical device compliance</li>
<li><strong>ISO 27001:</strong> Information security management</li>
</ul>

<h3>Interoperability Challenges:</h3>

<h4>Technical Integration:</h4>
<ul>
<li><strong>Device Compatibility:</strong> Multiple manufacturer integration</li>
<li><strong>Protocol Standardization:</strong> Communication standard alignment</li>
<li><strong>Data Format Harmonization:</strong> Consistent data structures</li>
<li><strong>Legacy System Integration:</strong> Existing infrastructure compatibility</li>
</ul>

<h4>Solutions Implemented:</h4>
<ul>
<li><strong>API Gateway:</strong> Unified integration platform</li>
<li><strong>Data Transformation:</strong> Format standardization layer</li>
<li><strong>Middleware Solutions:</strong> Communication facilitation</li>
<li><strong>Cloud Integration Platform:</strong> Scalable connectivity solutions</li>
</ul>

<h3>Clinical Benefits:</h3>

<h4>Patient Outcomes:</h4>
<ul>
<li><strong>Early Detection:</strong> Health deterioration prevention</li>
<li><strong>Medication Adherence:</strong> Improved compliance monitoring</li>
<li><strong>Personalized Care:</strong> Individual-specific treatment optimization</li>
<li><strong>Remote Care Access:</strong> Healthcare untuk remote locations</li>
</ul>

<h4>Operational Efficiency:</h4>
<ul>
<li><strong>Resource Optimization:</strong> Efficient staff dan equipment allocation</li>
<li><strong>Workflow Automation:</strong> Reduced manual processes</li>
<li><strong>Predictive Maintenance:</strong> Equipment downtime prevention</li>
<li><strong>Data-Driven Decisions:</strong> Evidence-based care protocols</li>
</ul>

<h3>Future Developments:</h3>

<h4>Emerging Technologies:</h4>
<ul>
<li><strong>5G Networks:</strong> Ultra-low latency communication</li>
<li><strong>Digital Twins:</strong> Virtual patient representations</li>
<li><strong>Blockchain:</strong> Secure data sharing</li>
<li><strong>Quantum Sensors:</strong> Ultra-sensitive medical measurements</li>
</ul>

<h4>Advanced Applications:</h4>
<ul>
<li><strong>Precision Medicine:</strong> Genomic data integration</li>
<li><strong>Population Health:</strong> Community health monitoring</li>
<li><strong>Pandemic Response:</strong> Disease outbreak tracking</li>
<li><strong>Mental Health:</strong> Behavioral pattern analysis</li>
</ul>

<p>IoMT represents the future of healthcare delivery, enabling proactive, personalized, dan precise medical care through the power of connected devices dan intelligent analytics.</p>

<h3>WebKhanza IoMT Roadmap:</h3>
<p>Kami committed untuk continuous expansion IoMT capabilities, dengan focus pada patient safety, clinical effectiveness, dan operational excellence dalam delivering connected healthcare solutions.</p>',
                'meta_title' => 'Internet of Medical Things (IoMT) - Ekosistem Kesehatan Terhubung',
                'meta_description' => 'Eksplorasi IoMT dan bagaimana perangkat medis terhubung mengubah delivery healthcare melalui real-time monitoring dan data analytics.',
                'meta_keywords' => ['IoMT', 'Internet of Medical Things', 'connected healthcare', 'remote monitoring', 'medical sensors']
            ]
        ];

        // Create blogs for each category (2 blogs per category)
        $blogIndex = 0;
        foreach ($categories as $category) {
            for ($i = 0; $i < 2; $i++) {
                if ($blogIndex >= count($blogContents)) {
                    break;
                }

                $content = $blogContents[$blogIndex];
                
                $slug = Str::slug($content['title']);
                
                $blog = Blog::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $content['title'],
                        'excerpt' => $content['excerpt'],
                        'content' => $content['content'],
                        'blog_category_id' => $category->id,
                        'user_id' => $user->id,
                        'status' => 'published',
                        'published_at' => now()->subDays(rand(1, 30)),
                        'is_featured' => rand(0, 1),
                        'allow_comments' => true,
                        'views_count' => rand(100, 2000),
                        'likes_count' => rand(10, 200),
                        'shares_count' => rand(5, 100),
                        'meta_title' => $content['meta_title'],
                        'meta_description' => $content['meta_description'],
                        'meta_keywords' => $content['meta_keywords'],
                        'reading_time' => rand(3, 8),
                        'sort_order' => $blogIndex + 1,
                    ]
                );
                
                if ($blog->wasRecentlyCreated) {
                    $this->command->info("✓ Created blog: {$blog->title}");
                } else {
                    $this->command->info("✓ Updated blog: {$blog->title}");
                }

                $blogIndex++;
            }
        }

        $this->command->info('Blog seeder completed successfully!');
        $this->command->info('Created: ' . ($blogIndex) . ' blog posts (2 per category)');
    }
}