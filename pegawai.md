# Prompt untuk Laravel Filament 4 - Membuat Cluster Pegawai

Saya memiliki aplikasi Laravel dengan Filament 4 yang sudah berjalan. Saya ingin menambahkan fitur baru dengan struktur sebagai berikut:

## Requirement yang Dibutuhkan:

### 1. Cluster "Pegawai"
- Buat Cluster baru bernama "Pegawai" 
- Icon yang sesuai untuk cluster pegawai
- Navigasi group yang terorganisir

### 2. Resource "Absent" (Absensi)
- Model Absent dengan fields:
  - employee_id (relasi ke user/employee)
  - date (tanggal absen)
  - check_in (waktu masuk)
  - check_out (waktu keluar) 
  - check_in_photo (foto saat masuk - maksimal 500KB)
  - check_out_photo (foto saat keluar - maksimal 500KB)
  - status (hadir/tidak_hadir/terlambat/izin)
  - notes (catatan opsional)
- Resource Filament dengan form dan table yang sesuai
- **Photo Upload dengan validasi:**
  - Maksimal ukuran 500KB per foto
  - Format yang diterima: jpg, jpeg, png
  - Automatic resize jika diperlukan
  - Storage optimization
- Validasi yang diperlukan
- Filtering dan searching

### 3. Resource "Cuti" 
- Model Cuti dengan fields:
  - employee_id (relasi ke user/employee)
  - start_date (tanggal mulai cuti)
  - end_date (tanggal selesai cuti)
  - leave_type (jenis cuti: tahunan/sakit/darurat/dll)
  - reason (alasan cuti)
  - status (pending/approved/rejected)
  - approved_by (yang menyetujui)
  - approved_at (waktu persetujuan)
- Resource Filament dengan form dan table
- Status badge untuk approval
- Validasi tanggal dan logika bisnis

## Yang Perlu Dibuat:

1. **Migration files** untuk kedua tabel
2. **Model files** dengan relationship yang tepat
3. **Cluster file** untuk grouping
4. **Resource files** untuk Absent dan Cuti
5. **Seeder** (opsional) untuk data dummy

## Spesifikasi Teknis:

- Laravel versi terbaru
- Filament 4
- **Laravel Spatie Permission** sudah terinstall dan dikonfigurasi
- Menggunakan best practices Laravel
- Implementasi proper validation
- Responsive design
- Relasi database yang tepat

## Authorization & Security:

### Role & Permission (Spatie):
- **Employee Role**: Hanya bisa melihat data absensi dan cuti milik sendiri
- **HR/Manager Role**: Bisa melihat semua data absensi dan cuti
- **Admin Role**: Full access ke semua fitur

### Permission Structure:
- `view_own_absent` - Lihat absensi sendiri
- `view_all_absent` - Lihat semua absensi
- `create_absent` - Buat absensi
- `edit_absent` - Edit absensi
- `delete_absent` - Hapus absensi
- `view_own_cuti` - Lihat cuti sendiri
- `view_all_cuti` - Lihat semua cuti
- `create_cuti` - Buat pengajuan cuti
- `approve_cuti` - Approve/reject cuti
- `edit_cuti` - Edit cuti
- `delete_cuti` - Hapus cuti

### Data Filtering:
- User biasa hanya melihat data milik sendiri (berdasarkan employee_id)
- HR/Manager/Admin bisa melihat semua data
- Implementasi di Resource menggunakan `getEloquentQuery()` method

## Struktur File yang Diharapkan:

```
app/
├── Models/
│   ├── Absent.php
│   └── Cuti.php
├── Filament/
│   ├── Clusters/
│   │   └── Pegawai.php
│   └── Resources/
│       ├── AbsentResource.php
│       └── CutiResource.php
├── Policies/ (jika diperlukan)
│   ├── AbsentPolicy.php
│   └── CutiPolicy.php
database/
├── migrations/
│   ├── xxxx_create_absents_table.php
│   └── xxxx_create_cutis_table.php
storage/
├── app/
│   └── public/
│       └── absent-photos/ (untuk menyimpan foto absensi)
```

Tolong buatkan semua file yang diperlukan dengan kode lengkap, termasuk migration, model, cluster, dan resource files. Pastikan mengikuti konvensi Laravel dan Filament 4, serta implementasi yang clean dan maintainable.

**Catatan Tambahan:**
- Gunakan Bahasa Indonesia untuk label dan form
- **Implementasi Laravel Spatie Permission untuk authorization**
- **Photo upload dengan validasi maksimal 500KB**
- **Data filtering berdasarkan role user**
- Tambahkan validation rules yang sesuai
- Gunakan cast dan accessor/mutator jika diperlukan
- **Implementasi image optimization dan storage management**
- **Policy classes untuk fine-grained permission control**

**Requirements Khusus Photo:**
- Validasi ukuran maksimal 500KB
- Format yang diterima: jpg, jpeg, png, webp
- Automatic compression jika ukuran melebihi batas
- Preview foto di form dan table view
- Storage di folder `storage/app/public/absent-photos/`

**Requirements Authorization:**
- User biasa hanya akses data sendiri (employee_id = auth()->id())
- HR/Manager role bisa akses semua data
- Admin role full access
- Proper gate dan policy implementation