<?php

namespace App\Filament\Clusters\Erm\Resources\LaboratoriumResource\Pages;

use App\Filament\Clusters\Erm\Resources\LaboratoriumResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListLaboratoriums extends ListRecords
{
    protected static string $resource = LaboratoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->resetTable()),

            Action::make('export')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // TODO: Implement export functionality
                    $this->notify('success', 'Export akan segera tersedia');
                }),
        ];
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    protected function getTableHeading(): string
    {
        return '🧪 Manajemen Hasil Laboratorium';
    }

    protected function getTableDescription(): string
    {
        return 'Kelola dan monitor hasil pemeriksaan laboratorium pasien. Data akan diperbarui otomatis setiap 30 detik.';
    }
}