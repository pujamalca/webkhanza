<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsents extends ListRecords
{
    protected static string $resource = AbsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Absensi'),
        ];
    }
}