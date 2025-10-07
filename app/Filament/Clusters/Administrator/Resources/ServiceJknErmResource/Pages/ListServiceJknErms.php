<?php

namespace App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource\Pages;

use App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Filament\Forms;

class ListServiceJknErms extends ListRecords
{
    protected static string $resource = ServiceJknErmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('validasi_taskid')
                ->label('Validasi Task ID')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->url(fn() => static::getResource()::getUrl('validasi-task-id')),

            \Filament\Actions\Action::make('antrean_per_tanggal')
                ->label('Antrean Per Tanggal')
                ->icon('heroicon-o-calendar-days')
                ->url(fn() => static::getResource()::getUrl('antrean-per-tanggal'))
                ->color('info'),

            Actions\CreateAction::make()
                ->label('Tambah Data')
                ->icon('heroicon-o-plus'),
        ];
    }
}
