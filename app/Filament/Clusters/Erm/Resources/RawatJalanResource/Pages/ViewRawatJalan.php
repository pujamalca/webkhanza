<?php

namespace App\Filament\Clusters\Erm\Resources\RawatJalanResource\Pages;

use App\Filament\Clusters\Erm\Resources\RawatJalanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRawatJalan extends ViewRecord
{
    protected static string $resource = RawatJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}