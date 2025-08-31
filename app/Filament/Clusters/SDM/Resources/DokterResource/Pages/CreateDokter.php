<?php

namespace App\Filament\Clusters\SDM\Resources\DokterResource\Pages;

use App\Filament\Clusters\SDM\Resources\DokterResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDokter extends CreateRecord
{
    protected static string $resource = DokterResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Ensure status field is properly cast to string for enum database column
        if (isset($data['status'])) {
            $data['status'] = $data['status'] ? '1' : '0';
        }
        
        \Log::info('Attempting dokter creation:', [
            'incoming_data' => $data,
            'kd_dokter' => $data['kd_dokter'] ?? 'not_set',
            'status_value' => $data['status'] ?? 'not_set',
            'status_type' => gettype($data['status'] ?? null)
        ]);
        
        try {
            // Level 1: Normal Eloquent create
            $record = static::getModel()::create($data);
            
            // Generate route_key after creation
            if ($record && $record->kd_dokter) {
                $routeKey = 'dr_' . str_replace('/', '_', $record->kd_dokter);
                
                try {
                    // Try raw SQL to bypass MariaDB prepared statement issues
                    $escapedKdDokter = addslashes($record->kd_dokter);
                    $escapedRouteKey = addslashes($routeKey);
                    \DB::unprepared("UPDATE dokter SET route_key = '{$escapedRouteKey}' WHERE kd_dokter = '{$escapedKdDokter}'");
                    
                    // Refresh the record to get the updated route_key
                    $record->refresh();
                    
                    \Log::info('Dokter creation successful with route_key (raw SQL):', [
                        'id' => $record->getKey(),
                        'kd_dokter' => $record->kd_dokter,
                        'route_key' => $record->route_key
                    ]);
                } catch (\Exception $e) {
                    \Log::info('Dokter created successfully but route_key update failed (even with raw SQL):', [
                        'id' => $record->getKey(),
                        'kd_dokter' => $record->kd_dokter,
                        'intended_route_key' => $routeKey,
                        'error' => $e->getMessage()
                    ]);
                    // Continue without failing - route binding will handle this
                }
            }
            
            return $record;
        } catch (\Exception $e1) {
            \Log::warning('Level 1 dokter creation failed, trying direct DB insert:', [
                'error' => $e1->getMessage()
            ]);
            
            try {
                // Level 2: Direct DB insert as fallback
                $model = new (static::getModel());
                $tableName = $model->getTable();
                
                \DB::table($tableName)->insert($data);
                
                // Get the created record
                $record = static::getModel()::where('kd_dokter', $data['kd_dokter'])->first();
                
                if ($record) {
                    // Try to generate route_key
                    $routeKey = 'dr_' . str_replace('/', '_', $record->kd_dokter);
                    try {
                        // Try raw SQL to bypass MariaDB prepared statement issues
                        $escapedKdDokter = addslashes($record->kd_dokter);
                        $escapedRouteKey = addslashes($routeKey);
                        \DB::unprepared("UPDATE dokter SET route_key = '{$escapedRouteKey}' WHERE kd_dokter = '{$escapedKdDokter}'");
                        $record->refresh();
                    } catch (\Exception $e) {
                        \Log::info('Direct insert dokter created but route_key update failed (raw SQL):', [
                            'kd_dokter' => $record->kd_dokter,
                            'intended_route_key' => $routeKey,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                \Log::info('Level 2 dokter creation successful with direct insert:', [
                    'kd_dokter' => $data['kd_dokter']
                ]);
                return $record;
            } catch (\Exception $e2) {
                \Log::error('Both levels of dokter creation failed:', [
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