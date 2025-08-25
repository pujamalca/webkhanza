<?php

namespace App\Filament\Resources\Trackers\Pages;

use App\Filament\Resources\Trackers\TrackerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTracker extends ViewRecord
{
    protected static string $resource = TrackerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
