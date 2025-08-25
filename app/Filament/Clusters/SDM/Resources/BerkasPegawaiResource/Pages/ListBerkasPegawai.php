<?php

namespace App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBerkasPegawai extends ListRecords
{
    protected static string $resource = BerkasPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}