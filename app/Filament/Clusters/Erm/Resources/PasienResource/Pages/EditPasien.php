<?php

namespace App\Filament\Clusters\Erm\Resources\PasienResource\Pages;

use App\Filament\Clusters\Erm\Resources\PasienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPasien extends EditRecord
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}