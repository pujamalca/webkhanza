<?php

namespace App\Filament\Resources\Erm\RegistrasiResource\Pages;

use App\Filament\Resources\Erm\RegistrasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrasi extends CreateRecord
{
    protected static string $resource = RegistrasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}