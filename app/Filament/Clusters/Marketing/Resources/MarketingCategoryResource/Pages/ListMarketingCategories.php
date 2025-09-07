<?php

namespace App\Filament\Clusters\Marketing\Resources\MarketingCategoryResource\Pages;

use App\Filament\Clusters\Marketing\Resources\MarketingCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketingCategories extends ListRecords
{
    protected static string $resource = MarketingCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}