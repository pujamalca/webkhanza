<?php

namespace App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBerkasPegawai extends EditRecord
{
    protected static string $resource = BerkasPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}