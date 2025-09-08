<?php

namespace App\Filament\Clusters\Marketing\Resources\BpjsTransferResource\Pages;

use App\Filament\Clusters\Marketing\Resources\BpjsTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditBpjsTransfer extends EditRecord
{
    protected static string $resource = BpjsTransferResource::class;

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
        return 'Data pindah BPJS berhasil diperbarui';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $currentRecord = $this->record;
        
        if (isset($data['is_edukasi_completed']) && $data['is_edukasi_completed']) {
            if (!$currentRecord->is_edukasi_completed) {
                // Baru dicentang
                $data['edukasi_completed_by'] = Auth::id();
                $data['edukasi_completed_at'] = now();
            }
        } else {
            // Dimatikan
            $data['edukasi_completed_by'] = null;
            $data['edukasi_completed_at'] = null;
        }

        return $data;
    }
}