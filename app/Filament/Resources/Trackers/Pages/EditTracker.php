<?php

namespace App\Filament\Resources\Trackers\Pages;

use App\Filament\Resources\Trackers\TrackerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTracker extends EditRecord
{
    protected static string $resource = TrackerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
