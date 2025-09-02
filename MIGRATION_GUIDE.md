# 🛡️ Safe Migration Guide - Proteksi Data Aplikasi Desktop

## 📋 Overview

Sistem ini dirancang untuk melindungi data existing dari aplikasi desktop sambil memungkinkan Laravel mengelola tabel-tabelnya sendiri dengan aman.

## 🔒 Tabel yang Dilindungi

Semua tabel yang **TIDAK** ada dalam daftar `$laravelTables` akan **SELALU** dilindungi dan tidak akan pernah dihapus oleh Laravel.

## 📝 Tabel Laravel yang Dikelola

```php
'migrations', 'cache', 'cache_locks', 'sessions', 'jobs', 'job_batches', 
'failed_jobs', 'activity_log', 'permissions', 'roles', 'model_has_permissions', 
'model_has_roles', 'role_has_permissions', 'personal_access_tokens', 
'password_reset_tokens', 'absents', 'cutis'
```

## 🚀 Cara Penggunaan

### 1. Status Tabel
```bash
php artisan migrate:safe status
```
Menampilkan:
- ✅ Tabel Laravel yang sudah ada
- ❌ Tabel Laravel yang belum ada  
- 🛡️ Tabel desktop app yang dilindungi

### 2. Safe Fresh Migration
```bash
php artisan migrate:safe fresh
```
- Hanya menghapus tabel Laravel
- Membuat ulang semua tabel Laravel
- **TIDAK menyentuh tabel desktop app**

### 3. Safe Rollback
```bash
php artisan migrate:safe rollback
```
- Hanya rollback migrations Laravel
- **TIDAK menyentuh tabel desktop app**

### 4. Seeding Aman
```bash
php artisan db:seed --class=SafeDatabaseSeeder
```
- Hanya seed data untuk tabel Laravel
- **TIDAK menyentuh data desktop app**

## 🔧 Migration Standar (HATI-HATI!)

⚠️ **JANGAN GUNAKAN** command berikut jika ingin melindungi data desktop:

```bash
# ❌ BAHAYA - Akan hapus SEMUA tabel
php artisan migrate:fresh

# ❌ BAHAYA - Akan hapus SEMUA tabel  
php artisan migrate:fresh --seed

# ❌ BAHAYA - Bisa rollback tabel desktop
php artisan migrate:rollback
```

## 🛠️ Menambah Tabel Laravel Baru

Jika ingin menambah tabel baru yang dikelola Laravel:

1. Edit `$laravelTables` di `SafeMigrationCommand.php`
2. Edit `$laravelTables` di migration `create_laravel_tables_safe_migration.php`
3. Tambahkan tabel ke array tersebut

## 📊 Monitoring

Selalu cek status sebelum operasi migration:

```bash
php artisan migrate:safe status
```

Ini akan menunjukkan:
- Berapa tabel Laravel yang sudah ada
- Berapa tabel desktop yang dilindungi
- Status masing-masing tabel

## 🔐 Keamanan

- ✅ Data desktop app **SELALU** aman
- ✅ Hanya tabel Laravel yang bisa di-fresh/rollback
- ✅ Konfirmasi manual sebelum operasi
- ✅ Log detail setiap operasi

## 📞 Emergency Recovery

Jika terjadi masalah, data desktop app akan tetap utuh. Anda hanya perlu:

1. Jalankan `php artisan migrate:safe status`
2. Re-seed data Laravel: `php artisan db:seed --class=SafeDatabaseSeeder`

## 🎯 Best Practices

1. **SELALU** gunakan `migrate:safe` command
2. **SELALU** cek status sebelum migrasi
3. **BACKUP** database sebelum operasi besar
4. **TEST** di environment development dulu
5. **GUNAKAN** SafeDatabaseSeeder untuk seeding

---

**⚠️ INGAT: Data aplikasi desktop Anda AMAN selama menggunakan command `migrate:safe`**