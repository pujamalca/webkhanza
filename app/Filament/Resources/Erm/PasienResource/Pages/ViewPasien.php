<?php

namespace App\Filament\Resources\Erm\PasienResource\Pages;

use App\Filament\Resources\Erm\PasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPasien extends ViewRecord
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}