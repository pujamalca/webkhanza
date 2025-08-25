<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\CreatePegawai;
use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\EditPegawai;
use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\ListPegawai;
use App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages\ViewPegawai;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Pegawai;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlineUser;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->maxLength(20),
                
                TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(50),
                
                Select::make('jk')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Pria' => 'Pria',
                        'Wanita' => 'Wanita',
                    ])
                    ->required(),
                
                TextInput::make('jbtn')
                    ->label('Jabatan')
                    ->required()
                    ->maxLength(25),
                
                TextInput::make('departemen')
                    ->label('Departemen')
                    ->required()
                    ->maxLength(4),
                
                TextInput::make('bidang')
                    ->label('Bidang')
                    ->required()
                    ->maxLength(15),
                
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
                    ->maxLength(60),
                
                TextInput::make('kota')
                    ->label('Kota')
                    ->required()
                    ->maxLength(20),
                
                DatePicker::make('mulai_kerja')
                    ->label('Mulai Kerja')
                    ->required(),
                
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
                
                TextInput::make('no_ktp')
                    ->label('No. KTP')
                    ->required()
                    ->maxLength(20),
                
                TextInput::make('npwp')
                    ->label('NPWP')
                    ->maxLength(15),
                
                TextInput::make('gapok')
                    ->label('Gaji Pokok')
                    ->numeric()
                    ->prefix('Rp'),
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
                
                TextColumn::make('departemen')
                    ->label('Dept')
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
                
                TextColumn::make('mulai_kerja')
                    ->label('Mulai Kerja')
                    ->date('d/m/Y')
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
            'index' => ListPegawai::route('/'),
            'create' => CreatePegawai::route('/create'),
            'view' => ViewPegawai::route('/{record}'),
            'edit' => EditPegawai::route('/{record}/edit'),
        ];
    }
}