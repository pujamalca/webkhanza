<?php

namespace App\Filament\Resources\Erm\RawatJalanResource\Pages;

use App\Filament\Resources\Erm\RawatJalanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRawatJalans extends ListRecords
{
    protected static string $resource = RawatJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


}