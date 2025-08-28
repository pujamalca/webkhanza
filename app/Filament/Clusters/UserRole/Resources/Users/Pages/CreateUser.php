<?php

namespace App\Filament\Clusters\UserRole\Resources\Users\Pages;

use App\Filament\Clusters\UserRole\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    protected $createdRecordId;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove pegawai_nik from data as it's not a user field
        unset($data['pegawai_nik']);
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Store the actual created record ID
        $this->createdRecordId = $this->getRecord()->id;
        
        \Log::info('User created with ID: ' . $this->createdRecordId);
    }

    protected function getRedirectUrl(): string
    {
        // Always redirect to index to avoid ID issues
        return $this->getResource()::getUrl('index');
    }
}
