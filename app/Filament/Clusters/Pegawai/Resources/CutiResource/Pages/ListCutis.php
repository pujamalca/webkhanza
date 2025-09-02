<?php

namespace App\Filament\Clusters\Pegawai\Resources\CutiResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\CutiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCutis extends ListRecords
{
    protected static string $resource = CutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Cuti'),
        ];
    }
}