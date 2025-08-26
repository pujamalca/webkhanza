<?php

namespace App\Filament\Clusters\Erm\Resources\PasienResource\Pages;

use App\Filament\Clusters\Erm\Resources\PasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPasiens extends ListRecords
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}