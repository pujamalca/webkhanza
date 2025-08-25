<?php

namespace App\Filament\Clusters\SDM\Resources\JabatanResource\Pages;

use App\Filament\Clusters\SDM\Resources\JabatanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewJabatan extends ViewRecord
{
    protected static string $resource = JabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}