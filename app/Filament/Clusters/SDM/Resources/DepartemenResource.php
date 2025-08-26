<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\DepartemenResource\Pages\CreateDepartemen;
use App\Filament\Clusters\SDM\Resources\DepartemenResource\Pages\EditDepartemen;
use App\Filament\Clusters\SDM\Resources\DepartemenResource\Pages\ListDepartemen;
use App\Filament\Clusters\SDM\Resources\DepartemenResource\Pages\ViewDepartemen;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Departemen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartemenResource extends Resource
{
    protected static ?string $model = Departemen::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationLabel(): string
    {
        return 'Departemen';
    }

    public static function getModelLabel(): string
    {
        return 'Departemen';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Departemen';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('dep_id')
                    ->label('Kode Departemen')
                    ->required()
                    ->maxLength(4)
                    ->unique(ignoreRecord: true),
                
                TextInput::make('nama')
                    ->label('Nama Departemen')
                    ->required()
                    ->maxLength(25),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dep_id')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama')
                    ->label('Nama Departemen')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('pegawai_count')
                    ->label('Jumlah Pegawai')
                    ->counts('pegawai')
                    ->badge()
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
            'index' => ListDepartemen::route('/'),
            'create' => CreateDepartemen::route('/create'),
            'view' => ViewDepartemen::route('/{record}'),
            'edit' => EditDepartemen::route('/{record}/edit'),
        ];
    }
}