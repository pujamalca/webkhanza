<?php

namespace App\Filament\Clusters\Administrator\Resources\Users\Pages;

use App\Filament\Clusters\Administrator\Resources\Users\UserResource;
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
        
        // Generate explicit ID for varchar(20) field
        $data['id'] = $this->generateUniqueUserId();
        
        return $data;
    }
    
    private function generateUniqueUserId(): string
    {
        // Get the last numeric ID from existing users
        $lastUser = \App\Models\User::whereRaw('id REGEXP "^[0-9]+$"')
            ->orderByRaw('CAST(id AS UNSIGNED) DESC')
            ->first();
            
        if ($lastUser && is_numeric($lastUser->id)) {
            $nextId = (string)((int)$lastUser->id + 1);
        } else {
            // If no numeric IDs exist, start from 2 (since admin is likely ID 1)
            $nextId = '2';
        }
        
        // Make sure the ID doesn't already exist (safety check)
        while (\App\Models\User::where('id', $nextId)->exists()) {
            $nextId = (string)((int)$nextId + 1);
        }
        
        return $nextId;
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
