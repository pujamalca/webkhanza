<?php

namespace App\Filament\Clusters\Erm\Resources\PasienResource\Pages;

use App\Filament\Clusters\Erm\Resources\PasienResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePasien extends CreateRecord
{
    protected static string $resource = PasienResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}