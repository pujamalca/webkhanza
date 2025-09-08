<?php

namespace App\Filament\Clusters\Erm\Resources\RegistrationTemplateResource\Pages;

use App\Filament\Clusters\Erm\Resources\RegistrationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrationTemplate extends EditRecord
{
    protected static string $resource = RegistrationTemplateResource::class;

    public function getTitle(): string
    {
        return 'Edit Template Registrasi';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}