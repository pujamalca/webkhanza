# Rawat Jalan Permission Seeder

## Deskripsi
Seeder ini membuat permission untuk semua tab di halaman Rawat Jalan (ViewRawatJalan).

## Permission yang Dibuat

| Permission Name | Deskripsi | Role yang Otomatis Mendapat Akses |
|----------------|-----------|-----------------------------------|
| `rawat_jalan_pemeriksaan_access` | Akses Tab Pemeriksaan Ralan | Super Admin, Admin, Dokter, Perawat |
| `rawat_jalan_input_tindakan_access` | Akses Tab Input Tindakan | Super Admin, Admin, Perawat |
| `rawat_jalan_diagnosa_access` | Akses Tab Diagnosa | Super Admin, Admin, Dokter |
| `rawat_jalan_catatan_access` | Akses Tab Catatan Pasien | Super Admin, Admin, Dokter, Perawat |
| `rawat_jalan_resep_access` | Akses Tab Resep Obat | Super Admin, Admin, Dokter |
| `rawat_jalan_labor_access` | Akses Tab Permintaan Labor | Super Admin, Admin |
| `rawat_jalan_resume_access` | Akses Tab Resume Pasien | Super Admin, Admin, Dokter |

## Cara Menjalankan

### Jalankan Seeder Ini Saja
```bash
php artisan db:seed --class=RawatJalanPermissionSeeder
```

### Jalankan Semua Seeder (termasuk ini)
```bash
php artisan db:seed
```

## Catatan Penting

1. **Safe to Re-run**: Seeder ini menggunakan `firstOrCreate()` sehingga aman dijalankan berulang kali tanpa duplikasi data.

2. **Role Assignment**: Seeder otomatis memberikan permission ke role yang sudah ada:
   - **Super Admin & Admin**: Mendapat semua akses
   - **Dokter**: Mendapat akses pemeriksaan, diagnosa, catatan, resep, dan resume
   - **Perawat**: Mendapat akses pemeriksaan, input tindakan, dan catatan

3. **ViewRawatJalan.php**: File ini sudah menggunakan `can()` method yang tidak throw exception jika permission belum ada, sehingga lebih aman.

## Troubleshooting

### Error: Permission already exists
- Tidak masalah, seeder akan skip permission yang sudah ada

### Role tidak mendapat permission otomatis
- Pastikan role sudah dibuat sebelum menjalankan seeder ini
- Atau berikan permission manual:
```bash
php artisan tinker
>>> $role = \Spatie\Permission\Models\Role::where('name', 'NamaRole')->first();
>>> $role->givePermissionTo('rawat_jalan_pemeriksaan_access');
```

## File Terkait

- **Seeder**: `database/seeders/RawatJalanPermissionSeeder.php`
- **Page**: `app/Filament/Resources/Erm/RawatJalanResource/Pages/ViewRawatJalan.php`
- **DatabaseSeeder**: `database/seeders/DatabaseSeeder.php` (sudah didaftarkan)

## Update History

- **2025-10-07**: Initial creation dengan 7 permission untuk tab Rawat Jalan
