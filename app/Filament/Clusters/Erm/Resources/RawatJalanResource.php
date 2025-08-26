<?php

namespace App\Filament\Clusters\Erm\Resources;

use App\Filament\Clusters\Erm\Resources\RawatJalanResource\Pages;
use App\Filament\Clusters\ErmCluster;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Penjab;
use App\Models\Poliklinik;
use App\Models\RegPeriksa;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RawatJalanResource extends Resource
{
    protected static ?string $model = RegPeriksa::class;

    protected static ?string $cluster = ErmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Rawat Jalan';
    }

    public static function getModelLabel(): string
    {
        return 'Registrasi Rawat Jalan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Rawat Jalan';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('rawat_jalan_view');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('rawat_jalan_view_details');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('rawat_jalan_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('rawat_jalan_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('rawat_jalan_delete');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status_lanjut', 'Ralan');
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Registrasi')
                    ->schema([
                        TextInput::make('no_reg')
                            ->label('No. Registrasi')
                            ->required()
                            ->maxLength(3)
                            ->default(function (callable $get) {
                                $kd_poli = $get('kd_poli');
                                if ($kd_poli) {
                                    $maxReg = RegPeriksa::whereDate('tgl_registrasi', now())
                                        ->where('kd_poli', $kd_poli)
                                        ->max('no_reg');
                                    return str_pad(($maxReg ?? 0) + 1, 3, '0', STR_PAD_LEFT);
                                }
                                return '001';
                            })
                            ->readonly(),

                        TextInput::make('no_rawat')
                            ->label('No. Rawat')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(function () {
                                $today = now()->format('Y/m/d');
                                $count = RegPeriksa::count() + 1;
                                return $today . '/' . str_pad($count, 6, '0', STR_PAD_LEFT);
                            })
                            ->readonly(),

                        DatePicker::make('tgl_registrasi')
                            ->label('Tanggal Registrasi')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        TimePicker::make('jam_reg')
                            ->label('Jam Registrasi')
                            ->required()
                            ->default(now()->format('H:i:s'))
                            ->seconds(false),

                        Select::make('no_rkm_medis')
                            ->label('Pasien')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                // Minimum 3 characters to search
                                if (strlen($search) < 3) {
                                    return [];
                                }
                                
                                // Cache key for search results
                                $cacheKey = 'pasien_search_' . md5($search);
                                
                                return \Cache::remember($cacheKey, 300, function () use ($search) {
                                    // Optimize query with specific indexes
                                    $query = Pasien::select('no_rkm_medis', 'nm_pasien', 'no_ktp', 'no_peserta')
                                        ->where(function ($q) use ($search) {
                                            // Prioritize exact matches first
                                            $q->where('no_rkm_medis', '=', $search)
                                              ->orWhere('no_ktp', '=', $search)  
                                              ->orWhere('no_peserta', '=', $search);
                                        })
                                        ->orWhere(function ($q) use ($search) {
                                            // Then LIKE searches with optimized patterns
                                            if (is_numeric($search)) {
                                                // For numeric search, prioritize RM and card numbers
                                                $q->where('no_rkm_medis', 'like', $search . '%')
                                                  ->orWhere('no_ktp', 'like', '%' . $search . '%')
                                                  ->orWhere('no_peserta', 'like', '%' . $search . '%');
                                            } else {
                                                // For text search, focus on names
                                                $q->where('nm_pasien', 'like', $search . '%')
                                                  ->orWhere('nm_pasien', 'like', '% ' . $search . '%');
                                            }
                                        })
                                        ->orderByRaw("
                                            CASE 
                                                WHEN no_rkm_medis = ? THEN 1
                                                WHEN no_ktp = ? THEN 2  
                                                WHEN no_peserta = ? THEN 3
                                                WHEN no_rkm_medis LIKE ? THEN 4
                                                WHEN nm_pasien LIKE ? THEN 5
                                                ELSE 6 
                                            END
                                        ", [$search, $search, $search, $search . '%', $search . '%'])
                                        ->limit(20) // Reduced limit for faster response
                                        ->get();
                                        
                                    return $query->mapWithKeys(function ($pasien) {
                                        $label = $pasien->no_rkm_medis . ' - ' . $pasien->nm_pasien;
                                        if ($pasien->no_ktp) {
                                            $label .= ' (KTP: ' . $pasien->no_ktp . ')';
                                        }
                                        if ($pasien->no_peserta) {
                                            $label .= ' (BPJS: ' . $pasien->no_peserta . ')';
                                        }
                                        return [$pasien->no_rkm_medis => $label];
                                    })->toArray();
                                });
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                $pasien = Pasien::where('no_rkm_medis', $value)->first();
                                if (!$pasien) return null;
                                
                                $label = $pasien->no_rkm_medis . ' - ' . $pasien->nm_pasien;
                                if ($pasien->no_ktp) {
                                    $label .= ' (KTP: ' . $pasien->no_ktp . ')';
                                }
                                if ($pasien->no_peserta) {
                                    $label .= ' (BPJS: ' . $pasien->no_peserta . ')';
                                }
                                return $label;
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state) {
                                    $pasien = Pasien::where('no_rkm_medis', $state)->first();
                                    if ($pasien) {
                                        // Auto calculate umur - only years (no comma)
                                        if ($pasien->tgl_lahir) {
                                            $birthDate = \Carbon\Carbon::parse($pasien->tgl_lahir);
                                            $years = (int) $birthDate->diffInYears(\Carbon\Carbon::now());
                                            $set('umurdaftar', $years);
                                            $set('sttsumur', 'Th');
                                        }
                                        
                                        // Auto-fill data from pasien
                                        if ($pasien->kd_pj) {
                                            $set('kd_pj', $pasien->kd_pj);
                                        }
                                        
                                        if ($pasien->namakeluarga) {
                                            $set('p_jawab', $pasien->namakeluarga);
                                        }
                                        
                                        if ($pasien->alamatpj) {
                                            $set('almt_pj', $pasien->alamatpj);
                                        }
                                        
                                        if ($pasien->keluarga) {
                                            $set('hubunganpj', strtoupper($pasien->keluarga));
                                        }
                                        
                                        // Check if patient is new or old and set biaya_reg
                                        $existingRecords = RegPeriksa::where('no_rkm_medis', $pasien->no_rkm_medis)->count();
                                        $isNewPatient = $existingRecords === 0;
                                        $set('stts_daftar', $isNewPatient ? 'Baru' : 'Lama');
                                        
                                        // Set biaya_reg based on patient status and poliklinik
                                        $kd_poli = $get('kd_poli');
                                        if ($kd_poli) {
                                            $poliklinik = \App\Models\Poliklinik::find($kd_poli);
                                            if ($poliklinik) {
                                                // Use registrasi for new patient, registrasilama for existing patient
                                                $biaya = $isNewPatient ? $poliklinik->registrasi : $poliklinik->registrasilama;
                                                $set('biaya_reg', $biaya ?? 0);
                                            }
                                        }
                                    }
                                }
                            })
                            ->placeholder('Ketik No. RM, nama, NIK, atau no. BPJS untuk mencari')
                            ->helperText('Minimum 3 karakter - Cari berdasarkan: No. RM, Nama, NIK, atau No. Kartu BPJS')
                            ->searchDebounce(500)
                            ->createOptionForm([
                                TextInput::make('no_rkm_medis')
                                    ->label('No. Rekam Medis')
                                    ->required()
                                    ->unique('pasien', 'no_rkm_medis')
                                    ->maxLength(15),
                                    
                                TextInput::make('nm_pasien')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(40),
                                    
                                TextInput::make('no_ktp')
                                    ->label('No. KTP')
                                    ->maxLength(20),
                                    
                                Select::make('jk')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->required(),
                                    
                                TextInput::make('tmp_lahir')
                                    ->label('Tempat Lahir')
                                    ->maxLength(15),
                                    
                                DatePicker::make('tgl_lahir')
                                    ->label('Tanggal Lahir')
                                    ->maxDate(now()),
                                    
                                TextInput::make('alamat')
                                    ->label('Alamat')
                                    ->maxLength(200),
                                    
                                TextInput::make('no_tlp')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->maxLength(40),
                                    
                                TextInput::make('namakeluarga')
                                    ->label('Nama Penanggung Jawab')
                                    ->maxLength(50),
                                    
                                Select::make('kd_pj')
                                    ->label('Cara Bayar')
                                    ->relationship('penjab', 'png_jawab')
                                    ->searchable()
                                    ->required(),
                                    
                                DatePicker::make('tgl_daftar')
                                    ->label('Tanggal Daftar')
                                    ->default(now())
                                    ->required(),
                            ]),

                        Select::make('kd_poli')
                            ->label('Poliklinik')
                            ->relationship('poliklinik', 'nm_poli')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set, $get) {
                                $set('kd_dokter', null);
                                
                                // Update no_reg when poli changes
                                $kd_poli = $get('kd_poli');
                                if ($kd_poli) {
                                    $maxReg = RegPeriksa::whereDate('tgl_registrasi', now())
                                        ->where('kd_poli', $kd_poli)
                                        ->max('no_reg');
                                    $newRegNumber = ($maxReg ?? 0) + 1;
                                    $set('no_reg', str_pad($newRegNumber, 3, '0', STR_PAD_LEFT));
                                    
                                    // Get poliklinik data
                                    $poliklinik = \App\Models\Poliklinik::find($kd_poli);
                                    if ($poliklinik) {
                                        // Update biaya_reg based on patient status
                                        $no_rkm_medis = $get('no_rkm_medis');
                                        if ($no_rkm_medis) {
                                            // Patient selected - check if new or existing
                                            $existingRecords = RegPeriksa::where('no_rkm_medis', $no_rkm_medis)->count();
                                            $isNewPatient = $existingRecords === 0;
                                            $biaya = $isNewPatient ? $poliklinik->registrasi : $poliklinik->registrasilama;
                                        } else {
                                            // No patient selected yet - default to new patient rate
                                            $biaya = $poliklinik->registrasi;
                                        }
                                        $set('biaya_reg', $biaya ?? 0);
                                    }
                                }
                            })
                            ->createOptionForm([
                                TextInput::make('kd_poli')
                                    ->label('Kode Poliklinik')
                                    ->required()
                                    ->unique('poliklinik', 'kd_poli')
                                    ->maxLength(5)
                                    ->placeholder('Contoh: POLI, U-01, dll'),
                                TextInput::make('nm_poli')
                                    ->label('Nama Poliklinik')
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('registrasi')
                                    ->label('Tarif Registrasi Baru')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->required(),
                                TextInput::make('registrasilama')
                                    ->label('Tarif Registrasi Lama')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->required(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        '0' => 'Non Aktif',
                                        '1' => 'Aktif',
                                    ])
                                    ->default('1')
                                    ->required(),
                            ]),

                        Select::make('kd_dokter')
                            ->label('Dokter')
                            ->relationship('dokter', 'nm_dokter')
                            ->required()
                            ->createOptionForm([
                                Select::make('pegawai_id')
                                    ->label('Pilih Pegawai')
                                    ->options(\App\Models\Pegawai::where('stts_aktif', 'AKTIF')->pluck('nama', 'nik'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $pegawai = \App\Models\Pegawai::where('nik', $state)->first();
                                            if ($pegawai) {
                                                $set('kd_dokter', $pegawai->nik);
                                                $set('nm_dokter', $pegawai->nama);
                                                $set('jk', $pegawai->jk);
                                                $set('tmp_lahir', $pegawai->tmp_lahir);
                                                $set('tgl_lahir', $pegawai->tgl_lahir);
                                                $set('almt_tgl', $pegawai->alamat);
                                                $set('no_telp', '');
                                                $set('email', '');
                                            }
                                        }
                                    })
                                    ->helperText('Pilih pegawai untuk mengisi data dokter otomatis'),
                                TextInput::make('kd_dokter')
                                    ->label('Kode Dokter')
                                    ->required()
                                    ->unique('dokter', 'kd_dokter')
                                    ->maxLength(20)
                                    ->readonly()
                                    ->helperText('Kode akan diisi otomatis dari NIK pegawai'),
                                TextInput::make('nm_dokter')
                                    ->label('Nama Dokter')
                                    ->required()
                                    ->maxLength(50),
                                Select::make('jk')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->required(),
                                TextInput::make('tmp_lahir')
                                    ->label('Tempat Lahir')
                                    ->maxLength(20),
                                DatePicker::make('tgl_lahir')
                                    ->label('Tanggal Lahir'),
                                Select::make('gol_drh')
                                    ->label('Golongan Darah')
                                    ->options([
                                        'A' => 'A',
                                        'B' => 'B',
                                        'AB' => 'AB',
                                        'O' => 'O',
                                    ]),
                                Select::make('agama')
                                    ->label('Agama')
                                    ->options([
                                        'ISLAM' => 'Islam',
                                        'KRISTEN' => 'Kristen',
                                        'KATOLIK' => 'Katolik',
                                        'HINDU' => 'Hindu',
                                        'BUDHA' => 'Budha',
                                        'KONG HU CHU' => 'Kong Hu Chu',
                                        'LAIN-LAIN' => 'Lain-lain',
                                    ])
                                    ->default('ISLAM'),
                                TextInput::make('almt_tgl')
                                    ->label('Alamat Tinggal')
                                    ->maxLength(60),
                                TextInput::make('no_telp')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->maxLength(13),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(50),
                                Select::make('stts_nikah')
                                    ->label('Status Nikah')
                                    ->options([
                                        'BELUM MENIKAH' => 'Belum Menikah',
                                        'MENIKAH' => 'Menikah',
                                        'JANDA' => 'Janda',
                                        'DUDHA' => 'Dudha',
                                        'JANDA MATI' => 'Janda Mati',
                                        'DUDHA MATI' => 'Dudha Mati',
                                    ])
                                    ->default('BELUM MENIKAH'),
                                Select::make('kd_sps')
                                    ->label('Spesialis')
                                    ->relationship('spesialis', 'nm_sps')
                                    ->searchable()
                                    ->createOptionForm([
                                        TextInput::make('kd_sps')
                                            ->label('Kode Spesialis')
                                            ->required()
                                            ->unique('spesialis', 'kd_sps')
                                            ->maxLength(5),
                                        TextInput::make('nm_sps')
                                            ->label('Nama Spesialis')
                                            ->required()
                                            ->maxLength(30),
                                    ]),
                                TextInput::make('alumni')
                                    ->label('Alumni')
                                    ->maxLength(60),
                                TextInput::make('no_ijn_praktek')
                                    ->label('No. Ijin Praktek')
                                    ->maxLength(40),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        '0' => 'Non Aktif',
                                        '1' => 'Aktif',
                                    ])
                                    ->default('1')
                                    ->required(),
                            ]),

                        Select::make('kd_pj')
                            ->label('Cara Bayar')
                            ->relationship('penjab', 'png_jawab')
                            ->required()
                            ->createOptionForm([
                                TextInput::make('png_jawab')
                                    ->label('Nama Cara Bayar')
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('nama_perusahaan')
                                    ->label('Nama Perusahaan')
                                    ->maxLength(60),
                                TextInput::make('alamat_asuransi')
                                    ->label('Alamat Asuransi')
                                    ->maxLength(130),
                                TextInput::make('no_telp')
                                    ->label('No. Telepon')
                                    ->maxLength(40),
                            ]),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Data Penanggung Jawab')
                    ->schema([
                        TextInput::make('p_jawab')
                            ->label('Penanggung Jawab')
                            ->maxLength(30)
                            ->placeholder('Auto dari data pasien')
                            ->required(),

                        TextInput::make('almt_pj')
                            ->label('Alamat PJ')
                            ->maxLength(60)
                            ->placeholder('Auto dari data pasien'),

                        Select::make('hubunganpj')
                            ->label('Hubungan dengan Pasien')
                            ->options([
                                'AYAH' => 'Ayah',
                                'IBU' => 'Ibu',
                                'ISTRI' => 'Istri',
                                'SUAMI' => 'Suami',
                                'SAUDARA' => 'Saudara',
                                'ANAK' => 'Anak',
                                'MENANTU' => 'Menantu',
                                'CUCU' => 'Cucu',
                                'ORANGTUA' => 'Orang Tua',
                                'MERTUA' => 'Mertua',
                                'FAMILI' => 'Famili',
                                'SENDIRI' => 'Sendiri',
                                'LAIN-LAIN' => 'Lain-lain',
                            ])
                            ->default('AYAH')
                            ->placeholder('Auto dari data pasien'),
                    ])
                    ->columns(1)
                    ->hiddenOn(['create', 'edit']),

                Section::make('Status & Biaya')
                    ->schema([
                        TextInput::make('biaya_reg')
                            ->label('Biaya Registrasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Select::make('stts')
                            ->label('Status Periksa')
                            ->options([
                                'Belum' => 'Belum Periksa',
                                'Sudah' => 'Sudah Periksa',
                                'Batal' => 'Batal',
                            ])
                            ->default('Belum')
                            ->required(),

                        Select::make('stts_daftar')
                            ->label('Status Daftar')
                            ->options([
                                'Lama' => 'Pasien Lama',
                                'Baru' => 'Pasien Baru',
                            ])
                            ->default('Lama')
                            ->required(),

                        Hidden::make('status_lanjut')
                            ->default('Ralan'),

                        TextInput::make('umurdaftar')
                            ->label('Umur Daftar')
                            ->numeric()
                            ->maxLength(3)
                            ->placeholder('Auto dari pasien'),

                        Select::make('sttsumur')
                            ->label('Satuan Umur')
                            ->options([
                                'Th' => 'Tahun',
                                'Bl' => 'Bulan',
                                'Hr' => 'Hari',
                            ])
                            ->default('Th')
                            ->placeholder('Auto dari pasien'),

                        Select::make('status_bayar')
                            ->label('Status Bayar')
                            ->options([
                                'Belum Bayar' => 'Belum Bayar',
                                'Sudah Bayar' => 'Sudah Bayar',
                            ])
                            ->default('Belum Bayar'),

                        Select::make('status_poli')
                            ->label('Status Poli')
                            ->options([
                                'Lama' => 'Kunjungan Lama',
                                'Baru' => 'Kunjungan Baru',
                            ])
                            ->default('Lama'),
                    ])
                    ->columns(1)
                    ->hiddenOn(['create', 'edit']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('no_reg')
                    ->label('No. Reg')
                    ->sortable(),

                TextColumn::make('tgl_registrasi')
                    ->label('Tgl Registrasi')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('jam_reg')
                    ->label('Jam')
                    ->time('H:i'),

                TextColumn::make('pasien.no_rkm_medis')
                    ->label('No. RM')
                    ->searchable(),

                TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->weight('bold')
                    ->limit(30),

                TextColumn::make('pasien.jk')
                    ->label('JK')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'L' => 'blue',
                        'P' => 'pink',
                        default => 'gray',
                    }),

                TextColumn::make('poliklinik.nm_poli')
                    ->label('Poliklinik')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('penjab.png_jawab')
                    ->label('Cara Bayar')
                    ->badge()
                    ->color('success'),

                TextColumn::make('stts')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Belum' => 'warning',
                        'Sudah' => 'success',
                        'Batal' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('biaya_reg')
                    ->label('Biaya')
                    ->money('IDR')
                    ->alignEnd(),

                TextColumn::make('stts_daftar')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'success',
                        'Lama' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('kd_poli')
                    ->label('Poliklinik')
                    ->relationship('poliklinik', 'nm_poli')
                    ->searchable(),

                SelectFilter::make('kd_dokter')
                    ->label('Dokter')
                    ->relationship('dokter', 'nm_dokter')
                    ->searchable(),

                SelectFilter::make('kd_pj')
                    ->label('Cara Bayar')
                    ->relationship('penjab', 'png_jawab'),

                SelectFilter::make('stts')
                    ->label('Status Periksa')
                    ->options([
                        'Belum' => 'Belum Periksa',
                        'Sudah' => 'Sudah Periksa',
                        'Batal' => 'Batal',
                    ]),

                SelectFilter::make('stts_daftar')
                    ->label('Status Daftar')
                    ->options([
                        'Lama' => 'Pasien Lama',
                        'Baru' => 'Pasien Baru',
                    ]),

                Filter::make('tgl_registrasi')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('Dari Tanggal'),
                        DatePicker::make('to_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '>=', $date)
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from_date'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['from_date'])->format('d M Y');
                        }

                        if ($data['to_date'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['to_date'])->format('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('tgl_registrasi', 'desc')
            ->defaultSort('jam_reg', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRawatJalans::route('/'),
            'create' => Pages\CreateRawatJalan::route('/create'),
            'view' => Pages\ViewRawatJalan::route('/{record}'),
            'edit' => Pages\EditRawatJalan::route('/{record}/edit'),
        ];
    }
}