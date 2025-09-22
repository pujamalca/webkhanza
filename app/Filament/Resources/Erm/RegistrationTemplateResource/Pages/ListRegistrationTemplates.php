<?php

namespace App\Filament\Resources\Erm\RegistrationTemplateResource\Pages;

use App\Filament\Resources\Erm\RegistrationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrationTemplates extends ListRecords
{
    protected static string $resource = RegistrationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Template Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Template Registrasi';
    }
}