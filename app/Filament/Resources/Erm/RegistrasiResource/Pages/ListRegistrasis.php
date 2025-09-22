<?php

namespace App\Filament\Resources\Erm\RegistrasiResource\Pages;

use App\Filament\Resources\Erm\RegistrasiResource;
use App\Filament\Resources\Erm\QuickRegistrationResource;
use App\Filament\Resources\Erm\RegistrationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrasis extends ListRecords
{
    protected static string $resource = RegistrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('quick_registration')
                ->label('Registrasi Cepat')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->url(QuickRegistrationResource::getUrl('create'))
                ->visible(fn() => auth()->user()->can('registration_quick_access')),
            Actions\Action::make('template_registration')
                ->label('Template Registrasi')
                ->icon('heroicon-o-document-duplicate')
                ->color('info')
                ->url(RegistrationTemplateResource::getUrl('index'))
                ->visible(fn() => auth()->user()->can('registration_template_manage')),
        ];
    }
}