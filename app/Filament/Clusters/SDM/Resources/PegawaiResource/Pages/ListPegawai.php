<?php

namespace App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\PegawaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPegawai extends ListRecords
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}