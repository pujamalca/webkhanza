<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\CreatePegawai;
use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\EditPegawai;
use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\ListPegawai;
use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\ViewPegawai;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Pegawai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationLabel(): string
    {
        return 'Pegawai';
    }

    public static function getModelLabel(): string
    {
        return 'Pegawai';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Pegawai';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Pribadi')
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        
                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(50),
                        
                        Select::make('jk')
                            ->label('Jenis Kelamin')
                            ->options([
                                'Pria' => 'Pria',
                                'Wanita' => 'Wanita',
                            ])
                            ->required(),
                        
                        TextInput::make('tmp_lahir')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(20),
                        
                        DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->required(),
                        
                        TextInput::make('alamat')
                            ->label('Alamat')
                            ->required()
                            ->maxLength(60)
                            ->columnSpanFull(),
                        
                        TextInput::make('kota')
                            ->label('Kota')
                            ->required()
                            ->maxLength(20),
                        
                        TextInput::make('no_ktp')
                            ->label('No. KTP')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                    ])
                    ->columns(2),
                
                Section::make('Data Kepegawaian')
                    ->schema([
                        TextInput::make('jbtn')
                            ->label('Jabatan')
                            ->required()
                            ->maxLength(25),
                        
                        Select::make('jnj_jabatan')
                            ->label('Jenjang Jabatan')
                            ->relationship('jnj_jabatan', 'nama')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('kode')->required()->maxLength(10),
                                TextInput::make('nama')->required()->maxLength(50),
                                TextInput::make('tnj')->numeric()->required()->label('Tunjangan'),
                                TextInput::make('indek')->numeric()->required(),
                            ]),
                        
                        Select::make('kode_kelompok')
                            ->label('Kelompok Jabatan')
                            ->relationship('kelompok_jabatan', 'nama_kelompok')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('kode_kelompok')->required()->maxLength(3),
                                TextInput::make('nama_kelompok')->required()->maxLength(100),
                                TextInput::make('indek')->numeric(),
                            ]),
                        
                        Select::make('departemen')
                            ->label('Departemen')
                            ->relationship('departemen', 'nama')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('dep_id')->required()->maxLength(4)->label('Kode'),
                                TextInput::make('nama')->required()->maxLength(25),
                            ]),
                        
                        Select::make('bidang')
                            ->label('Bidang')
                            ->relationship('bidang', 'nama')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('nama')->required()->maxLength(15),
                            ]),
                        
                        Select::make('kode_resiko')
                            ->label('Resiko Kerja')
                            ->relationship('resiko_kerja', 'nama_resiko')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('kode_resiko')->required()->maxLength(3),
                                TextInput::make('nama_resiko')->required()->maxLength(100),
                                TextInput::make('indek')->numeric(),
                            ]),
                        
                        Select::make('kode_emergency')
                            ->label('Emergency Index')
                            ->relationship('emergency_index', 'nama_emergency')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('kode_emergency')->required()->maxLength(3),
                                TextInput::make('nama_emergency')->required()->maxLength(100),
                                TextInput::make('indek')->numeric(),
                            ]),
                        
                        DatePicker::make('mulai_kerja')
                            ->label('Mulai Kerja')
                            ->required(),
                        
                        DatePicker::make('mulai_kontrak')
                            ->label('Mulai Kontrak'),
                        
                        Select::make('ms_kerja')
                            ->label('Masa Kerja')
                            ->options([
                                '<1' => 'Kurang dari 1 tahun',
                                'PT' => 'Paruh Waktu',
                                'FT>1' => 'Penuh Waktu > 1 tahun',
                            ]),
                        
                        Select::make('stts_aktif')
                            ->label('Status Aktif')
                            ->options([
                                'AKTIF' => 'AKTIF',
                                'CUTI' => 'CUTI',
                                'KELUAR' => 'KELUAR',
                                'TENAGA LUAR' => 'TENAGA LUAR',
                                'NON AKTIF' => 'NON AKTIF',
                            ])
                            ->required(),
                        
                        TextInput::make('wajibmasuk')
                            ->label('Wajib Masuk (hari)')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('cuti_diambil')
                            ->label('Cuti Diambil')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                
                Section::make('Data Keuangan & Pajak')
                    ->schema([
                        Select::make('stts_wp')
                            ->label('Status WP')
                            ->relationship('stts_wp', 'ktg')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('stts')->required()->maxLength(5)->label('Status'),
                                TextInput::make('ktg')->required()->maxLength(50)->label('Kategori'),
                            ]),
                        
                        Select::make('stts_kerja')
                            ->label('Status Kerja')
                            ->relationship('stts_kerja', 'ktg')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('stts')->required()->maxLength(3)->label('Status'),
                                TextInput::make('ktg')->required()->maxLength(20)->label('Kategori'),
                                TextInput::make('indek')->numeric(),
                            ]),
                        
                        TextInput::make('npwp')
                            ->label('NPWP')
                            ->maxLength(15)
                            ->default('-'),
                        
                        Select::make('bpd')
                            ->label('Bank')
                            ->relationship('bank', 'namabank')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('namabank')->required()->maxLength(50)->label('Nama Bank'),
                            ]),
                        
                        TextInput::make('rekening')
                            ->label('No. Rekening')
                            ->maxLength(25),
                        
                        TextInput::make('gapok')
                            ->label('Gaji Pokok')
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01),
                        
                        TextInput::make('pengurang')
                            ->label('Pengurang')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->step(0.01),
                        
                        TextInput::make('dankes')
                            ->label('Dankes')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->step(0.01),
                        
                        TextInput::make('indek')
                            ->label('Indek')
                            ->numeric()
                            ->default(1),
                        
                        TextInput::make('indexins')
                            ->label('Index Ins')
                            ->maxLength(4),
                    ])
                    ->columns(2),
                
                Section::make('Pendidikan & Foto')
                    ->schema([
                        Select::make('pendidikan')
                            ->label('Tingkat Pendidikan')
                            ->relationship('pendidikan', 'tingkat')
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('tingkat')->required()->maxLength(80),
                                TextInput::make('indek')->numeric()->required(),
                                TextInput::make('gapok1')->numeric()->required()->label('Gaji Pokok'),
                                TextInput::make('kenaikan')->numeric()->required(),
                                TextInput::make('maksimal')->numeric()->required(),
                            ]),
                        
                        FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->directory('pegawai')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jk')
                    ->label('JK')
                    ->sortable(),
                
                TextColumn::make('jbtn')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('departemen.nama')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('bidang.nama')
                    ->label('Bidang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jnj_jabatan.nama')
                    ->label('Jenjang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('stts_aktif')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AKTIF' => 'success',
                        'CUTI' => 'warning',
                        'KELUAR' => 'danger',
                        'TENAGA LUAR' => 'info',
                        'NON AKTIF' => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('gapok')
                    ->label('Gaji Pokok')
                    ->money('IDR')
                    ->sortable(),
                
                TextColumn::make('mulai_kerja')
                    ->label('Mulai Kerja')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ]);
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
            'index' => ListPegawai::route('/'),
            'create' => CreatePegawai::route('/create'),
            'view' => ViewPegawai::route('/{record}'),
            'edit' => EditPegawai::route('/{record}/edit'),
        ];
    }
}