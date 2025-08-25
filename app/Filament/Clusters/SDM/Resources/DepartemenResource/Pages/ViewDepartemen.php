<?php

namespace App\Filament\Clusters\SDM\Resources\DepartemenResource\Pages;

use App\Filament\Clusters\SDM\Resources\DepartemenResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartemen extends ViewRecord
{
    protected static string $resource = DepartemenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}