# 🔐 Session Management & Admin Reset Actions

## 📋 Overview
Sistem manajemen session dengan fitur reset manual untuk admin ketika user mengalami masalah login atau stuck session.

## 🎯 Features yang Tersedia

### 1. **Admin Reset Actions di Users Table**
Admin dapat mengakses actions di: **Admin Panel → User Role → Manajemen User**

#### A. **Reset Session (Individual)**
- **Lokasi**: Action menu di setiap row user
- **Kapan muncul**: User dengan status login aktif (green check)
- **Fungsi**: Reset session user tertentu
- **Yang dilakukan**:
  - Fire logout event (trigger database update)
  - Hapus semua sessions user dari database
  - Set `is_logged_in = false`, `device_token = null`, `logged_in_at = null`
  - Log admin action untuk audit

#### B. **Logout Semua User**
- **Lokasi**: Header Actions (tombol di atas table)
- **Kapan muncul**: Ketika ada minimal 1 user yang sedang login
- **Fungsi**: Force logout SEMUA user yang sedang login
- **⚠️ PERHATIAN**: Action ini akan logout semua user di sistem!

#### C. **Reset Session Terpilih (Bulk)**
- **Lokasi**: Bulk Actions (centang multiple users)
- **Fungsi**: Reset session untuk multiple users sekaligus
- **Cara pakai**: 
  1. Centang users yang ingin di-reset
  2. Klik "Reset Session Terpilih" di bulk actions

### 2. **Session Status Monitoring**
Table Users menampilkan informasi lengkap:
- **Status Login**: Icon hijau (login) / abu-abu (logout)
- **Login Aktif**: Waktu terakhir login
- **Perangkat**: Browser dan OS info
- **IP Terakhir**: IP address login terakhir

## 🛠️ Technical Implementation

### Event-Driven System
Semua reset actions menggunakan Laravel Event System:
```php
// Fire logout event untuk consistency
if ($user->is_logged_in) {
    event(new \Illuminate\Auth\Events\Logout('web', $user));
}

// Cleanup sessions
\DB::table('sessions')->where('user_id', $user->id)->delete();

// Update database
$user->setLoggedOut();
```

### Logging & Audit Trail
Semua admin actions tercatat di logs:
- Admin ID dan nama yang melakukan action
- Target user yang di-reset
- Before/after state comparison
- Timestamp lengkap

## 📖 Cara Menggunakan

### Scenario 1: User Tidak Bisa Login (Stuck Session)
1. Admin buka **Admin Panel → User Role → Manajemen User**
2. Cari user bermasalah
3. Lihat kolom "Status Login" - jika masih hijau (login) padahal user bilang logout
4. Klik **Actions → Reset Session**
5. Konfirmasi modal dialog
6. User bisa login kembali dari perangkat baru

### Scenario 2: Reset Multiple Users
1. Buka **Manajemen User**
2. **Centang** users yang ingin di-reset
3. Klik **Reset Session Terpilih** di bulk actions toolbar
4. Konfirmasi bulk reset

### Scenario 3: Emergency - Logout Semua User
1. Buka **Manajemen User** 
2. Klik tombol **"Logout Semua User"** di header (atas table)
3. Konfirmasi dengan hati-hati (akan show jumlah user yang akan di-logout)
4. Semua user akan ter-logout paksa

## 🔍 Troubleshooting

### User Masih Stuck Setelah Reset?
1. Check logs: `tail -f storage/logs/laravel.log | grep "ADMIN RESET"`
2. Verify database: User `is_logged_in = false`
3. Clear browser cache/cookies di sisi user
4. Restart user's browser completely

### Reset Action Tidak Muncul?
- User harus memiliki permission `users_edit`
- Action "Reset Session" hanya muncul untuk user dengan `is_logged_in = true` atau `device_token != null`

### Performance untuk Banyak User?
- Bulk reset dioptimasi untuk handle multiple users
- Actions menggunakan single query untuk efficiency
- Log aggregation untuk reduce I/O

## 📊 Monitoring Commands

### Real-time Session Monitoring
```bash
php artisan session:monitor --watch
```

### Production Testing
```bash  
php artisan session:test-production --monitor
```

### Log Monitoring
```bash
tail -f storage/logs/laravel.log | grep -E "(ADMIN RESET|LOGOUT EVENT)"
```

## 🎉 Summary

**✅ WORKING FEATURES:**
- ✅ Admin Reset Session (Individual & Bulk)
- ✅ Force Logout All Users  
- ✅ Event-driven database updates
- ✅ Comprehensive logging & audit
- ✅ Real-time session monitoring
- ✅ User-friendly confirmations & notifications

**📱 USER INTERFACE:**
- Clear visual indicators (green/gray status icons)
- Informative modal dialogs
- Success/error notifications  
- Responsive bulk operations

**🔒 SECURITY:**
- Permission-based access (`users_edit`)
- Admin action logging for audit trail
- Safe confirmation dialogs
- Proper event system integration

## 🤖 Automatic Session Cleanup

### Scheduled Cleanup Command
System menjalankan automatic cleanup setiap 5 menit menggunakan **exact same logic** seperti tombol "Reset Session":

```bash
# Manual cleanup
php artisan sessions:cleanup

# Dry-run (preview what would be cleaned)
php artisan sessions:cleanup --dry-run
```

### Monitoring Commands
```bash
# Check current state
php artisan sessions:monitor-cleanup

# Real-time monitoring
php artisan sessions:monitor-cleanup --watch

# View cleanup statistics
php artisan sessions:monitor-cleanup --stats
```

### Automatic Cleanup Process
1. **Detection**: Find users dengan `logged_in_at` > session.lifetime
2. **Logout Event**: Fire `Logout` event (same as manual reset)
3. **Database Update**: Call `setLoggedOut()` method
4. **Session Cleanup**: Delete database sessions
5. **Logging**: Complete audit trail

### Schedule Configuration
- **Frequency**: Every 5 minutes
- **Prevention**: `withoutOverlapping()` prevents concurrent runs
- **Logging**: Output saved to `storage/logs/session-cleanup.log`
- **Server**: `onOneServer()` untuk multi-server setups

---

## 👑 Multi-Device Login Permission System

### **🎯 Permission-Based Approach (IDEAL SOLUTION)**
Sistem yang paling ideal menggunakan **permission-based control** melalui role & permission interface yang sudah ada:

### **🔓 Multi-Device Benefits:**
- ✅ **Multi-Device Login**: Dapat login dari multiple perangkat bersamaan
- ✅ **No Device Conflicts**: Tidak ada "logout dari perangkat lain" 
- ✅ **Emergency Access**: Tetap bisa akses dari perangkat berbeda saat emergency
- ✅ **Management Flexibility**: Bisa manage users sambil login di tempat lain

### **🎛️ How It Works:**
Users dengan permission `multi_device_login` akan **skip** single device login restriction:

```php
// Simple permission-based check in middleware
private function isAdminUser($user): bool
{
    return $user->can('multi_device_login');
}
```

### **⚙️ Permission Management (Admin UI):**

1. **Buka Admin Panel** → **User Role** → **Permissions**
2. **Find Permission**: `multi_device_login` 
3. **Assign to Role**: Berikan permission ke role yang diinginkan
4. **Assign to User**: Atau berikan langsung ke user tertentu

**Example Scenarios:**
- ✅ **Super Admin Role** → `multi_device_login` → Multi-device allowed
- ✅ **IT Manager Role** → `multi_device_login` → Multi-device allowed  
- ❌ **Regular Staff Role** → No permission → Single device only
- ✅ **Specific User** → Direct permission → Multi-device allowed

### **🔧 Technical Implementation:**
Permission dibuat melalui migration:
```php
// Migration creates the permission
Permission::create([
    'name' => 'multi_device_login',
    'guard_name' => 'web'
]);

// Auto-assigned to Super Admin role
$superAdminRole->givePermissionTo('multi_device_login');
```

### **🔒 Regular User Enforcement:**
Non-admin users tetap mendapat single device restriction:
- ❌ Hanya bisa login dari 1 device
- ❌ Login baru = logout otomatis dari device lama
- ✅ Device token validation
- ✅ Session security maintained

---

## 🎯 Complete Solution Summary

**✅ MANUAL CONTROLS (Admin UI):**
- Individual Reset Session (per user)
- Bulk Reset Session (multiple users)  
- Emergency Logout All Users (header button)

**✅ AUTOMATIC SYSTEM:**
- Scheduled cleanup every 5 minutes
- Uses identical logic as manual reset
- Complete monitoring and statistics
- Audit logging for compliance

**✅ MULTI-DEVICE PERMISSION SYSTEM:**
- Permission-based multi-device control (`multi_device_login`)
- Flexible role & permission management through Admin UI
- Granular access control per user or role
- No hardcoded detection logic

**🚀 SOLUSI FINAL:** Admin punya **full control** dengan manual actions DAN automatic system yang reliable untuk session management!