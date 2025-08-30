<?php

namespace App\Filament\Clusters\SDM\Resources\PetugasResource\Pages;

use App\Filament\Clusters\SDM\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePetugas extends CreateRecord
{
    protected static string $resource = PetugasResource::class;

    protected function getRedirectUrl(): string
    {
        // Always redirect to index to avoid ID issues after creation
        return $this->getResource()::getUrl('index');
    }
}