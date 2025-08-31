<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages\CreateBerkasPegawai;
use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages\EditBerkasPegawai;
use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages\ListBerkasPegawai;
use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages\ViewBerkasPegawai;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\BerkasPegawai;
use App\Models\Pegawai;
use App\Models\MasterBerkasPegawai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BerkasPegawaiResource extends Resource
{
    protected static ?string $model = BerkasPegawai::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'berkas';

    public static function getNavigationLabel(): string
    {
        return 'Berkas Pegawai';
    }

    public static function getModelLabel(): string
    {
        return 'Berkas Pegawai';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Berkas Pegawai';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('berkas_pegawai_view');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('berkas_pegawai_view_details');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('berkas_pegawai_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('berkas_pegawai_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('berkas_pegawai_delete');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Berkas')
                    ->description('Informasi dasar berkas pegawai')
                    ->schema([
                        Select::make('nik')
                            ->label('Pegawai')
                            ->relationship('pegawai', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->required()
                                    ->maxLength(20),
                                TextInput::make('nama')
                                    ->label('Nama Pegawai')
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->columnSpan(2),
                        
                        Select::make('kode_berkas')
                            ->label('Jenis Berkas')
                            ->relationship('masterBerkasPegawai', 'nama_berkas')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                \Filament\Schemas\Components\Section::make('Data Master Berkas yang Sudah Ada')
                                    ->description('Daftar jenis berkas yang sudah terdaftar - Klik tombol hapus untuk menghapus data')
                                    ->schema([
                                        ViewField::make('master_berkas')
                                            ->view('forms.components.master-berkas-manager')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull()
                                    ->collapsible()
                                    ->collapsed(),
                                
                                \Filament\Schemas\Components\Section::make('Tambah Jenis Berkas Baru')
                                    ->description('Form untuk menambah jenis berkas baru')
                                    ->schema([
                                        TextInput::make('kode')
                                            ->label('Kode Berkas')
                                            ->required()
                                            ->maxLength(10)
                                            ->default(function () {
                                                $lastCode = MasterBerkasPegawai::orderBy('kode', 'desc')->first();
                                                if ($lastCode) {
                                                    $codeNumber = (int) substr($lastCode->kode, 3);
                                                    $nextNumber = $codeNumber + 1;
                                                    return 'MBP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                                                }
                                                return 'MBP0001';
                                            })
                                            ->disabled()
                                            ->dehydrated(),
                                        
                                        TextInput::make('nama_berkas')
                                            ->label('Nama Berkas')
                                            ->required()
                                            ->maxLength(300)
                                            ->placeholder('Contoh: FOTOCOPY KTP'),
                                        
                                        Select::make('kategori')
                                            ->label('Kategori')
                                            ->options([
                                                'Tenaga klinis Dokter Umum' => 'Tenaga klinis Dokter Umum',
                                                'Tenaga klinis Dokter Spesialis' => 'Tenaga klinis Dokter Spesialis',
                                                'Tenaga klinis Perawat dan Bidan' => 'Tenaga klinis Perawat dan Bidan',
                                                'Tenaga klinis Profesi Lain' => 'Tenaga klinis Profesi Lain',
                                                'Tenaga Non Klinis' => 'Tenaga Non Klinis',
                                            ])
                                            ->required()
                                            ->searchable(),
                                        
                                        TextInput::make('no_urut')
                                            ->label('No. Urut')
                                            ->numeric()
                                            ->default(function () {
                                                return MasterBerkasPegawai::max('no_urut') + 1;
                                            })
                                            ->required()
                                            ->minValue(1),
                                    ])
                                    ->columns(2),
                            ])
                            ->editOptionForm([
                                TextInput::make('kode')
                                    ->label('Kode Berkas')
                                    ->required()
                                    ->maxLength(10)
                                    ->disabled() // Prevent editing primary key
                                    ->dehydrated(),
                                
                                TextInput::make('nama_berkas')
                                    ->label('Nama Berkas')
                                    ->required()
                                    ->maxLength(300),
                                
                                Select::make('kategori')
                                    ->label('Kategori')
                                    ->options([
                                        'Tenaga klinis Dokter Umum' => 'Tenaga klinis Dokter Umum',
                                        'Tenaga klinis Dokter Spesialis' => 'Tenaga klinis Dokter Spesialis',
                                        'Tenaga klinis Perawat dan Bidan' => 'Tenaga klinis Perawat dan Bidan',
                                        'Tenaga klinis Profesi Lain' => 'Tenaga klinis Profesi Lain',
                                        'Tenaga Non Klinis' => 'Tenaga Non Klinis',
                                    ])
                                    ->required()
                                    ->searchable(),
                                
                                TextInput::make('no_urut')
                                    ->label('No. Urut')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Buat Jenis Berkas Baru')
                                    ->modalSubmitActionLabel('Simpan')
                                    ->modalCancelActionLabel('Batal');
                            })
                            ->editOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Edit Jenis Berkas')
                                    ->modalSubmitActionLabel('Simpan')
                                    ->modalCancelActionLabel('Batal');
                            })
                            ->columnSpan(2),
                    ])
                    ->columns(4),

                Section::make('Informasi Tanggal')
                    ->description('Tanggal upload dan berakhir berkas')
                    ->schema([
                        DatePicker::make('tgl_uploud')
                            ->label('Tanggal Upload')
                            ->required()
                            ->default(now())
                            ->columnSpan(2),
                        
                        DatePicker::make('tgl_berakhir')
                            ->label('Tanggal Berakhir')
                            ->columnSpan(2),
                    ])
                    ->columns(4),

                Section::make('File Berkas')
                    ->description('Upload file berkas pegawai')
                    ->schema([
                        FileUpload::make('berkas')
                            ->label('File Berkas')
                            ->disk('local')
                            ->directory('berkas_pegawai')
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai.nama')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('pegawai.nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('masterBerkasPegawai.nama_berkas')
                    ->label('Jenis Berkas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tidak ada jenis berkas'),
                
                TextColumn::make('masterBerkasPegawai.kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tidak ada kategori'),
                
                TextColumn::make('tgl_uploud')
                    ->label('Tanggal Upload')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('tgl_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Tidak ada batas waktu'),
                
                TextColumn::make('berkas')
                    ->label('File')
                    ->limit(30)
                    ->url(fn ($record) => $record->berkas ? route('berkas-pegawai.show', basename($record->berkas)) : null)
                    ->openUrlInNewTab()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->berkas ? route('berkas-pegawai.download', basename($record->berkas)) : null)
                    ->visible(fn() => auth()->user()->can('berkas_pegawai_download'))
                    ->openUrlInNewTab(),
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
            'index' => ListBerkasPegawai::route('/'),
            'create' => CreateBerkasPegawai::route('/create'),
            'view' => ViewBerkasPegawai::route('/{record}'),
            'edit' => EditBerkasPegawai::route('/{record}/edit'),
        ];
    }
}