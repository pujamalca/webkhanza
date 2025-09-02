<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsent extends CreateRecord
{
    protected static string $resource = AbsentResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!auth()->user()->can('view_all_absent')) {
            $data['employee_id'] = auth()->id();
        }
        
        return $data;
    }
}