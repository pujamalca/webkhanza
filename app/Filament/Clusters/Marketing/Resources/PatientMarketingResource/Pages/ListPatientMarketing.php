<?php

namespace App\Filament\Clusters\Marketing\Resources\PatientMarketingResource\Pages;

use App\Filament\Clusters\Marketing\Resources\PatientMarketingResource;
use App\Filament\Widgets\PatientMarketingStatsWidget;
use App\Filament\Widgets\MarketingTaskCompletionWidget;
use App\Filament\Widgets\TopMarketingCategoriesWidget;
use Filament\Resources\Pages\ListRecords;

class ListPatientMarketing extends ListRecords
{
    protected static string $resource = PatientMarketingResource::class;

    protected function getFooterWidgets(): array
    {
        return [
            PatientMarketingStatsWidget::class,
        ];
    }
}