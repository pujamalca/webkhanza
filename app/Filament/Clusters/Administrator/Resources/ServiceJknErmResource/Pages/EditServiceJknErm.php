<?php

namespace App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource\Pages;

use App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceJknErm extends EditRecord
{
    protected static string $resource = ServiceJknErmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data Service JKN ERM berhasil diupdate';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Format tanggal dan jam jika diperlukan
        if (isset($data['tanggal_periksa']) && $data['tanggal_periksa'] instanceof \DateTime) {
            $data['tanggal_periksa'] = $data['tanggal_periksa']->format('Y-m-d');
        }

        if (isset($data['jam_periksa']) && $data['jam_periksa'] instanceof \DateTime) {
            $data['jam_periksa'] = $data['jam_periksa']->format('H:i:s');
        }

        return $data;
    }
}
