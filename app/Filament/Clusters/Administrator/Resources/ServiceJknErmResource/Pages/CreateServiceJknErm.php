<?php

namespace App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource\Pages;

use App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferensiMobilejknBpjsErm;

class CreateServiceJknErm extends CreateRecord
{
    protected static string $resource = ServiceJknErmResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data Service JKN ERM berhasil ditambahkan';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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

    protected function handleRecordCreation(array $data): Model
    {
        // Check jika data dengan tanggal, jam, dan no_rkm_medis yang sama sudah ada
        $existing = ReferensiMobilejknBpjsErm::where('tanggal_periksa', $data['tanggal_periksa'])
            ->where('jam_periksa', $data['jam_periksa'])
            ->where('no_rkm_medis', $data['no_rkm_medis'])
            ->first();

        if ($existing) {
            // Update existing record instead of creating new one
            $existing->update($data);

            // Show notification
            \Filament\Notifications\Notification::make()
                ->title('Data sudah ada dan berhasil diupdate')
                ->success()
                ->send();

            return $existing;
        }

        return static::getModel()::create($data);
    }
}
