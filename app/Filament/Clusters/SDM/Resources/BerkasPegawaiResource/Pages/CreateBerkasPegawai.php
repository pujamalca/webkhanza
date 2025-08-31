<?php

namespace App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBerkasPegawai extends CreateRecord
{
    protected static string $resource = BerkasPegawaiResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        \Log::info('Attempting berkas_pegawai creation:', [
            'incoming_data' => $data,
            'berkas' => $data['berkas'] ?? 'not_set'
        ]);
        
        try {
            // Level 1: Normal Eloquent create
            $record = static::getModel()::create($data);
            
            // Generate route_key after creation
            if ($record && $record->berkas) {
                $filename = pathinfo($record->berkas, PATHINFO_FILENAME);
                $routeKey = 'bp_' . $filename;
                
                try {
                    // Try raw SQL to bypass MariaDB prepared statement issues
                    $escapedBerkas = addslashes($record->berkas);
                    $escapedRouteKey = addslashes($routeKey);
                    \DB::unprepared("UPDATE berkas_pegawai SET route_key = '{$escapedRouteKey}' WHERE berkas = '{$escapedBerkas}'");
                    
                    // Refresh the record to get the updated route_key
                    $record->refresh();
                    
                    \Log::info('Berkas_pegawai creation successful with route_key (raw SQL):', [
                        'berkas' => $record->berkas,
                        'route_key' => $record->route_key
                    ]);
                } catch (\Exception $e) {
                    \Log::info('Berkas_pegawai created successfully but route_key update failed (even with raw SQL):', [
                        'berkas' => $record->berkas,
                        'intended_route_key' => $routeKey,
                        'error' => $e->getMessage()
                    ]);
                    // Continue without failing - route binding will handle this
                }
            }
            
            return $record;
        } catch (\Exception $e1) {
            \Log::warning('Level 1 berkas_pegawai creation failed, trying direct DB insert:', [
                'error' => $e1->getMessage()
            ]);
            
            try {
                // Level 2: Direct DB insert as fallback
                $model = new (static::getModel());
                $tableName = $model->getTable();
                
                \DB::table($tableName)->insert($data);
                
                // Get the created record
                $record = static::getModel()::where('berkas', $data['berkas'])->first();
                
                if ($record) {
                    // Try to generate route_key
                    $filename = pathinfo($record->berkas, PATHINFO_FILENAME);
                    $routeKey = 'bp_' . $filename;
                    try {
                        $escapedBerkas = addslashes($record->berkas);
                        $escapedRouteKey = addslashes($routeKey);
                        \DB::unprepared("UPDATE berkas_pegawai SET route_key = '{$escapedRouteKey}' WHERE berkas = '{$escapedBerkas}'");
                        $record->refresh();
                    } catch (\Exception $e) {
                        \Log::info('Direct insert berkas_pegawai created but route_key update failed (raw SQL):', [
                            'berkas' => $record->berkas,
                            'intended_route_key' => $routeKey,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                \Log::info('Level 2 berkas_pegawai creation successful with direct insert');
                return $record;
            } catch (\Exception $e2) {
                \Log::error('Both levels of berkas_pegawai creation failed:', [
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