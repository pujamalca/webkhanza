<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\JabatanResource\Pages\CreateJabatan;
use App\Filament\Clusters\SDM\Resources\JabatanResource\Pages\EditJabatan;
use App\Filament\Clusters\SDM\Resources\JabatanResource\Pages\ListJabatan;
use App\Filament\Clusters\SDM\Resources\JabatanResource\Pages\ViewJabatan;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Jabatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class JabatanResource extends Resource
{
    protected static ?string $model = Jabatan::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlineUserGroup;

    protected static ?string $recordTitleAttribute = 'nm_jbtn';

    public static function getNavigationLabel(): string
    {
        return 'Jabatan';
    }

    public static function getModelLabel(): string
    {
        return 'Jabatan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Jabatan';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('kd_jbtn')
                    ->label('Kode Jabatan')
                    ->required()
                    ->maxLength(4)
                    ->unique(ignoreRecord: true),
                
                TextInput::make('nm_jbtn')
                    ->label('Nama Jabatan')
                    ->required()
                    ->maxLength(25),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_jbtn')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nm_jbtn')
                    ->label('Nama Jabatan')
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
            'index' => ListJabatan::route('/'),
            'create' => CreateJabatan::route('/create'),
            'view' => ViewJabatan::route('/{record}'),
            'edit' => EditJabatan::route('/{record}/edit'),
        ];
    }
}