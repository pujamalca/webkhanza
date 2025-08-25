<?php

namespace App\Filament\Clusters\SDM\Resources\DokterResource\Pages;

use App\Filament\Clusters\SDM\Resources\DokterResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDokter extends ViewRecord
{
    protected static string $resource = DokterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}