<?php

namespace App\Filament\Clusters\Pegawai\Resources\CutiResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\CutiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCuti extends EditRecord
{
    protected static string $resource = CutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat'),
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update approved_at when status changes to approved or rejected
        if (in_array($data['status'] ?? null, ['approved', 'rejected'])) {
            if (!$this->record->approved_at) {
                $data['approved_at'] = now();
                $data['approved_by'] = auth()->id();
            }
        }
        
        return $data;
    }
}