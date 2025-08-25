<?php

namespace App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Pages;

use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\TrackerSQLResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrackerSQLs extends ListRecords
{
    protected static string $resource = TrackerSQLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}