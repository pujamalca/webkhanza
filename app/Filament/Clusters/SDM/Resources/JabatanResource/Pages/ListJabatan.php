<?php

namespace App\Filament\Clusters\SDM\Resources\JabatanResource\Pages;

use App\Filament\Clusters\SDM\Resources\JabatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJabatan extends ListRecords
{
    protected static string $resource = JabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}