<?php

namespace App\Filament\Clusters\SDM\Resources\PetugasResource\Pages;

use App\Filament\Clusters\SDM\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePetugas extends CreateRecord
{
    protected static string $resource = PetugasResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Ensure status field is properly cast to string for enum database column
        if (isset($data['status'])) {
            $data['status'] = (string) $data['status'];
        }
        
        \Log::info('Attempting petugas creation:', [
            'incoming_data' => $data,
            'status_value' => $data['status'] ?? 'not_set',
            'status_type' => gettype($data['status'] ?? null)
        ]);
        
        try {
            // Level 1: Normal Eloquent create
            $record = static::getModel()::create($data);
            \Log::info('Level 1 petugas creation successful:', [
                'id' => $record->getKey(),
                'final_status' => $record->status,
                'final_status_type' => gettype($record->status),
                'all_attributes' => $record->toArray()
            ]);
            return $record;
        } catch (\Exception $e1) {
            \Log::warning('Level 1 petugas creation failed, trying direct DB insert:', [
                'error' => $e1->getMessage()
            ]);
            
            try {
                // Level 2: Direct DB insert as fallback
                $model = new (static::getModel());
                $tableName = $model->getTable();
                
                \DB::table($tableName)->insert($data);
                
                // Get the created record
                $record = static::getModel()::where('nip', $data['nip'])->first();
                
                \Log::info('Level 2 petugas creation successful with direct insert:', [
                    'id' => $record->getKey(),
                    'final_status' => $record->status,
                    'final_status_type' => gettype($record->status),
                    'all_attributes' => $record->toArray()
                ]);
                return $record;
            } catch (\Exception $e2) {
                \Log::error('Both levels of petugas creation failed:', [
                    'level1_error' => $e1->getMessage(),
                    'level2_error' => $e2->getMessage(),
                    'data' => $data
                ]);
                
                throw $e1; // Re-throw original exception
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        // Always redirect to index to avoid ID issues after creation
        return $this->getResource()::getUrl('index');
    }
}