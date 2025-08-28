<?php

namespace App\Filament\Clusters\UserRole\Resources\Users\Schemas;

use App\Models\Pegawai;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Pegawai Selection (only for create)
                Fieldset::make('Pilih Pegawai')
                    ->schema([
                        Select::make('id')
                            ->label('Pilih Pegawai')
                            ->options(function () {
                                return Pegawai::whereNotIn('nik', User::pluck('id'))
                                    ->where('stts_aktif', 'AKTIF')
                                    ->pluck('nama', 'nik');
                            })
                            ->searchable()
                            ->required(fn ($context) => $context === 'create')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $pegawai = Pegawai::where('nik', $state)->first();
                                    if ($pegawai) {
                                        $set('name', $pegawai->nama);
                                        $set('email', strtolower(str_replace(' ', '.', $pegawai->nama)) . '@hospital.com');
                                    }
                                }
                            })
                            ->visible(fn ($context) => $context === 'create'),
                    ])
                    ->visible(fn ($context) => $context === 'create'),

                // Basic User Info
                Fieldset::make('Informasi User')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn ($context) => $context === 'create'),
                        
                        TextInput::make('username')
                            ->label('Username')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->minLength(6)
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state)),
                    ]),

                // Role & Permission
                Fieldset::make('Role & Permission')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),

                // Device & Login Info
                Section::make('Informasi Login & Perangkat')
                    ->schema([
                        Toggle::make('is_logged_in')
                            ->label('Status Login')
                            ->disabled()
                            ->helperText('User sedang login atau tidak'),
                            
                        DateTimePicker::make('logged_in_at')
                            ->label('Waktu Login')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),
                            
                        DateTimePicker::make('last_login_at')
                            ->label('Login Terakhir')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),
                            
                        TextInput::make('last_login_ip')
                            ->label('IP Login Terakhir')
                            ->disabled(),
                            
                        Textarea::make('device_info')
                            ->label('Info Perangkat')
                            ->disabled()
                            ->rows(3)
                            ->formatStateUsing(function ($state) {
                                if ($state) {
                                    $info = json_decode($state, true);
                                    return "Browser: " . ($info['browser'] ?? 'Unknown') . "\n" .
                                           "OS: " . ($info['os'] ?? 'Unknown') . "\n" .
                                           "User Agent: " . ($info['user_agent'] ?? 'Unknown');
                                }
                                return 'Tidak ada informasi perangkat';
                            }),
                    ])
                    ->collapsible(),

                // Admin Info
                Section::make('Informasi Admin')
                    ->schema([
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Terverifikasi')
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
