<?php

namespace App\Filament\Clusters\Erm\Resources\RawatJalanResource\Pages;

use App\Filament\Clusters\Erm\Resources\RawatJalanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRawatJalan extends CreateRecord
{
    protected static string $resource = RawatJalanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}