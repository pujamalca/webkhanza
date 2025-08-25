<?php

namespace App\Filament\Clusters\SDM\Resources\DepartemenResource\Pages;

use App\Filament\Clusters\SDM\Resources\DepartemenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDepartemen extends ListRecords
{
    protected static string $resource = DepartemenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}