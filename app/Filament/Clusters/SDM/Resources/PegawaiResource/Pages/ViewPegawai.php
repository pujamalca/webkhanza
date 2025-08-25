<?php

namespace App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\PegawaiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}