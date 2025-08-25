<?php

namespace App\Filament\Clusters\UserRole\Resources\Trackers\Pages;

use App\Filament\Clusters\UserRole\Resources\Trackers\TrackerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTracker extends CreateRecord
{
    protected static string $resource = TrackerResource::class;
}
