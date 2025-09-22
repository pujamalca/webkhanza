<?php

namespace App\Filament\Resources\Erm\RegistrationTemplateResource\Pages;

use App\Filament\Resources\Erm\RegistrationTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrationTemplate extends CreateRecord
{
    protected static string $resource = RegistrationTemplateResource::class;

    public function getTitle(): string
    {
        return 'Buat Template Registrasi';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}