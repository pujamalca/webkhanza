# Panduan Instalasi WebKhanza

Dokumen ini berisi panduan instalasi untuk WebKhanza, baik untuk fresh installation maupun migrasi dari database KHANZA desktop yang sudah ada.

## Daftar Isi

- [Requirements](#requirements)
- [Instalasi Baru (Fresh Install)](#instalasi-baru-fresh-install)
- [Instalasi dengan Database Existing](#instalasi-dengan-database-existing)
- [Post Installation](#post-installation)
- [Troubleshooting](#troubleshooting)

---

## Requirements

### Server Requirements

- PHP 8.2 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.3+
- Composer 2.x
- Node.js 16+ dan NPM
- Git

### PHP Extensions

Pastikan extension PHP berikut sudah aktif:

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- Filter
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_MySQL
- Tokenizer
- XML
- ZIP
```

Untuk mengecek di Laragon: klik Laragon > PHP > Quick settings > dan pastikan extension di atas ter-checklist.

---

## Instalasi Baru (Fresh Install)

Gunakan metode ini jika Anda **tidak memiliki** database KHANZA desktop sebelumnya.

### 1. Clone Repository

```bash
git clone <repository-url> webkhanza
cd webkhanza
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy file .env.example menjadi .env
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi:

```env
APP_NAME="WebKhanza"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Locale Settings
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID
APP_TIMEZONE=Asia/Jakarta

# Database Configuration
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webkhanza
DB_USERNAME=root
DB_PASSWORD=

# Cache Settings (gunakan file untuk development)
CACHE_STORE=file

# Session Settings
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Buat Database

Buat database baru melalui phpMyAdmin atau MySQL CLI:

```sql
CREATE DATABASE webkhanza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run Migrations

```bash
php artisan migrate
```

Perintah ini akan membuat semua tabel yang diperlukan untuk WebKhanza.

### 7. Run Seeders

```bash
php artisan db:seed
```

Seeder akan membuat:
- ✅ Role dan permissions (Super Admin, Admin, HRD Manager, dll)
- ✅ User admin default (email: admin@gmail.com, password: admin)
- ✅ Website identity dengan data contoh
- ✅ Blog categories dan sample posts
- ✅ Marketing categories
- ✅ Registration templates
- ✅ SOAPIE templates

### 8. Compile Assets

```bash
# Untuk production
npm run build

# Untuk development (dengan auto-reload)
npm run dev
```

### 9. Start Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

### 10. Login

Akses admin panel di `http://localhost:8000/admin`

**Default credentials:**
- Email: `admin@gmail.com`
- Password: `admin`

**⚠️ PENTING:** Segera ubah password default setelah login pertama kali!

---

## Instalasi dengan Database Existing

Gunakan metode ini jika Anda **sudah memiliki** database dari aplikasi desktop KHANZA.

### 1-4. Sama dengan Fresh Install

Ikuti langkah 1-4 dari [Instalasi Baru](#instalasi-baru-fresh-install)

### 5. Gunakan Database Existing

Edit file `.env` dan arahkan ke database KHANZA yang sudah ada:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sik        # Nama database KHANZA Anda
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Backup Database

**SANGAT PENTING!** Backup database Anda terlebih dahulu:

```bash
# Via mysqldump
mysqldump -u root -p sik > backup_sik_$(date +%Y%m%d).sql

# Atau via phpMyAdmin: Export > Go
```

### 7. Run Migrations (Aman untuk Database Existing)

Migration dirancang untuk **menambahkan** tabel/kolom baru tanpa merusak data existing:

```bash
php artisan migrate
```

**Yang akan dilakukan migration:**

✅ Menambahkan kolom baru ke tabel `users` (jika ada):
   - `device_token`, `device_info`
   - `is_logged_in`, `logged_in_at`
   - `last_login_at`, `last_login_ip`
   - `avatar_url`

✅ Membuat tabel baru untuk web app:
   - `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`
   - `activity_log`
   - `absents`, `cutis`
   - `website_identities`
   - `blogs`, `blog_categories`, `blog_tags`, `blog_blog_tag`
   - `marketing_categories`, `marketing_patient_tasks`
   - `bpjs_transfers`, `bpjs_transfer_tasks`
   - `registration_templates`
   - `soapie_templates`
   - `resep_templates`, `resep_template_details`
   - `cache`, `sessions`, `jobs`, `notifications`

✅ Menambahkan index ke tabel `pasien` (jika belum ada)

✅ Menambahkan kolom ke tabel `berkas_pegawai` (jika ada): `tgl_berakhir`

**Yang TIDAK akan dilakukan:**
- ❌ Menghapus tabel existing
- ❌ Mengubah struktur tabel KHANZA desktop
- ❌ Menghapus atau mengubah data existing

**Catatan:** Semua migration memiliki pengecekan `hasTable` dan `hasColumn` untuk memastikan keamanan.

### 8. Run Seeders

```bash
php artisan db:seed
```

**Seeder aman untuk database existing:**

✅ `RolePermissionSeeder` - Membuat/update roles dan permissions
✅ `AdminUserSeeder` - Membuat user admin (atau update jika sudah ada)
✅ `CoreDataSeeder` - Membuat/update website identity
✅ `BlogSeeder` - Membuat sample blog posts (opsional)

**Jika ada error pada seeder tertentu**, jalankan individual:

```bash
# Jalankan satu per satu
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=CoreDataSeeder
```

### 9-10. Sama dengan Fresh Install

Lanjutkan dengan langkah 8-10 dari [Instalasi Baru](#instalasi-baru-fresh-install)

---

## Post Installation

### 1. Konfigurasi Permission Cache

```bash
php artisan permission:cache-reset
php artisan optimize:clear
```

### 2. Setup Storage Link (jika upload files)

```bash
php artisan storage:link
```

### 3. Setup Cron Jobs (untuk production)

Tambahkan ke crontab:

```bash
* * * * * cd /path/to/webkhanza && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Setup Queue Worker (opsional)

Untuk background jobs:

```bash
# Install supervisor atau setup systemd service
php artisan queue:work --tries=3
```

### 5. Ubah Password Admin

Login ke admin panel dan segera ubah password default!

### 6. Konfigurasi Website Identity

1. Login ke admin panel
2. Buka menu **Website Management > Website Identity**
3. Update data sesuai rumah sakit/klinik Anda:
   - Nama
   - Tagline
   - Alamat
   - Kontak
   - Warna tema
   - Logo (jika ada)

### 7. Setup Roles & Permissions

Review dan sesuaikan permissions untuk setiap role di menu **Administrator > Roles**

---

## Troubleshooting

### Error: "Base table or view not found"

**Penyebab:** Migration belum dijalankan atau database tidak terhubung.

**Solusi:**

```bash
# Cek koneksi database
php artisan db:show

# Cek status migrations
php artisan migrate:status

# Jalankan ulang migrations yang pending
php artisan migrate
```

### Error: "SQLSTATE[42S01]: Table already exists"

**Penyebab:** Migration mencoba membuat tabel yang sudah ada (seharusnya tidak terjadi karena ada `hasTable` check).

**Solusi:**

```bash
# Cek migration mana yang error, lalu skip atau rollback
php artisan migrate:rollback --step=1

# Atau fresh migration (HATI-HATI: menghapus semua data!)
# JANGAN gunakan pada database production!
php artisan migrate:fresh --seed
```

### Error: "Class 'Spatie\Permission\Models\Role' not found"

**Penyebab:** Package spatie/laravel-permission belum terinstall.

**Solusi:**

```bash
composer install
php artisan config:clear
```

### Error: "Permission denied" pada storage/logs

**Penyebab:** Folder permission tidak sesuai (Linux/Mac).

**Solusi:**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Seeder Error: "Integrity constraint violation"

**Penyebab:** Relasi foreign key atau duplikat data.

**Solusi:**

```bash
# Reset permission cache
php artisan permission:cache-reset

# Jalankan seeder individual dengan --force
php artisan db:seed --class=RolePermissionSeeder --force
```

### Migration Sangat Lambat

**Penyebab:** Database besar atau query optimizer.

**Solusi:**

```bash
# Nonaktifkan SQL query logging sementara
# Edit .env
DEBUGBAR_ENABLED=false

# Clear cache
php artisan optimize:clear
```

### Error: "Activity log table doesn't exist" saat migration

**Penyebab:** SQL query tracker mencoba log sebelum tabel dibuat.

**Solusi:** Sudah diperbaiki dengan pengecekan di `SqlQueryTracker::track()`. Jika masih terjadi:

```bash
# Disable activity logging sementara
# Di AppServiceProvider, comment out baris:
# SqlQueryTracker::track();
```

### Lupa Password Admin

**Solusi:**

```bash
# Reset via tinker
php artisan tinker
>>> $admin = User::where('email', 'admin@gmail.com')->first();
>>> $admin->password = Hash::make('newpassword');
>>> $admin->save();
>>> exit
```

---

## Update .env.example

Setelah instalasi berhasil, pastikan file `.env.example` Anda sudah lengkap dengan semua konfigurasi yang diperlukan. File ini akan menjadi template untuk instalasi berikutnya.

**Checklist .env.example:**

- [x] APP_LOCALE, APP_TIMEZONE
- [x] DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE
- [x] CACHE_STORE (file untuk dev, database untuk prod)
- [x] SESSION_DRIVER (database)
- [x] BPJS API configuration (jika digunakan)

---

## Deployment Production

Untuk deploy ke production server:

1. **Set Environment:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize:**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   ```

3. **Security:**
   - Ubah semua password default
   - Setup HTTPS
   - Configure firewall
   - Setup backup otomatis
   - Enable audit logging

4. **Performance:**
   - Setup Redis untuk cache (opsional)
   - Configure queue worker
   - Setup CDN untuk assets (opsional)
   - Enable OPcache

---

## Support

Untuk bantuan lebih lanjut:

- 📧 Email: support@webkhanza.com
- 📚 Documentation: [Link to docs]
- 🐛 Issue Tracker: GitHub Issues

---

**Catatan Penting:**

- Selalu backup database sebelum migrasi atau update
- Test di environment development terlebih dahulu
- Jangan gunakan `migrate:fresh` pada database production
- Ubah semua password default setelah instalasi

**Selamat menggunakan WebKhanza! 🎉**
