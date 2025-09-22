<?php

namespace App\Filament\Resources\Erm\RegistrasiResource\Pages;

use App\Filament\Resources\Erm\RegistrasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistrasi extends ViewRecord
{
    protected static string $resource = RegistrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}