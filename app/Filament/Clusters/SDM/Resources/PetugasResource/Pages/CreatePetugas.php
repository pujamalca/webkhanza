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
            
            // Generate route_key after creation
            if ($record && $record->nip) {
                $routeKey = 'pg_' . str_replace('/', '_', $record->nip);
                
                try {
                    // Try raw SQL to bypass MariaDB prepared statement issues
                    $escapedNip = addslashes($record->nip);
                    $escapedRouteKey = addslashes($routeKey);
                    \DB::unprepared("UPDATE petugas SET route_key = '{$escapedRouteKey}' WHERE nip = '{$escapedNip}'");
                    
                    // Refresh the record to get the updated route_key
                    $record->refresh();
                    
                    \Log::info('Petugas creation successful with route_key (raw SQL):', [
                        'id' => $record->getKey(),
                        'nip' => $record->nip,
                        'route_key' => $record->route_key
                    ]);
                } catch (\Exception $e) {
                    \Log::info('Petugas created successfully but route_key update failed (even with raw SQL):', [
                        'id' => $record->getKey(),
                        'nip' => $record->nip,
                        'intended_route_key' => $routeKey,
                        'error' => $e->getMessage()
                    ]);
                    // Continue without failing - route binding will handle this
                }
            }
            
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
                
                if ($record) {
                    // Try to generate route_key
                    $routeKey = 'pg_' . str_replace('/', '_', $record->nip);
                    try {
                        // Try raw SQL to bypass MariaDB prepared statement issues
                        $escapedNip = addslashes($record->nip);
                        $escapedRouteKey = addslashes($routeKey);
                        \DB::unprepared("UPDATE petugas SET route_key = '{$escapedRouteKey}' WHERE nip = '{$escapedNip}'");
                        $record->refresh();
                    } catch (\Exception $e) {
                        \Log::info('Direct insert petugas created but route_key update failed (raw SQL):', [
                            'nip' => $record->nip,
                            'intended_route_key' => $routeKey,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
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