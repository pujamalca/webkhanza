<?php

namespace App\Filament\Clusters\Pegawai\Resources\CutiResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\CutiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCuti extends CreateRecord
{
    protected static string $resource = CutiResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!auth()->user()->can('view_all_cuti')) {
            $data['employee_id'] = auth()->id();
        }
        
        // Set default status to pending for new requests
        $data['status'] = 'pending';
        
        return $data;
    }
}