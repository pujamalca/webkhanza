<?php

namespace App\Filament\Clusters\Erm\Resources;

use App\Filament\Clusters\Erm\Resources\RegistrasiResource\Pages;
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
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Closure;

class RegistrasiResource extends Resource
{
    protected static ?string $model = RegPeriksa::class;

    protected static ?string $cluster = ErmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Registrasi';
    }

    public static function getModelLabel(): string
    {
        return 'Registrasi Pasien';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Registrasi';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('registrasi_view');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('registrasi_view_details');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('registrasi_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('registrasi_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('registrasi_delete');
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
                                if (strlen($search) < 3) {
                                    return [];
                                }
                                
                                $cacheKey = 'pasien_search_' . md5($search);
                                
                                return \Cache::remember($cacheKey, 300, function () use ($search) {
                                    $query = Pasien::select('no_rkm_medis', 'nm_pasien', 'no_ktp', 'no_peserta')
                                        ->where(function ($q) use ($search) {
                                            $q->where('no_rkm_medis', '=', $search)
                                              ->orWhere('no_ktp', '=', $search)  
                                              ->orWhere('no_peserta', '=', $search);
                                        })
                                        ->orWhere(function ($q) use ($search) {
                                            if (is_numeric($search)) {
                                                $q->where('no_rkm_medis', 'like', $search . '%')
                                                  ->orWhere('no_ktp', 'like', '%' . $search . '%')
                                                  ->orWhere('no_peserta', 'like', '%' . $search . '%');
                                            } else {
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
                                        ->limit(20)
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
                                        if ($pasien->tgl_lahir) {
                                            $birthDate = \Carbon\Carbon::parse($pasien->tgl_lahir);
                                            $years = (int) $birthDate->diffInYears(\Carbon\Carbon::now());
                                            $set('umurdaftar', $years);
                                            $set('sttsumur', 'Th');
                                        }
                                        
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
                                        
                                        $existingRecords = RegPeriksa::where('no_rkm_medis', $pasien->no_rkm_medis)->count();
                                        $isNewPatient = $existingRecords === 0;
                                        $set('stts_daftar', $isNewPatient ? 'Baru' : 'Lama');
                                        
                                        $kd_poli = $get('kd_poli');
                                        if ($kd_poli) {
                                            $poliklinik = \App\Models\Poliklinik::find($kd_poli);
                                            if ($poliklinik) {
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
                                
                                $kd_poli = $get('kd_poli');
                                if ($kd_poli) {
                                    $maxReg = RegPeriksa::whereDate('tgl_registrasi', now())
                                        ->where('kd_poli', $kd_poli)
                                        ->max('no_reg');
                                    $newRegNumber = ($maxReg ?? 0) + 1;
                                    $set('no_reg', str_pad($newRegNumber, 3, '0', STR_PAD_LEFT));
                                    
                                    $poliklinik = \App\Models\Poliklinik::find($kd_poli);
                                    if ($poliklinik) {
                                        $no_rkm_medis = $get('no_rkm_medis');
                                        if ($no_rkm_medis) {
                                            $existingRecords = RegPeriksa::where('no_rkm_medis', $no_rkm_medis)->count();
                                            $isNewPatient = $existingRecords === 0;
                                            $biaya = $isNewPatient ? $poliklinik->registrasi : $poliklinik->registrasilama;
                                        } else {
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
                                    ->options(function () {
                                        return \App\Models\Pegawai::select('nik', 'nama', 'jk', 'tmp_lahir', 'tgl_lahir', 'alamat')
                                            ->get()
                                            ->mapWithKeys(function ($pegawai) {
                                                $label = $pegawai->nama . ' - ' . $pegawai->nik;
                                                if ($pegawai->jk) $label .= ' (' . $pegawai->jk . ')';
                                                return [$pegawai->nik => $label];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->placeholder('Pilih pegawai...')
                                    ->helperText('Pilih pegawai untuk mengisi data dokter otomatis')
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
                                            }
                                        }
                                    })
                                    ->columnSpan(3),
                                    
                                TextInput::make('kd_dokter')
                                    ->label('Kode Dokter')
                                    ->required()
                                    ->unique('dokter', 'kd_dokter')
                                    ->maxLength(20)
                                    ->readonly()
                                    ->helperText('Auto dari NIK pegawai'),
                                    
                                TextInput::make('nm_dokter')
                                    ->label('Nama Dokter')
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->columns(3),

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

                        Select::make('status_lanjut')
                            ->label('Status Lanjut')
                            ->options([
                                'Ralan' => 'Rawat Jalan',
                                'Ranap' => 'Rawat Inap',
                            ])
                            ->required()
                            ->default('Ralan'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Hidden::make('p_jawab')
                    ->default(function (callable $get) {
                        $no_rkm_medis = $get('no_rkm_medis');
                        if ($no_rkm_medis) {
                            $pasien = \App\Models\Pasien::where('no_rkm_medis', $no_rkm_medis)->first();
                            return $pasien?->namakeluarga ?? 'SENDIRI';
                        }
                        return 'SENDIRI';
                    }),

                Hidden::make('almt_pj')
                    ->default(function (callable $get) {
                        $no_rkm_medis = $get('no_rkm_medis');
                        if ($no_rkm_medis) {
                            $pasien = \App\Models\Pasien::where('no_rkm_medis', $no_rkm_medis)->first();
                            return $pasien?->alamatpj ?? $pasien?->alamat ?? '-';
                        }
                        return '-';
                    }),

                Hidden::make('hubunganpj')
                    ->default(function (callable $get) {
                        $no_rkm_medis = $get('no_rkm_medis');
                        if ($no_rkm_medis) {
                            $pasien = \App\Models\Pasien::where('no_rkm_medis', $no_rkm_medis)->first();
                            return strtoupper($pasien?->keluarga ?? 'SENDIRI');
                        }
                        return 'SENDIRI';
                    }),

                Hidden::make('biaya_reg')
                    ->default(0),

                Hidden::make('stts')
                    ->default('Belum'),

                Hidden::make('umurdaftar')
                    ->default(0),

                Hidden::make('sttsumur')
                    ->default('Th'),

                Hidden::make('status_bayar')
                    ->default('Belum Bayar'),

                Hidden::make('status_poli')
                    ->default('Baru'),
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
                    ->label('Tanggal & Jam Registrasi')
                    ->formatStateUsing(function ($record) {
                        $date = \Carbon\Carbon::parse($record->tgl_registrasi)->format('d/m/Y');
                        $time = \Carbon\Carbon::parse($record->jam_reg)->format('H:i');
                        return $date . ' - ' . $time;
                    })
                    ->sortable(),

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

                TextColumn::make('status_lanjut')
                    ->label('Status Lanjut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ralan' => 'info',
                        'Ranap' => 'warning',
                        default => 'gray',
                    }),

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
                    ->label('Baru/Lama')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'success',
                        'Lama' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state === '-' ? 'Lama' : $state),

                TextColumn::make('pasien.no_ktp')
                    ->label('No. KTP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('No. KTP berhasil disalin!')
                    ->copyMessageDuration(2000)
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('pasien.no_peserta')
                    ->label('No. BPJS')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('No. BPJS berhasil disalin!')
                    ->copyMessageDuration(2000)
                    ->placeholder('-')
                    ->toggleable(),
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

                SelectFilter::make('status_lanjut')
                    ->label('Status Lanjut')
                    ->options([
                        'Ralan' => 'Rawat Jalan',
                        'Ranap' => 'Rawat Inap',
                    ]),

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
            ->recordActions([
                ActionGroup::make([
                    \Filament\Actions\ViewAction::make(),
                    \Filament\Actions\EditAction::make(),
                    \Filament\Actions\DeleteAction::make(),
                ])
                ->label('Menu')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ], position: RecordActionsPosition::BeforeColumns)
            ->defaultSort('tgl_registrasi', 'desc')
            ->defaultSort('jam_reg', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function resolveRecordRouteBinding(int | string $key, ?\Closure $modifyQuery = null): ?Model
    {
        $decodedKey = base64_decode($key);
        
        $query = static::getModel()::where('no_rawat', $decodedKey);
        
        if ($modifyQuery) {
            $query = $modifyQuery($query) ?: $query;
        }
        
        return $query->first();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrasis::route('/'),
            'create' => Pages\CreateRegistrasi::route('/create'),
            'view' => Pages\ViewRegistrasi::route('/{record}'),
            'edit' => Pages\EditRegistrasi::route('/{record}/edit'),
        ];
    }
}