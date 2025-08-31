<?php

namespace App\Filament\Clusters\Erm\Resources\RegistrasiResource\Pages;

use App\Filament\Clusters\Erm\Resources\RegistrasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrasis extends ListRecords
{
    protected static string $resource = RegistrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}