<?php

namespace App\Filament\Resources\Erm\RawatJalanResource\Pages;

use App\Filament\Resources\Erm\RawatJalanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRawatJalan extends EditRecord
{
    protected static string $resource = RawatJalanResource::class;

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