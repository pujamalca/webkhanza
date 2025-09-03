# Website Identity Management

Dokumentasi untuk fitur Website Identity di WebKhanza Admin Panel.

## Overview

Fitur Website Identity memungkinkan administrator untuk mengelola informasi identitas dan branding website melalui admin panel Filament. Sistem ini menggunakan pattern singleton, yang berarti hanya ada satu set data identitas website.

## Fitur

- ✅ **Singleton Pattern**: Hanya satu record identitas website yang dapat dibuat
- ✅ **File Upload**: Upload logo dan favicon dengan preview
- ✅ **Validasi**: Validasi email, ukuran file, dan tipe file
- ✅ **Storage Management**: Auto cleanup file lama saat update
- ✅ **Permission Based**: Akses hanya untuk Administrator
- ✅ **Responsive UI**: Support dark mode dan mobile

## Struktur Data

### Database Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | ✅ | Nama website |
| `description` | text | ✅ | Deskripsi singkat website |
| `logo` | string | ❌ | Path file logo (nullable) |
| `favicon` | string | ❌ | Path file favicon (nullable) |
| `email` | string | ✅ | Email kontak |
| `phone` | string | ✅ | Nomor telepon |
| `address` | text | ✅ | Alamat lengkap |
| `tagline` | string | ✅ | Tagline atau motto |

### File Upload Specifications

**Logo:**
- Max size: 2MB
- Formats: JPG, PNG, WEBP
- Storage: `storage/app/public/uploads/website-identity/`

**Favicon:**
- Max size: 1MB  
- Formats: ICO, PNG, JPG
- Ideal size: 32x32px
- Aspect ratio: 1:1

## Akses & Permission

### Spatie Permission Based Access
Primary access control menggunakan permission:
- **`manage_website_identity`**: Permission untuk mengelola identitas website

### Role Based Access (Fallback)
Jika permission system error, akan fallback ke role check:
- **Super Admin**: Full access  
- **Admin**: Full access (jika ada di sistem)
- **Other roles**: No access

### Permission Assignment
Permission `manage_website_identity` otomatis assigned ke:
- ✅ **Super Admin** role
- ✅ **Admin** role (jika ditemukan)

### Setup Permission (First Time)
Jalankan seeder untuk membuat permission:
```bash
php artisan db:seed --class=WebsiteIdentityPermissionSeeder
```

### Manual Assignment
Untuk assign permission ke role lain:
```bash
php artisan tinker
$role = \Spatie\Permission\Models\Role::findByName('Role Name');
$role->givePermissionTo('manage_website_identity');
```

## Cara Penggunaan

### 1. Akses Menu
1. Login sebagai Administrator
2. Buka sidebar menu **Administrator**
3. Klik **Identitas Website**

### 2. First Time Setup
Jika belum ada data:
1. Akan diarahkan ke form create
2. Isi semua field yang required
3. Upload logo dan favicon (opsional)
4. Klik **Simpan**

### 3. Edit Data
Jika sudah ada data:
1. Akan langsung diarahkan ke form edit
2. Update data sesuai kebutuhan
3. File lama akan otomatis dihapus saat upload baru
4. Klik **Simpan**

## API Usage

### Get Instance
```php
use App\Models\WebsiteIdentity;

// Get singleton instance
$identity = WebsiteIdentity::getInstance();

// Access properties
echo $identity->name;
echo $identity->logo_url; // Full URL to logo
echo $identity->favicon_url; // Full URL to favicon
```

### Update Logo/Favicon
```php
// Update with file cleanup
$identity->updateLogo($newLogoPath);
$identity->updateFavicon($newFaviconPath);
```

## File Structure

```
app/
├── Filament/Clusters/Administrator/
│   ├── AdministratorCluster.php
│   └── Resources/
│       └── WebsiteIdentityResource/
│           ├── WebsiteIdentityResource.php
│           └── Pages/
│               ├── ListWebsiteIdentities.php
│               ├── CreateWebsiteIdentity.php
│               └── EditWebsiteIdentity.php
├── Models/
│   └── WebsiteIdentity.php
├── Policies/
│   └── WebsiteIdentityPolicy.php
└── ...

database/migrations/
└── 2025_09_03_193528_create_website_identities_table.php

storage/app/public/uploads/website-identity/
├── logo-files...
└── favicon-files...
```

## Security Features

1. **File Type Validation**: Hanya menerima image formats yang aman
2. **File Size Limit**: Logo max 2MB, favicon max 1MB
3. **Permission Based**: Policy melindungi akses resource
4. **Singleton Protection**: Mencegah duplikasi data
5. **Auto Cleanup**: File lama dihapus otomatis

## Troubleshooting

### Storage Link Error
```bash
php artisan storage:link
```

### Permission Issues
Pastikan user memiliki role Admin/Super Admin atau permission `manage_website_identity`.

### File Upload Error
1. Check file size (max 2MB untuk logo, 1MB untuk favicon)
2. Check file format (hanya image formats)
3. Check storage permissions

### Migration Error
```bash
php artisan migrate
```

## Customization

### Menambah Field Baru
1. Update migration
2. Update model `$fillable`
3. Update form schema di Resource
4. Update table columns jika perlu

### Mengubah File Upload Path
Update `directory('uploads/website-identity')` di form schema.

### Custom Validation
Override `mutateFormDataBeforeCreate/Save` di Pages.

---

**Catatan**: Sistem ini menggunakan pattern singleton untuk memastikan konsistensi identitas website. Jangan bypass pattern ini kecuali benar-benar diperlukan.