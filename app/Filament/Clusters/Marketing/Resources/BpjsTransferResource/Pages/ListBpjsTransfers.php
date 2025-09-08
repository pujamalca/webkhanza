<?php

namespace App\Filament\Clusters\Marketing\Resources\BpjsTransferResource\Pages;

use App\Filament\Clusters\Marketing\Resources\BpjsTransferResource;
use App\Filament\Widgets\BpjsTransferStatsWidget;
use App\Filament\Widgets\MarketingTaskCompletionWidget;
use App\Filament\Widgets\TopMarketingCategoriesWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBpjsTransfers extends ListRecords
{
    protected static string $resource = BpjsTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data Pindah BPJS')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BpjsTransferStatsWidget::class,
        ];
    }
}