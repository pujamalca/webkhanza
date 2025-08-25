<?php

namespace App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\PegawaiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}