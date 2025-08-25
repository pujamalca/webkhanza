<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\BidangResource\Pages\CreateBidang;
use App\Filament\Clusters\SDM\Resources\BidangResource\Pages\EditBidang;
use App\Filament\Clusters\SDM\Resources\BidangResource\Pages\ListBidang;
use App\Filament\Clusters\SDM\Resources\BidangResource\Pages\ViewBidang;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Bidang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class BidangResource extends Resource
{
    protected static ?string $model = Bidang::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationLabel(): string
    {
        return 'Bidang';
    }

    public static function getModelLabel(): string
    {
        return 'Bidang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Bidang';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Bidang')
                    ->required()
                    ->maxLength(15)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Bidang')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('pegawai_count')
                    ->label('Jumlah Pegawai')
                    ->counts('pegawai')
                    ->badge()
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
            'index' => ListBidang::route('/'),
            'create' => CreateBidang::route('/create'),
            'view' => ViewBidang::route('/{record}'),
            'edit' => EditBidang::route('/{record}/edit'),
        ];
    }
}