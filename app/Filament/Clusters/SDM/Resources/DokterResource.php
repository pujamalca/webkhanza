<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\CreateDokter;
use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\EditDokter;
use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\ListDokter;
use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\ViewDokter;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Dokter;
use App\Models\Pegawai;
use App\Models\Spesialis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DokterResource extends Resource
{
    protected static ?string $model = Dokter::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $recordTitleAttribute = 'nm_dokter';

    public static function getNavigationLabel(): string
    {
        return 'Dokter';
    }

    public static function getModelLabel(): string
    {
        return 'Dokter';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Dokter';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('dokter_view');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('dokter_view_details');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('dokter_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('dokter_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('dokter_delete');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Identitas')
                    ->description('Informasi dasar dokter')
                    ->schema([
                        Select::make('kd_dokter')
                            ->label('NIK (Pilih dari Pegawai)')
                            ->relationship(
                                'pegawai', 
                                'nik',
                                fn ($query, $record) => $query->whereNotIn('nik', function ($subQuery) use ($record) {
                                    $subQuery->select('kd_dokter')
                                        ->from('dokter')
                                        ->whereNotNull('kd_dokter');
                                    // Allow current record's NIK when editing
                                    if ($record && $record->kd_dokter) {
                                        $subQuery->where('kd_dokter', '!=', $record->kd_dokter);
                                    }
                                })
                            )
                            ->getOptionLabelFromRecordUsing(fn (Pegawai $record): string => "{$record->nik} - {$record->nama}")
                            ->searchable(['nik', 'nama'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $pegawai = Pegawai::where('nik', $state)->first();
                                    if ($pegawai) {
                                        // Auto-fill from pegawai
                                        $set('nm_dokter', $pegawai->nama);
                                        $set('tmp_lahir', $pegawai->tmp_lahir);
                                        $set('tgl_lahir', $pegawai->tgl_lahir ? $pegawai->tgl_lahir->format('Y-m-d') : null);
                                        $set('almt_tgl', $pegawai->alamat);
                                        $set('gol_drh', $pegawai->gol_darah);
                                        $set('agama', $pegawai->agama);
                                        $set('no_telp', $pegawai->no_telp);
                                        $set('email', $pegawai->email);
                                        
                                        // Convert jk from Pria/Wanita to L/P for dokter
                                        if ($pegawai->jk === 'Pria') {
                                            $set('jk', 'L');
                                        } elseif ($pegawai->jk === 'Wanita') {
                                            $set('jk', 'P');
                                        }
                                        
                                        // Set default status aktif
                                        $set('status', 1);
                                    }
                                }
                            })
                            ->columnSpan(2),
                        
                        TextInput::make('nm_dokter')
                            ->label('Nama Dokter')
                            ->required()
                            ->maxLength(50)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        
                        Select::make('jk')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(4),

                Section::make('Data Pribadi')
                    ->description('Informasi pribadi dokter (Sebagian auto-filled, sebagian input manual)')
                    ->schema([
                        TextInput::make('tmp_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(20)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        
                        DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->columnSpan(1),
                        
                        Select::make('gol_drh')
                            ->label('Golongan Darah')
                            ->options([
                                'A' => 'A',
                                'B' => 'B', 
                                'O' => 'O',
                                'AB' => 'AB',
                                '-' => 'Tidak Diketahui',
                            ])
                            ->columnSpan(1),
                        
                        Select::make('agama')
                            ->label('Agama')
                            ->options([
                                'ISLAM' => 'Islam',
                                'KRISTEN' => 'Kristen',
                                'KATHOLIK' => 'Katholik',
                                'HINDU' => 'Hindu',
                                'BUDHA' => 'Budha',
                                'KONGHUCU' => 'Konghucu',
                                'KEPERCAYAAN' => 'Kepercayaan',
                            ])
                            ->searchable()
                            ->columnSpan(1),
                        
                        Select::make('stts_nikah')
                            ->label('Status Nikah')
                            ->options([
                                'BELUM MENIKAH' => 'Belum Menikah',
                                'MENIKAH' => 'Menikah', 
                                'JANDA' => 'Janda',
                                'DUDHA' => 'Dudha',
                                'JOMBLO' => 'Jomblo',
                            ])
                            ->columnSpan(2),
                    ])
                    ->columns(4),

                Section::make('Kontak & Alamat')
                    ->description('Informasi kontak dokter (Auto-filled dari data pegawai, dapat diedit)')
                    ->schema([
                        Textarea::make('almt_tgl')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpan(2),
                        
                        TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(13)
                            ->columnSpan(1),
                        
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(70)
                            ->columnSpan(1),
                    ])
                    ->columns(4),

                Section::make('Data Profesi')
                    ->description('Informasi profesi dan keahlian')
                    ->schema([
                        Select::make('kd_sps')
                            ->label('Spesialis')
                            ->relationship('spesialis', 'nm_sps')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('kd_sps')
                                    ->label('Kode Spesialis')
                                    ->required()
                                    ->maxLength(5),
                                TextInput::make('nm_sps')
                                    ->label('Nama Spesialis')
                                    ->required()
                                    ->maxLength(30),
                            ])
                            ->columnSpan(2),
                        
                        TextInput::make('alumni')
                            ->label('Alumni')
                            ->maxLength(60)
                            ->columnSpan(2),
                        
                        TextInput::make('no_ijn_praktek')
                            ->label('No. Ijin Praktek')
                            ->maxLength(120)
                            ->columnSpan(2),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                1 => 'Aktif',
                                0 => 'Non Aktif',
                            ])
                            ->required()
                            ->default(1)
                            ->columnSpan(2),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_dokter')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nm_dokter')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jk')
                    ->label('JK')
                    ->formatStateUsing(fn (string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->sortable(),
                
                TextColumn::make('spesialis.nm_sps')
                    ->label('Spesialis')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum ada spesialis'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
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
            'index' => ListDokter::route('/'),
            'create' => CreateDokter::route('/create'),
            'view' => ViewDokter::route('/{record}'),
            'edit' => EditDokter::route('/{record}/edit'),
        ];
    }
}