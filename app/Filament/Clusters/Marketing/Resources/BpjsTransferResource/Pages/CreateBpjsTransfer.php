<?php

namespace App\Filament\Clusters\Marketing\Resources\BpjsTransferResource\Pages;

use App\Filament\Clusters\Marketing\Resources\BpjsTransferResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBpjsTransfer extends CreateRecord
{
    protected static string $resource = BpjsTransferResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data pindah BPJS berhasil ditambahkan';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        
        if ($data['is_edukasi_completed'] ?? false) {
            $data['edukasi_completed_by'] = Auth::id();
            $data['edukasi_completed_at'] = now();
        }

        return $data;
    }
}