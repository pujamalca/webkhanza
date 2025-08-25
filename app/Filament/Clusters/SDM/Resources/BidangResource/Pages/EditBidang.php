<?php

namespace App\Filament\Clusters\SDM\Resources\BidangResource\Pages;

use App\Filament\Clusters\SDM\Resources\BidangResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBidang extends EditRecord
{
    protected static string $resource = BidangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}