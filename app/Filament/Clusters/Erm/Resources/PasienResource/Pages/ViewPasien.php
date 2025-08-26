<?php

namespace App\Filament\Clusters\Erm\Resources\PasienResource\Pages;

use App\Filament\Clusters\Erm\Resources\PasienResource;
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