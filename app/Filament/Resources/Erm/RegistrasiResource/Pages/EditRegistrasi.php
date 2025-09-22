<?php

namespace App\Filament\Resources\Erm\RegistrasiResource\Pages;

use App\Filament\Resources\Erm\RegistrasiResource;
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