<?php

namespace App\Filament\Clusters\Erm\Resources\RegistrationTemplateResource\Pages;

use App\Filament\Clusters\Erm\Resources\RegistrationTemplateResource;
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