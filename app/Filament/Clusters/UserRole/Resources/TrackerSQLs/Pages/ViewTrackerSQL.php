<?php

namespace App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Pages;

use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\TrackerSQLResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTrackerSQL extends ViewRecord
{
    protected static string $resource = TrackerSQLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}