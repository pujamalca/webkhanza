<?php

namespace App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Pages;

use App\Filament\Clusters\UserRole\Resources\TrackerSQLs\TrackerSQLResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTrackerSQL extends EditRecord
{
    protected static string $resource = TrackerSQLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}