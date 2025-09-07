<?php

namespace App\Filament\Clusters\Marketing\Resources\MarketingCategoryResource\Pages;

use App\Filament\Clusters\Marketing\Resources\MarketingCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketingCategory extends EditRecord
{
    protected static string $resource = MarketingCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}