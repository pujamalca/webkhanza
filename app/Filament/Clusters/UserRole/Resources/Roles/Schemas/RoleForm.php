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
            ]);
    }
}
