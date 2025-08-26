<?php

namespace App\Filament\Clusters\Erm\Resources;

use App\Filament\Clusters\Erm\Resources\PasienResource\Pages;
use App\Filament\Clusters\ErmCluster;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Pasien;
use App\Models\Penjab;
use BackedEnum;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static ?string $cluster = ErmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Pasien';
    }

    public static function getModelLabel(): string
    {
        return 'Pasien';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Pasien';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('pasien_view');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('pasien_view_details');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('pasien_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('pasien_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('pasien_delete');
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Identitas Pasien')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('no_rkm_medis')
                                    ->label('No. Rekam Medis')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(15)
                                    ->columnSpan(2),
                                    
                                Checkbox::make('auto_generate_rm')
                                    ->label('Auto Generate')
                                    ->default(true)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $lastRM = Pasien::max('no_rkm_medis');
                                            if ($lastRM) {
                                                // Increment the last RM number
                                                if (is_numeric($lastRM)) {
                                                    $newRM = str_pad((int)$lastRM + 1, 6, '0', STR_PAD_LEFT);
                                                } else {
                                                    // If RM contains letters, extract numbers and increment
                                                    preg_match('/(\d+)/', $lastRM, $matches);
                                                    $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
                                                    $newRM = str_pad($number, 6, '0', STR_PAD_LEFT);
                                                }
                                            } else {
                                                $newRM = '000001';
                                            }
                                            $set('no_rkm_medis', $newRM);
                                        }
                                    })
                                    ->columnSpan(1),
                            ]),

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

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tgl_lahir')
                                    ->label('Tanggal Lahir')
                                    ->maxDate(now())
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $birthDate = \Carbon\Carbon::parse($state);
                                            $now = \Carbon\Carbon::now();
                                            
                                            $years = $birthDate->diffInYears($now);
                                            $months = $birthDate->copy()->addYears($years)->diffInMonths($now);
                                            $days = $birthDate->copy()->addYears($years)->addMonths($months)->diffInDays($now);
                                            
                                            if ($years > 0) {
                                                $umur = $years . ' Th';
                                                if ($months > 0) $umur .= ' ' . $months . ' Bl';
                                                if ($days > 0) $umur .= ' ' . $days . ' Hr';
                                            } elseif ($months > 0) {
                                                $umur = $months . ' Bl';
                                                if ($days > 0) $umur .= ' ' . $days . ' Hr';
                                            } else {
                                                $umur = $days . ' Hr';
                                            }
                                            
                                            $set('umur', $umur);
                                        }
                                    }),
                                    
                                TextInput::make('umur')
                                    ->label('Umur')
                                    ->maxLength(30)
                                    ->readonly(),
                            ]),

                        TextInput::make('nm_ibu')
                            ->label('Nama Ibu Kandung')
                            ->maxLength(40),
                    ])
                    ->columns(2),

                Section::make('Informasi Kontak & Alamat')
                    ->schema([
                        Select::make('alamat')
                            ->label('Alamat Lengkap')
                            ->options(function () {
                                // Get unique addresses from existing patients
                                return Pasien::whereNotNull('alamat')
                                    ->where('alamat', '!=', '')
                                    ->distinct()
                                    ->pluck('alamat', 'alamat')
                                    ->take(100); // Limit to 100 for performance
                            })
                            ->searchable()
                            ->allowHtml()
                            ->getSearchResultsUsing(function (string $search) {
                                return Pasien::where('alamat', 'like', "%{$search}%")
                                    ->whereNotNull('alamat')
                                    ->where('alamat', '!=', '')
                                    ->distinct()
                                    ->limit(50)
                                    ->pluck('alamat', 'alamat');
                            })
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            })
                            ->createOptionForm([
                                Textarea::make('alamat')
                                    ->label('Alamat Baru')
                                    ->rows(3)
                                    ->maxLength(200)
                                    ->required(),
                            ]),

                        Select::make('kd_kab')
                            ->label('Kabupaten')
                            ->options(function () {
                                // Get kabupaten that are actually used by patients
                                return Kabupaten::whereIn('kd_kab', 
                                    Pasien::whereNotNull('kd_kab')->distinct()->pluck('kd_kab')
                                )->pluck('nm_kab', 'kd_kab')
                                ->union(Kabupaten::pluck('nm_kab', 'kd_kab'));
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('kd_kec', null);
                                $set('kd_kel', null);
                            })
                            ->createOptionForm([
                                TextInput::make('nm_kab')
                                    ->label('Nama Kabupaten')
                                    ->required()
                                    ->maxLength(60),
                            ]),

                        Select::make('kd_kec')
                            ->label('Kecamatan')
                            ->options(function (callable $get) {
                                $kabId = $get('kd_kab');
                                if ($kabId) {
                                    // Prioritize kecamatan used by existing patients
                                    $usedKec = Pasien::where('kd_kab', $kabId)
                                        ->whereNotNull('kd_kec')
                                        ->distinct()
                                        ->pluck('kd_kec');
                                    
                                    return Kecamatan::where('kd_kab', $kabId)
                                        ->orderByRaw("FIELD(kd_kec, '" . $usedKec->implode("','") . "') DESC")
                                        ->pluck('nm_kec', 'kd_kec');
                                }
                                return [];
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('kd_kel', null);
                            })
                            ->createOptionForm([
                                TextInput::make('nm_kec')
                                    ->label('Nama Kecamatan')
                                    ->required()
                                    ->maxLength(60),
                                Select::make('kd_kab')
                                    ->label('Kabupaten')
                                    ->relationship('kabupaten', 'nm_kab')
                                    ->required()
                                    ->searchable(),
                            ]),

                        Select::make('kd_kel')
                            ->label('Kelurahan')
                            ->options(function (callable $get) {
                                $kecId = $get('kd_kec');
                                if ($kecId) {
                                    // Prioritize kelurahan used by existing patients
                                    $usedKel = Pasien::where('kd_kec', $kecId)
                                        ->whereNotNull('kd_kel')
                                        ->distinct()
                                        ->pluck('kd_kel');
                                    
                                    return Kelurahan::where('kd_kec', $kecId)
                                        ->orderByRaw("FIELD(kd_kel, '" . $usedKel->implode("','") . "') DESC")
                                        ->pluck('nm_kel', 'kd_kel');
                                }
                                return [];
                            })
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('nm_kel')
                                    ->label('Nama Kelurahan')
                                    ->required()
                                    ->maxLength(60),
                                Select::make('kd_kec')
                                    ->label('Kecamatan')
                                    ->relationship('kecamatan', 'nm_kec')
                                    ->required()
                                    ->searchable(),
                            ]),

                        TextInput::make('no_tlp')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(40),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Section::make('Data Pribadi')
                    ->schema([
                        Select::make('gol_darah')
                            ->label('Golongan Darah')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'AB' => 'AB',
                                'O' => 'O',
                                '-' => 'Tidak Diketahui',
                            ]),

                        Select::make('pekerjaan')
                            ->label('Pekerjaan')
                            ->options(function () {
                                return Pasien::whereNotNull('pekerjaan')
                                    ->where('pekerjaan', '!=', '')
                                    ->distinct()
                                    ->pluck('pekerjaan', 'pekerjaan')
                                    ->take(50);
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Pasien::where('pekerjaan', 'like', "%{$search}%")
                                    ->whereNotNull('pekerjaan')
                                    ->where('pekerjaan', '!=', '')
                                    ->distinct()
                                    ->limit(25)
                                    ->pluck('pekerjaan', 'pekerjaan');
                            })
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            }),

                        Select::make('stts_nikah')
                            ->label('Status Nikah')
                            ->options([
                                'BELUM MENIKAH' => 'Belum Menikah',
                                'MENIKAH' => 'Menikah',
                                'JANDA' => 'Janda',
                                'DUDUK' => 'Duda',
                                'JANDA MATI' => 'Janda Mati',
                                'DUDA MATI' => 'Duda Mati',
                            ]),

                        Select::make('agama')
                            ->label('Agama')
                            ->options([
                                'ISLAM' => 'Islam',
                                'KRISTEN' => 'Kristen',
                                'KATOLIK' => 'Katolik',
                                'HINDU' => 'Hindu',
                                'BUDHA' => 'Budha',
                                'KONG HU CU' => 'Kong Hu Cu',
                                'KEPERCAYAAN' => 'Kepercayaan',
                            ]),

                        Select::make('pnd')
                            ->label('Pendidikan')
                            ->options([
                                'TS' => 'Tidak Sekolah',
                                'TK' => 'TK',
                                'SD' => 'SD',
                                'SLTP' => 'SLTP',
                                'SLTA' => 'SLTA',
                                'D1' => 'D1',
                                'D2' => 'D2',
                                'D3' => 'D3',
                                'D4' => 'D4',
                                'S1' => 'S1',
                                'S2' => 'S2',
                                'S3' => 'S3',
                            ]),

                        Select::make('suku_bangsa')
                            ->label('Suku Bangsa')
                            ->options([
                                'JAWA' => 'Jawa',
                                'SUNDA' => 'Sunda',
                                'BATAK' => 'Batak',
                                'BUGIS' => 'Bugis',
                                'BETAWI' => 'Betawi',
                                'MINANG' => 'Minang',
                                'BALI' => 'Bali',
                                'BANJAR' => 'Banjar',
                                'LAINNYA' => 'Lainnya',
                            ])
                            ->default('JAWA'),

                        TextInput::make('bahasa_pasien')
                            ->label('Bahasa')
                            ->maxLength(10)
                            ->default('Indonesia'),

                        Select::make('cacat_fisik')
                            ->label('Cacat Fisik')
                            ->options([
                                'TIDAK CACAT' => 'Tidak Cacat',
                                'CACAT FISIK' => 'Cacat Fisik',
                                'CACAT MENTAL' => 'Cacat Mental',
                                'CACAT FISIK & MENTAL' => 'Cacat Fisik & Mental',
                            ])
                            ->default('TIDAK CACAT'),
                    ])
                    ->columns(3),

                Section::make('Data Penanggung Jawab')
                    ->schema([
                        Select::make('keluarga')
                            ->label('Status Keluarga')
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
                            ->searchable(),

                        TextInput::make('namakeluarga')
                            ->label('Nama Penanggung Jawab')
                            ->maxLength(50),

                        Select::make('alamatpj')
                            ->label('Alamat Penanggung Jawab')
                            ->options(function () {
                                // Get unique addresses from existing patients PJ
                                return Pasien::whereNotNull('alamatpj')
                                    ->where('alamatpj', '!=', '')
                                    ->distinct()
                                    ->pluck('alamatpj', 'alamatpj')
                                    ->take(100);
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Pasien::where('alamatpj', 'like', "%{$search}%")
                                    ->whereNotNull('alamatpj')
                                    ->where('alamatpj', '!=', '')
                                    ->distinct()
                                    ->limit(50)
                                    ->pluck('alamatpj', 'alamatpj');
                            })
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            })
                            ->createOptionForm([
                                Textarea::make('alamatpj')
                                    ->label('Alamat Baru')
                                    ->rows(2)
                                    ->maxLength(100)
                                    ->required(),
                            ]),

                        Select::make('kelurahanpj')
                            ->label('Kelurahan PJ')
                            ->options(function () {
                                return Pasien::whereNotNull('kelurahanpj')
                                    ->where('kelurahanpj', '!=', '')
                                    ->distinct()
                                    ->pluck('kelurahanpj', 'kelurahanpj')
                                    ->take(50);
                            })
                            ->searchable()
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            }),

                        Select::make('kecamatanpj')
                            ->label('Kecamatan PJ')
                            ->options(function () {
                                return Pasien::whereNotNull('kecamatanpj')
                                    ->where('kecamatanpj', '!=', '')
                                    ->distinct()
                                    ->pluck('kecamatanpj', 'kecamatanpj')
                                    ->take(50);
                            })
                            ->searchable()
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            }),

                        Select::make('kabupatenpj')
                            ->label('Kabupaten PJ')
                            ->options(function () {
                                return Pasien::whereNotNull('kabupatenpj')
                                    ->where('kabupatenpj', '!=', '')
                                    ->distinct()
                                    ->pluck('kabupatenpj', 'kabupatenpj')
                                    ->take(50);
                            })
                            ->searchable()
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            }),

                        Select::make('pekerjaanpj')
                            ->label('Pekerjaan PJ')
                            ->options(function () {
                                return Pasien::whereNotNull('pekerjaanpj')
                                    ->where('pekerjaanpj', '!=', '')
                                    ->distinct()
                                    ->pluck('pekerjaanpj', 'pekerjaanpj')
                                    ->take(50);
                            })
                            ->searchable()
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            }),

                        Select::make('kd_pj')
                            ->label('Cara Bayar')
                            ->options(Penjab::pluck('png_jawab', 'kd_pj'))
                            ->searchable()
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

                        TextInput::make('no_peserta')
                            ->label('No. Peserta/Kartu')
                            ->maxLength(25),
                    ])
                    ->columns(3),

                Section::make('Informasi Pendaftaran')
                    ->schema([
                        DatePicker::make('tgl_daftar')
                            ->label('Tanggal Daftar')
                            ->default(now())
                            ->required(),

                        TextInput::make('nip')
                            ->label('NIP (Jika PNS)')
                            ->maxLength(30),

                        Select::make('perusahaan_pasien')
                            ->label('Perusahaan/Instansi')
                            ->options(function () {
                                return Pasien::whereNotNull('perusahaan_pasien')
                                    ->where('perusahaan_pasien', '!=', '')
                                    ->distinct()
                                    ->pluck('perusahaan_pasien', 'perusahaan_pasien')
                                    ->take(50);
                            })
                            ->searchable()
                            ->createOptionUsing(function (string $value) {
                                return $value;
                            }),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rkm_medis')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('no_ktp')
                    ->label('No. KTP')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('jk')
                    ->label('JK')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'L' => 'blue',
                        'P' => 'pink',
                        default => 'gray',
                    }),

                TextColumn::make('tmp_lahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable()
                    ->limit(15),

                TextColumn::make('tgl_lahir')
                    ->label('Tgl Lahir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('umur')
                    ->label('Umur')
                    ->toggleable(),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('no_tlp')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('penjab.png_jawab')
                    ->label('Cara Bayar')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('tgl_daftar')
                    ->label('Tgl Daftar')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('reg_periksa_count')
                    ->label('Kunjungan')
                    ->counts('regPeriksa')
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                SelectFilter::make('jk')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),

                SelectFilter::make('kd_pj')
                    ->label('Cara Bayar')
                    ->relationship('penjab', 'png_jawab')
                    ->searchable(),

                SelectFilter::make('agama')
                    ->label('Agama')
                    ->options([
                        'ISLAM' => 'Islam',
                        'KRISTEN' => 'Kristen',
                        'KATOLIK' => 'Katolik',
                        'HINDU' => 'Hindu',
                        'BUDHA' => 'Budha',
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('tgl_daftar', 'desc');
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
            'index' => Pages\ListPasiens::route('/'),
            'create' => Pages\CreatePasien::route('/create'),
            'view' => Pages\ViewPasien::route('/{record}'),
            'edit' => Pages\EditPasien::route('/{record}/edit'),
        ];
    }
}