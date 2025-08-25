<?php

namespace App\Filament\Clusters\UserRole\Resources\Trackers\Pages;

use App\Filament\Clusters\UserRole\Resources\Trackers\TrackerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrackers extends ListRecords
{
    protected static string $resource = TrackerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
