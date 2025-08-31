<?php

namespace App\Filament\Clusters\Erm\Resources\RegistrasiResource\Pages;

use App\Filament\Clusters\Erm\Resources\RegistrasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrasi extends EditRecord
{
    protected static string $resource = RegistrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}