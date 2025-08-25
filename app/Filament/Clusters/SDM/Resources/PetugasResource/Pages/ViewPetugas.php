<?php

namespace App\Filament\Clusters\SDM\Resources\PetugasResource\Pages;

use App\Filament\Clusters\SDM\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPetugas extends ViewRecord
{
    protected static string $resource = PetugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}