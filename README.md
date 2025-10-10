# WebKhanza - Sistem Informasi Kesehatan Terpadu

Platform terintegrasi untuk manajemen rumah sakit, klinik, dan fasilitas kesehatan dengan teknologi modern dan user-friendly interface.

## Fitur Utama

- **Electronic Medical Record (EMR)** - Manajemen data pasien, registrasi, dan rawat jalan
- **Manajemen SDM** - Pengelolaan pegawai, dokter, petugas, absensi, dan cuti
- **Marketing Management** - Kategori marketing, patient tasks, dan BPJS transfer
- **Website & Blog Management** - Kelola identitas website dan konten blog
- **Role & Permission Management** - Sistem keamanan berbasis role dan permission
- **Activity Logging** - Tracking semua aktivitas user
- **Multi-device Login Control** - Kontrol perangkat login user

## Tech Stack

- **Laravel 11** - PHP Framework
- **Filament 3** - Admin Panel Framework
- **MySQL/MariaDB** - Database
- **Spatie Packages** - Permission & Activity Log
- **Tailwind CSS** - Styling

## Instalasi

### Requirements

- PHP 8.2 atau lebih tinggi
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk kompilasi assets)

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd webkhanza
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   ```

   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mariadb
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=webkhanza
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Create database**

   Buat database baru dengan nama sesuai `DB_DATABASE` di file `.env`:
   ```sql
   CREATE DATABASE webkhanza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Run seeders**
   ```bash
   php artisan db:seed
   ```

   Seeder akan membuat:
   - Role dan permissions
   - User admin default (email: admin@gmail.com, password: admin)
   - Data website identity
   - Sample blog categories dan posts
   - Marketing categories
   - Registration templates

8. **Compile assets**
   ```bash
   npm run build
   # atau untuk development
   npm run dev
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

   Aplikasi akan berjalan di `http://localhost:8000`

10. **Login ke admin panel**

    URL: `http://localhost:8000/admin`
    - Email: `admin@gmail.com`
    - Password: `admin`

## Migrasi dari Database Desktop

Jika Anda sudah memiliki database dari aplikasi desktop KHANZA sebelumnya:

1. Import database desktop Anda terlebih dahulu
2. Jalankan migration untuk menambahkan tabel-tabel baru yang diperlukan web app:
   ```bash
   php artisan migrate
   ```
3. Migration dirancang untuk tidak merusak data existing dengan pengecekan `hasColumn` dan `hasTable`
4. Jalankan seeder untuk menambahkan data initial web app:
   ```bash
   php artisan db:seed
   ```

## Default User Credentials

Setelah seeding, Anda dapat login dengan:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@gmail.com | admin |

**PENTING**: Segera ubah password default setelah login pertama kali!

## Struktur Filament Clusters

Aplikasi menggunakan struktur cluster untuk organisasi menu:

- **Administrator** - User management, roles, permissions, activity logs
- **ERM (Electronic Medical Record)** - Pasien, registrasi, rawat jalan
- **SDM** - Pegawai, dokter, petugas, berkas, absensi, cuti
- **Marketing** - Marketing categories, patient tasks, BPJS transfer
- **Website Management** - Website identity, blog management
- **Pegawai Cluster** - Portal untuk pegawai (absensi & cuti mereka sendiri)

## Permission System

Aplikasi menggunakan Spatie Laravel Permission dengan role-based access control:

- **Super Admin** - Full access
- **Admin** - Hampir semua akses kecuali delete users/roles
- **HRD Manager** - Full access SDM cluster
- **Staff HRD** - Limited access SDM cluster
- **Supervisor** - View only SDM data
- **Manager** - View semua data, approve cuti
- **Marketing** - Marketing cluster & view pasien
- **Dokter** - ERM access & medical records
- **Perawat** - ERM access & vital signs
- **User** - View own data only

## Troubleshooting

### Migration Error

Jika ada error saat migration:

```bash
# Clear cache terlebih dahulu
php artisan config:clear
php artisan cache:clear

# Jalankan ulang migration
php artisan migrate:fresh --seed
```

**WARNING**: `migrate:fresh` akan menghapus semua data. Gunakan hanya untuk fresh installation!

### Seeder Error

Jika seeder tertentu error, jalankan individual:

```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=CoreDataSeeder
```

### Permission Cache

Jika permission tidak terdeteksi setelah seeding:

```bash
php artisan permission:cache-reset
php artisan optimize:clear
```

### Storage Permission (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

Project ini mengikuti PSR-12 coding standard.

### Git Workflow

1. Create feature branch dari `main`
2. Commit dengan pesan yang jelas dan deskriptif
3. Push dan create pull request
4. Review dan merge

## Database Schema

Migration files ada di `database/migrations/`:
- User authentication & device management
- Permission tables (Spatie)
- Activity log tables
- WebKhanza tables (compatibility dengan desktop app)
- New web features tables

## License

Proprietary - All rights reserved

## Support

Untuk bantuan dan dokumentasi lebih lanjut, hubungi tim development.

---

**Catatan**: README ini dibuat untuk memudahkan setup fresh installation. Pastikan semua langkah diikuti dengan urutan yang benar.
