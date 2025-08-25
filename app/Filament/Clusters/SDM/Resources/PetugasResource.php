<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\CreatePetugas;
use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\EditPetugas;
use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\ListPetugas;
use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\ViewPetugas;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Petugas;
use App\Models\Jabatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PetugasResource extends Resource
{
    protected static ?string $model = Petugas::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationLabel(): string
    {
        return 'Petugas';
    }

    public static function getModelLabel(): string
    {
        return 'Petugas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Petugas';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Identitas')
                    ->description('Informasi dasar petugas')
                    ->schema([
                        TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        
                        TextInput::make('nama')
                            ->label('Nama Petugas')
                            ->required()
                            ->maxLength(50)
                            ->columnSpan(2),
                        
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
                    ->description('Informasi pribadi petugas')
                    ->schema([
                        TextInput::make('tmp_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(20)
                            ->columnSpan(1),
                        
                        DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->columnSpan(1),
                        
                        Select::make('gol_darah')
                            ->label('Golongan Darah')
                            ->options([
                                'A' => 'A',
                                'B' => 'B', 
                                'O' => 'O',
                                'AB' => 'AB',
                                '-' => 'Tidak Diketahui',
                            ])
                            ->columnSpan(1),
                        
                        TextInput::make('agama')
                            ->label('Agama')
                            ->maxLength(12)
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
                    ->description('Informasi kontak petugas')
                    ->schema([
                        Textarea::make('alamat')
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
                            ->maxLength(50)
                            ->columnSpan(1),
                    ])
                    ->columns(4),

                Section::make('Data Profesi')
                    ->description('Informasi jabatan dan status')
                    ->schema([
                        Select::make('kd_jbtn')
                            ->label('Jabatan')
                            ->relationship('jabatan', 'nm_jbtn')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('kd_jbtn')
                                    ->label('Kode Jabatan')
                                    ->required()
                                    ->maxLength(5),
                                TextInput::make('nm_jbtn')
                                    ->label('Nama Jabatan')
                                    ->required()
                                    ->maxLength(25),
                            ])
                            ->columnSpan(2),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                1 => 'Aktif',
                                0 => 'Non Aktif',
                            ])
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama')
                    ->label('Nama Petugas')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jk')
                    ->label('JK')
                    ->formatStateUsing(fn (string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->sortable(),
                
                TextColumn::make('jabatan.nm_jbtn')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum ada jabatan'),
                
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
            'index' => ListPetugas::route('/'),
            'create' => CreatePetugas::route('/create'),
            'view' => ViewPetugas::route('/{record}'),
            'edit' => EditPetugas::route('/{record}/edit'),
        ];
    }
}