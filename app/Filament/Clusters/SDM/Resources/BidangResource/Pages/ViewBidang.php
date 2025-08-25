<?php

namespace App\Filament\Clusters\SDM\Resources\BidangResource\Pages;

use App\Filament\Clusters\SDM\Resources\BidangResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBidang extends ViewRecord
{
    protected static string $resource = BidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}