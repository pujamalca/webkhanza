<?php

namespace App\Filament\Clusters\UserRole\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Informasi Role')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Role')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Nama role akan digunakan dalam sistem permission'),
                            
                        TextInput::make('guard_name')
                            ->label('Guard Name')
                            ->default('web')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Biasanya "web" untuk aplikasi web'),
                    ]),

                // We don't need a hidden permissions field anymore

                Section::make('Dashboard & System')
                    ->description('Akses dasar sistem dan dashboard')
                    ->schema([
                        CheckboxList::make('dashboard_permissions')
                            ->label('Dashboard & System Permissions')
                            ->options(function () {
                                return Permission::whereIn('name', [
                                    'dashboard_access',
                                    'system_settings_access', 
                                    'system_logs_access'
                                ])->pluck('name', 'id')->map(function ($name) {
                                    return match($name) {
                                        'dashboard_access' => '📊 Dashboard - Akses ke dashboard utama',
                                        'system_settings_access' => '⚙️ System Settings - Akses pengaturan sistem',
                                        'system_logs_access' => '📋 System Logs - Akses log sistem',
                                        default => $name
                                    };
                                })->toArray();
                            })
                            ->columns(1)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('Administrator Cluster')
                    ->description('Manajemen pengguna dan role')
                    ->schema([
                        CheckboxList::make('admin_permissions')
                            ->options(function () {
                                return Permission::where('name', 'like', 'administrator_access')
                                    ->orWhere('name', 'like', 'users_%')
                                    ->orWhere('name', 'like', 'roles_%')
                                    ->orWhere('name', '=', 'multi_device_login')
                                    ->orWhere('name', '=', 'manage_website_identity')
                                    ->pluck('name', 'id')
                                    ->map(function ($name) {
                                        return match($name) {
                                            'administrator_access' => '🔐 Administrator - Akses ke cluster Administrator',
                                            'users_view' => '👁️ Users - Lihat daftar pengguna',
                                            'users_create' => '➕ Users - Buat pengguna baru',
                                            'users_edit' => '✏️ Users - Edit pengguna',
                                            'users_delete' => '🗑️ Users - Hapus pengguna',
                                            'users_reset_device' => '📱 Users - Reset perangkat pengguna',
                                            'roles_view' => '👁️ Roles - Lihat daftar role',
                                            'roles_create' => '➕ Roles - Buat role baru',
                                            'roles_edit' => '✏️ Roles - Edit role',
                                            'roles_delete' => '🗑️ Roles - Hapus role',
                                            'multi_device_login' => '📱 Multi Device - Login dari multiple perangkat',
                                            'manage_website_identity' => '🏢 Website Identity - Kelola identitas website',
                                            default => $name
                                        };
                                    })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('SDM Cluster')
                    ->description('Sumber Daya Manusia - Pegawai, Dokter, Petugas')
                    ->schema([
                        CheckboxList::make('sdm_permissions')
                            ->options(function () {
                                return Permission::where('name', 'like', 'sdm_access')
                                    ->orWhere('name', 'like', 'pegawai_%')
                                    ->orWhere('name', 'like', 'dokter_%')
                                    ->orWhere('name', 'like', 'petugas_%')
                                    ->orWhere('name', 'like', 'berkas_pegawai_%')
                                    ->pluck('name', 'id')
                                    ->map(function ($name) {
                                        return match($name) {
                                            'sdm_access' => '🏢 SDM - Akses ke cluster SDM',
                                            
                                            'pegawai_view' => '👁️ Pegawai - Lihat daftar pegawai',
                                            'pegawai_create' => '➕ Pegawai - Buat pegawai baru',
                                            'pegawai_edit' => '✏️ Pegawai - Edit data pegawai',
                                            'pegawai_delete' => '🗑️ Pegawai - Hapus pegawai',
                                            'pegawai_view_details' => '📄 Pegawai - Lihat detail pegawai',
                                            
                                            'dokter_view' => '👁️ Dokter - Lihat daftar dokter',
                                            'dokter_create' => '➕ Dokter - Buat dokter baru',
                                            'dokter_edit' => '✏️ Dokter - Edit data dokter',
                                            'dokter_delete' => '🗑️ Dokter - Hapus dokter',
                                            'dokter_view_details' => '📄 Dokter - Lihat detail dokter',
                                            
                                            'petugas_view' => '👁️ Petugas - Lihat daftar petugas',
                                            'petugas_create' => '➕ Petugas - Buat petugas baru',
                                            'petugas_edit' => '✏️ Petugas - Edit data petugas',
                                            'petugas_delete' => '🗑️ Petugas - Hapus petugas',
                                            'petugas_view_details' => '📄 Petugas - Lihat detail petugas',
                                            
                                            'berkas_pegawai_view' => '👁️ Berkas Pegawai - Lihat daftar berkas',
                                            'berkas_pegawai_create' => '➕ Berkas Pegawai - Upload berkas baru',
                                            'berkas_pegawai_edit' => '✏️ Berkas Pegawai - Edit berkas',
                                            'berkas_pegawai_delete' => '🗑️ Berkas Pegawai - Hapus berkas',
                                            'berkas_pegawai_download' => '💾 Berkas Pegawai - Download berkas',
                                            'berkas_pegawai_view_details' => '📄 Berkas Pegawai - Lihat detail berkas',
                                            
                                            default => $name
                                        };
                                    })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('ERM Cluster')
                    ->description('Electronic Medical Record - Registrasi, Rawat Jalan, Pasien')
                    ->schema([
                        CheckboxList::make('erm_permissions')
                            ->options(function () {
                                return Permission::where('name', 'like', 'erm_access')
                                    ->orWhere('name', 'like', 'registrasi_%')
                                    ->orWhere('name', 'like', 'registration_%')
                                    ->orWhere('name', 'like', 'rawat_jalan_%')
                                    ->orWhere('name', 'like', 'pasien_%')
                                    ->orWhere('name', '=', 'manage_all_examinations')
                                    ->orWhere('name', '=', 'manage_all_medical_notes')
                                    ->orWhere('name', '=', 'manage_all_input_tindakan')
                                    ->pluck('name', 'id')
                                    ->map(function ($name) {
                                        return match($name) {
                                            'erm_access' => '🏥 ERM - Akses ke cluster ERM',

                                            'registrasi_view' => '👁️ Registrasi - Lihat daftar registrasi',
                                            'registrasi_create' => '➕ Registrasi - Buat registrasi baru',
                                            'registrasi_edit' => '✏️ Registrasi - Edit data registrasi',
                                            'registrasi_delete' => '🗑️ Registrasi - Hapus registrasi',
                                            'registrasi_view_details' => '📄 Registrasi - Lihat detail registrasi',

                                            'registration_quick_access' => '⚡ Registrasi Cepat - Akses fitur registrasi cepat',
                                            'registration_template_manage' => '📋 Template Registrasi - Kelola template registrasi',

                                            'rawat_jalan_view' => '👁️ Rawat Jalan - Lihat daftar rawat jalan',
                                            'rawat_jalan_create' => '➕ Rawat Jalan - Buat registrasi rawat jalan baru',
                                            'rawat_jalan_edit' => '✏️ Rawat Jalan - Edit data rawat jalan',
                                            'rawat_jalan_delete' => '🗑️ Rawat Jalan - Hapus rawat jalan',
                                            'rawat_jalan_view_details' => '📄 Rawat Jalan - Lihat detail rawat jalan',

                                            'pasien_view' => '👁️ Pasien - Lihat daftar pasien',
                                            'pasien_create' => '➕ Pasien - Buat data pasien baru',
                                            'pasien_edit' => '✏️ Pasien - Edit data pasien',
                                            'pasien_delete' => '🗑️ Pasien - Hapus pasien',
                                            'pasien_view_details' => '📄 Pasien - Lihat detail pasien',

                                            'manage_all_examinations' => '🩺 Manage All Examinations - Kelola pemeriksaan atas nama petugas lain',
                                            'manage_all_medical_notes' => '📝 Manage All Medical Notes - Kelola catatan medis atas nama petugas lain',
                                            'manage_all_input_tindakan' => '🩹 Manage All Input Tindakan - Kelola input tindakan atas nama petugas lain',

                                            default => $name
                                        };
                                    })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('SOAPIE Template & TTV')
                    ->description('Template SOAPIE dan pengisian TTV dari pemeriksaan sebelumnya')
                    ->schema([
                        CheckboxList::make('soapie_permissions')
                            ->options(function () {
                                return Permission::where('name', 'like', '%soapie_templates%')
                                    ->orWhere('name', 'like', '%ttv%')
                                    ->pluck('name', 'id')
                                    ->map(function ($name) {
                                        return match($name) {
                                            'view_soapie_templates' => '👁️ SOAPIE Template - Melihat template SOAPIE',
                                            'create_soapie_templates' => '➕ SOAPIE Template - Membuat template SOAPIE',
                                            'edit_own_soapie_templates' => '✏️ SOAPIE Template - Mengedit template SOAPIE sendiri',
                                            'edit_all_soapie_templates' => '✏️ SOAPIE Template - Mengedit semua template SOAPIE',
                                            'delete_soapie_templates' => '🗑️ SOAPIE Template - Menghapus template SOAPIE',
                                            'create_public_soapie_templates' => '🌐 SOAPIE Template - Membuat template SOAPIE public',
                                            'fill_ttv_from_previous' => '📋 TTV - Mengisi TTV dari pemeriksaan sebelumnya',
                                            default => $name
                                        };
                                    })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('Pegawai')
                    ->description('Manajemen absensi dan cuti pegawai')
                    ->schema([
                        CheckboxList::make('pegawai_permissions')
                            ->options(function () {
                                return Permission::whereIn('name', [
                                    'view_own_absent', 'view_all_absent', 'create_absent', 'edit_absent', 'delete_absent',
                                    'view_own_cuti', 'view_all_cuti', 'create_cuti', 'approve_cuti', 'edit_cuti', 'delete_cuti'
                                ])->pluck('name', 'id')->map(function ($name) {
                                    return match($name) {
                                        'view_own_absent' => '👁️ Absensi - Lihat absensi sendiri',
                                        'view_all_absent' => '👁️ Absensi - Lihat semua absensi',
                                        'create_absent' => '➕ Absensi - Buat data absensi',
                                        'edit_absent' => '✏️ Absensi - Edit data absensi',
                                        'delete_absent' => '🗑️ Absensi - Hapus data absensi',
                                        
                                        'view_own_cuti' => '👁️ Cuti - Lihat cuti sendiri',
                                        'view_all_cuti' => '👁️ Cuti - Lihat semua pengajuan cuti',
                                        'create_cuti' => '➕ Cuti - Ajukan cuti baru',
                                        'approve_cuti' => '✅ Cuti - Setujui/tolak pengajuan cuti',
                                        'edit_cuti' => '✏️ Cuti - Edit data cuti',
                                        'delete_cuti' => '🗑️ Cuti - Hapus data cuti',
                                        
                                        default => $name
                                    };
                                })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('Master Data')
                    ->description('Pembuatan data master melalui dropdown')
                    ->schema([
                        CheckboxList::make('master_permissions')
                            ->options(function () {
                                return Permission::where('name', 'like', 'master_%')
                                    ->pluck('name', 'id')
                                    ->map(function ($name) {
                                        return match($name) {
                                            'master_bidang_create' => '🏗️ Bidang - Buat bidang baru melalui form',
                                            'master_departemen_create' => '🏗️ Departemen - Buat departemen baru melalui form',
                                            'master_jabatan_create' => '🏗️ Jabatan - Buat jabatan baru melalui form',
                                            'master_spesialis_create' => '🏗️ Spesialis - Buat spesialis baru melalui form',
                                            default => $name
                                        };
                                    })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('Marketing Cluster')
                    ->description('Manajemen marketing pasien dan transfer BPJS')
                    ->schema([
                        CheckboxList::make('marketing_permissions')
                            ->options(function () {
                                return Permission::where('name', 'like', 'marketing_%')
                                    ->orWhere('name', 'like', 'patient_marketing_%')
                                    ->orWhere('name', 'like', 'bpjs_transfer_%')
                                    ->pluck('name', 'id')
                                    ->map(function ($name) {
                                        return match($name) {
                                            'marketing_access' => '📢 Marketing - Akses ke cluster Marketing',
                                            
                                            'marketing_categories_view' => '👁️ Kategori Marketing - Lihat daftar kategori',
                                            'marketing_categories_create' => '➕ Kategori Marketing - Buat kategori baru',
                                            'marketing_categories_edit' => '✏️ Kategori Marketing - Edit kategori',
                                            'marketing_categories_delete' => '🗑️ Kategori Marketing - Hapus kategori',
                                            
                                            'patient_marketing_view' => '👁️ Patient Marketing - Lihat data pasien marketing',
                                            'patient_marketing_edit' => '✏️ Patient Marketing - Edit data pasien marketing',
                                            
                                            'bpjs_transfer_view' => '👁️ Pindah BPJS - Lihat daftar pindah BPJS',
                                            'bpjs_transfer_create' => '➕ Pindah BPJS - Buat data pindah BPJS baru',
                                            'bpjs_transfer_edit' => '✏️ Pindah BPJS - Edit data pindah BPJS',
                                            'bpjs_transfer_delete' => '🗑️ Pindah BPJS - Hapus data pindah BPJS',
                                            
                                            default => $name
                                        };
                                    })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Section::make('Website Management')
                    ->description('Manajemen identitas website dan blog')
                    ->schema([
                        CheckboxList::make('website_permissions')
                            ->options(function () {
                                return Permission::where('name', 'like', 'website_management_%')
                                    ->orWhere('name', 'like', '%website_identity')
                                    ->orWhere('name', 'like', 'blog_%')
                                    ->orWhere('name', '=', 'activity_logs_view')
                                    ->pluck('name', 'id')
                                    ->map(function ($name) {
                                        return match($name) {
                                            'website_management_access' => '🌐 Website Management - Akses ke website management',
                                            
                                            'view_any_website_identity' => '👁️ Identitas Website - Lihat daftar identitas',
                                            'view_website_identity' => '👁️ Identitas Website - Lihat detail identitas',
                                            'create_website_identity' => '➕ Identitas Website - Buat identitas baru',
                                            'update_website_identity' => '✏️ Identitas Website - Update identitas',
                                            
                                            'blog_management_access' => '📝 Blog Management - Akses ke blog management',
                                            'view_any_blog' => '👁️ Blog - Lihat daftar blog',
                                            'view_blog' => '👁️ Blog - Lihat detail blog',
                                            'create_blog' => '➕ Blog - Buat blog baru',
                                            'update_blog' => '✏️ Blog - Update blog',
                                            'delete_blog' => '🗑️ Blog - Hapus blog',
                                            'publish_blog' => '🚀 Blog - Publish blog',
                                            
                                            'view_any_blog_category' => '👁️ Kategori Blog - Lihat daftar kategori',
                                            'view_blog_category' => '👁️ Kategori Blog - Lihat detail kategori',
                                            'create_blog_category' => '➕ Kategori Blog - Buat kategori baru',
                                            'update_blog_category' => '✏️ Kategori Blog - Update kategori',
                                            'delete_blog_category' => '🗑️ Kategori Blog - Hapus kategori',
                                            
                                            'view_any_blog_tag' => '👁️ Tag Blog - Lihat daftar tag',
                                            'view_blog_tag' => '👁️ Tag Blog - Lihat detail tag',
                                            'create_blog_tag' => '➕ Tag Blog - Buat tag baru',
                                            'update_blog_tag' => '✏️ Tag Blog - Update tag',
                                            'delete_blog_tag' => '🗑️ Tag Blog - Hapus tag',
                                            
                                            'activity_logs_view' => '📋 Activity Logs - Lihat log aktivitas sistem',
                                            
                                            default => $name
                                        };
                                    })->toArray();
                            })
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),
            ]);
    }
}
