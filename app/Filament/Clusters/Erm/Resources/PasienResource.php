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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                        TextInput::make('no_rkm_medis')
                            ->label('No. Rekam Medis')
                            ->required()
                            ->unique(ignoreRecord: true)
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

                        TextInput::make('nm_ibu')
                            ->label('Nama Ibu Kandung')
                            ->maxLength(40),
                    ])
                    ->columns(2),

                Section::make('Informasi Kontak & Alamat')
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->maxLength(200),

                        Select::make('kd_kab')
                            ->label('Kabupaten')
                            ->options(Kabupaten::pluck('nm_kab', 'kd_kab'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('kd_kec', null);
                                $set('kd_kel', null);
                            }),

                        Select::make('kd_kec')
                            ->label('Kecamatan')
                            ->options(function (callable $get) {
                                $kabId = $get('kd_kab');
                                if ($kabId) {
                                    return Kecamatan::where('kd_kab', $kabId)->pluck('nm_kec', 'kd_kec');
                                }
                                return [];
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('kd_kel', null);
                            }),

                        Select::make('kd_kel')
                            ->label('Kelurahan')
                            ->options(function (callable $get) {
                                $kecId = $get('kd_kec');
                                if ($kecId) {
                                    return Kelurahan::where('kd_kec', $kecId)->pluck('nm_kel', 'kd_kel');
                                }
                                return [];
                            })
                            ->searchable(),

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

                        TextInput::make('pekerjaan')
                            ->label('Pekerjaan')
                            ->maxLength(60),

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
                        TextInput::make('keluarga')
                            ->label('Status Keluarga')
                            ->maxLength(11),

                        TextInput::make('namakeluarga')
                            ->label('Nama Penanggung Jawab')
                            ->maxLength(50),

                        Textarea::make('alamatpj')
                            ->label('Alamat Penanggung Jawab')
                            ->rows(2)
                            ->maxLength(100),

                        TextInput::make('kelurahanpj')
                            ->label('Kelurahan PJ')
                            ->maxLength(60),

                        TextInput::make('kecamatanpj')
                            ->label('Kecamatan PJ')
                            ->maxLength(60),

                        TextInput::make('kabupatenpj')
                            ->label('Kabupaten PJ')
                            ->maxLength(60),

                        TextInput::make('pekerjaanpj')
                            ->label('Pekerjaan PJ')
                            ->maxLength(35),

                        Select::make('kd_pj')
                            ->label('Cara Bayar')
                            ->options(Penjab::pluck('png_jawab', 'kd_pj'))
                            ->searchable()
                            ->required(),

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

                        TextInput::make('umur')
                            ->label('Umur')
                            ->maxLength(30),

                        TextInput::make('nip')
                            ->label('NIP (Jika PNS)')
                            ->maxLength(30),

                        TextInput::make('perusahaan_pasien')
                            ->label('Perusahaan/Instansi')
                            ->maxLength(8),
                    ])
                    ->columns(2),
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