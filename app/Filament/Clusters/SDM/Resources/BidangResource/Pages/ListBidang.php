<?php

namespace App\Filament\Clusters\SDM\Resources\BidangResource\Pages;

use App\Filament\Clusters\SDM\Resources\BidangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBidang extends ListRecords
{
    protected static string $resource = BidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}