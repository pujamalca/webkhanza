<?php

namespace App\Filament\Clusters\UserRole\Resources\TrackerSQLs;

use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Pages\ListTrackerSQLs;
use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Pages\ViewTrackerSQL;
use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Schemas\TrackerSQLForm;
use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Schemas\TrackerSQLInfolist;
use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Tables\TrackerSQLsTable;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Models\TrackerSQL;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrackerSQLResource extends Resource
{
    protected static ?string $model = TrackerSQL::class;

    protected static ?string $cluster = UserRoleCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'custom_key';

    public static function getNavigationLabel(): string
    {
        return 'SQL Tracker';
    }

    public static function getModelLabel(): string
    {
        return 'SQL Tracker';
    }

    public static function getPluralModelLabel(): string
    {
        return 'SQL Trackers';
    }

    public static function form(Schema $schema): Schema
    {
        return TrackerSQLForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrackerSQLInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrackerSQLsTable::configure($table);
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
            'index' => ListTrackerSQLs::route('/'),
            'view' => ViewTrackerSQL::route('/{record}'),
        ];
    }
}