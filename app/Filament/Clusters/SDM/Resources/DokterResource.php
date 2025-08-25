<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\CreateDokter;
use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\EditDokter;
use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\ListDokter;
use App\Filament\Clusters\SDM\Resources\DokterResource\Pages\ViewDokter;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Dokter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\DatePicker;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('kd_dokter')
                    ->label('Kode Dokter')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                
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
                        'O' => 'O',
                        'AB' => 'AB',
                        '-' => 'Tidak Diketahui',
                    ]),
                
                TextInput::make('agama')
                    ->label('Agama')
                    ->maxLength(12),
                
                TextInput::make('almt_tgl')
                    ->label('Alamat')
                    ->maxLength(60),
                
                TextInput::make('no_telp')
                    ->label('No. Telepon')
                    ->maxLength(13),
                
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(70),
                
                Select::make('stts_nikah')
                    ->label('Status Nikah')
                    ->options([
                        'BELUM MENIKAH' => 'Belum Menikah',
                        'MENIKAH' => 'Menikah',
                        'JANDA' => 'Janda',
                        'DUDHA' => 'Dudha',
                        'JOMBLO' => 'Jomblo',
                    ]),
                
                TextInput::make('kd_sps')
                    ->label('Kode Spesialis')
                    ->maxLength(5),
                
                TextInput::make('alumni')
                    ->label('Alumni')
                    ->maxLength(60),
                
                TextInput::make('no_ijn_praktek')
                    ->label('No. Ijin Praktek')
                    ->maxLength(120),
                
                Select::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Non Aktif',
                    ])
                    ->required(),
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
                
                TextColumn::make('kd_sps')
                    ->label('Spesialis')
                    ->searchable()
                    ->sortable(),
                
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
            ->actions([
                ViewAction::make(),
                EditAction::make(),
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