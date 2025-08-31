<?php

namespace App\Filament\Clusters\Erm\Resources\RegistrasiResource\Pages;

use App\Filament\Clusters\Erm\Resources\RegistrasiResource;
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