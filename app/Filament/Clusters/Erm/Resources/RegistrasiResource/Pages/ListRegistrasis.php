<?php

namespace App\Filament\Clusters\Erm\Resources\RegistrasiResource\Pages;

use App\Filament\Clusters\Erm\Resources\RegistrasiResource;
use App\Filament\Clusters\Erm\Resources\QuickRegistrationResource;
use App\Filament\Clusters\Erm\Resources\RegistrationTemplateResource;
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