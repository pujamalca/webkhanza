<?php

namespace App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource;
use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Widgets\BerkasPegawaiExpirationWidget;
use App\Notifications\BerkasPegawaiExpirationNotification;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBerkasPegawai extends ListRecords
{
    protected static string $resource = BerkasPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            BerkasPegawaiExpirationWidget::class,
        ];
    }
    
    public function mount(): void
    {
        parent::mount();
        
        // Send notification when page is accessed
        BerkasPegawaiExpirationNotification::sendToCurrentUser();
    }
}