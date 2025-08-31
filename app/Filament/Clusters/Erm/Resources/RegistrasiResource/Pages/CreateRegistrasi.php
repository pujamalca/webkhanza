<?php

namespace App\Filament\Clusters\Erm\Resources\RegistrasiResource\Pages;

use App\Filament\Clusters\Erm\Resources\RegistrasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrasi extends CreateRecord
{
    protected static string $resource = RegistrasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}