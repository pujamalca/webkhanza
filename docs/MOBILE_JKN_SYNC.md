# 📱 Service Mobile JKN ERM - Dokumentasi

Service ini menyediakan sinkronisasi otomatis data registrasi pasien ke sistem Mobile JKN BPJS, mirip dengan aplikasi desktop SIMRS Khanza.

## 🎯 Fitur Utama

### 1. **Sinkronisasi Otomatis**
- Otomatis mengirim data registrasi ke API Mobile JKN setiap 5 menit
- Hanya mengirim registrasi hari ini yang belum terkirim
- Mencegah duplikasi data dengan validasi composite key

### 2. **Manajemen Data (CRUD)**
- Menu admin di: **Administrator → Service JKN ERM**
- Create, Read, Update data referensi Mobile JKN
- Filter dan search data
- Tracking status kirim (Belum/Sudah)

### 3. **Tracking Task ID**
- Task 1: Checkin
- Task 2: Tunggu Poli
- Task 3: Mulai Periksa
- Task 4: Panggil Farmasi
- Task 5: Tunggu Obat
- Task 6: Obat Disiapkan
- Task 7: Obat Diserahkan
- Task 99: Selesai

## ⚙️ Konfigurasi

### 1. **Environment Variables**

Tambahkan ke file `.env`:

```env
# BPJS Mobile JKN Configuration
BPJS_MOBILE_JKN_URL=https://api-satusehat.kemkes.go.id
BPJS_API_KEY=your_api_key_here
BPJS_SECRET_KEY=your_secret_key_here
```

### 2. **Database Migration**

Jalankan migration untuk membuat tabel:

```bash
php artisan migrate
```

Tabel yang dibuat: `referensi_mobilejkn_bpjs`

## 🚀 Cara Penggunaan

### A. Manual Sync (Command)

#### 1. Sync Normal (Hanya data yang belum terkirim)
```bash
php artisan mobilejkn:sync
```

#### 2. Force Sync (Kirim ulang semua data)
```bash
php artisan mobilejkn:sync --force
```

#### Output:
```
🚀 Memulai sinkronisasi Mobile JKN...
📋 Ditemukan 15 registrasi hari ini
✅ Terkirim: 2025/10/06/001 - AHMAD ZAKI
✅ Terkirim: 2025/10/06/002 - SITI AMINAH
❌ Gagal: 2025/10/06/003 - API timeout

📊 Hasil Sinkronisasi:
+---------------------+--------+
| Status              | Jumlah |
+---------------------+--------+
| Berhasil Terkirim   | 10     |
| Sudah Ada (Updated) | 3      |
| Gagal               | 2      |
+---------------------+--------+
✨ Sinkronisasi selesai!
```

### B. Otomatis dengan Scheduler

Scheduler sudah dikonfigurasi untuk menjalankan sync setiap 5 menit.

#### Setup Scheduler di Server:

**Linux/Mac:**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Windows (Task Scheduler):**
- Buat task baru
- Trigger: Every 5 minutes
- Action: `php D:\laragon\www\webkhanza\artisan schedule:run`

#### Cek Scheduler:
```bash
php artisan schedule:list
```

#### Monitor Log:
```bash
tail -f storage/logs/mobilejkn-sync.log
```

### C. Manual via Admin Panel

1. Login ke admin panel
2. Menu: **Administrator → Service JKN ERM**
3. Klik **"Tambah Data"**
4. Isi form:
   - Data Registrasi (Tanggal, Jam, Pasien)
   - Data BPJS (No. Kartu, Poli, dll)
   - Task ID
   - Status Kirim
5. Simpan

## 📊 Struktur Data

### Tabel: `referensi_mobilejkn_bpjs`

| Field             | Type     | Description                           |
|-------------------|----------|---------------------------------------|
| tanggal_periksa   | string   | Tanggal registrasi (YYYY-MM-DD)       |
| jam_periksa       | time     | Jam registrasi                        |
| no_rkm_medis      | string   | No. Rekam Medis (Primary Key)        |
| no_rawat          | string   | No. Rawat Jalan                       |
| no_kartu          | string   | No. Kartu BPJS                        |
| kodepoli          | string   | Kode Poliklinik                       |
| nama_poli         | string   | Nama Poliklinik                       |
| nomor_referensi   | integer  | Nomor referensi                       |
| jenis_kunjungan   | integer  | 1=Sakit, 2=Sehat, 3=Konsul, 4=Rujuk |
| taskid            | integer  | Task ID aktif (0-99)                  |
| taskid1-7, 99     | datetime | Timestamp setiap task                 |
| status_kirim      | enum     | Belum/Sudah                           |

**Primary Key:** Composite (`tanggal_periksa` + `jam_periksa` + `no_rkm_medis`)

## 🔧 API Endpoint

### 1. Add Antrian
**Endpoint:** `POST /antrean/add`

**Payload:**
```json
{
  "kodebooking": "20251006POL001",
  "jenispasien": "JKN",
  "nomorkartu": "0001234567890",
  "nik": "3201010101010001",
  "nohp": "081234567890",
  "kodepoli": "POL",
  "namapoli": "POLI UMUM",
  "pasienbaru": 0,
  "norm": "000001",
  "tanggalperiksa": "2025-10-06",
  "kodedokter": "DR001",
  "namadokter": "dr. Ahmad",
  "jampraktek": "08:00",
  "jeniskunjungan": 1,
  "nomorreferensi": "",
  "nomorantrean": "001",
  "angkaantrean": 1,
  "estimasidilayani": 1728194400000,
  "sisakuotajkn": 50,
  "kuotajkn": 100,
  "sisakuotanonjkn": 50,
  "kuotanonjkn": 100,
  "keterangan": "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
}
```

### 2. Update Waktu
**Endpoint:** `POST /antrean/updatewaktu`

### 3. Batal Antrian
**Endpoint:** `POST /antrean/batal`

### 4. Farmasi Add
**Endpoint:** `POST /antrean/farmasi/add`

## 🔐 Keamanan

### Signature Generation

Command otomatis generate signature untuk setiap request:

```php
$timestamp = time();
$data = $apiKey . "&" . $timestamp;
$signature = hash_hmac('sha256', $data, $secretKey, false);
```

### Headers Required:
```
x-cons-id: YOUR_API_KEY
x-timestamp: 1728194400
x-signature: generated_signature
user_key: YOUR_SECRET_KEY
Content-Type: application/json
```

## 📝 Log dan Monitoring

### Log File Locations:
- **Sync Log:** `storage/logs/mobilejkn-sync.log`
- **Laravel Log:** `storage/logs/laravel.log`

### Monitoring Commands:
```bash
# Lihat log real-time
tail -f storage/logs/mobilejkn-sync.log

# Cek status scheduler
php artisan schedule:list

# Test command manual
php artisan mobilejkn:sync -v
```

## ⚠️ Troubleshooting

### 1. Tidak Ada Data Yang Terkirim
**Penyebab:**
- Tidak ada registrasi hari ini
- Status sudah "Sudah"
- API credentials salah

**Solusi:**
```bash
# Cek data registrasi hari ini
php artisan tinker
>>> RegPeriksa::whereDate('tgl_registrasi', today())->count();

# Force sync
php artisan mobilejkn:sync --force
```

### 2. Error API Timeout
**Penyebab:**
- Server BPJS lambat/down
- Network issue

**Solusi:**
- Cek koneksi internet
- Coba lagi nanti
- Cek log: `storage/logs/mobilejkn-sync.log`

### 3. Signature Invalid
**Penyebab:**
- API Key/Secret Key salah
- Timestamp tidak sinkron

**Solusi:**
```bash
# Cek konfigurasi
php artisan tinker
>>> config('services.bpjs.api_key');
>>> config('services.bpjs.secret_key');

# Update .env
nano .env
```

## 🎓 Best Practices

1. **Pastikan Scheduler Berjalan:**
   - Setup cron job di server
   - Monitor log secara berkala

2. **Backup Data:**
   - Backup tabel `referensi_mobilejkn_bpjs`
   - Simpan log untuk audit

3. **Test di Development:**
   - Gunakan API staging/development dulu
   - Test dengan data dummy

4. **Monitor Performance:**
   - Cek jumlah data yang dikirim
   - Monitor response time API

## 📞 Support

Jika ada pertanyaan atau issue:
- Cek log file
- Review dokumentasi BPJS
- Contact developer

## 📚 Referensi

- [SIMRS Khanza GitHub](https://github.com/mas-elkhanza/SIMRS-Khanza)
- [Laravel Scheduler](https://laravel.com/docs/scheduling)
- [BPJS API Documentation](https://dvlp.bpjs-kesehatan.go.id/)
