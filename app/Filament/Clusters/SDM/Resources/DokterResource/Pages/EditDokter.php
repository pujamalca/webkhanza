<?php

namespace App\Filament\Clusters\SDM\Resources\DokterResource\Pages;

use App\Filament\Clusters\SDM\Resources\DokterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class EditDokter extends EditRecord
{
    protected static string $resource = DokterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, $record) {
                    // Check if dokter is referenced by other tables
                    $constraints = [];
                    
                    // Check common tables that might reference dokter by kd_dokter
                    $tablesToCheck = [
                        'reg_periksa' => 'kd_dokter',
                        'jadwal' => 'kd_dokter',
                        'pemeriksaan_ralan' => 'kd_dokter',
                        'pemeriksaan_ranap' => 'kd_dokter',
                        'rawat_jl_dr' => 'kd_dokter',
                        'rawat_inap_dr' => 'kd_dokter',
                        'operasi' => 'dokter_operator',
                        'rujukan_keluar' => 'kd_dokter',
                    ];
                    
                    foreach ($tablesToCheck as $table => $column) {
                        try {
                            if (DB::table($table)->where($column, $record->kd_dokter)->exists()) {
                                $constraints[] = ucfirst(str_replace('_', ' ', $table));
                            }
                        } catch (\Exception $e) {
                            // Skip if table/column doesn't exist or query fails
                            \Log::info("Skipping constraint check for {$table}.{$column}: " . $e->getMessage());
                            continue;
                        }
                    }
                    
                    if (!empty($constraints)) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus dokter')
                            ->body('Data dokter ini sedang digunakan pada: ' . implode(', ', $constraints) . '. Hapus data terkait terlebih dahulu sebelum menghapus dokter.')
                            ->persistent()
                            ->send();
                        
                        // Cancel the delete action
                        $action->cancel();
                    }
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Ensure status field is properly cast to string for enum database column
        if (isset($data['status'])) {
            $data['status'] = $data['status'] ? '1' : '0';
        }
        
        \Log::info('Attempting dokter update:', [
            'id' => $record->getKey(),
            'kd_dokter' => $record->kd_dokter,
            'current_route_key' => $record->route_key,
            'data_keys' => array_keys($data),
            'status_value' => $data['status'] ?? 'not_set',
            'status_type' => gettype($data['status'] ?? null)
        ]);
        
        try {
            // Level 1: Normal Eloquent update
            $record->update($data);
            
            // Generate route_key if missing
            if ($record->kd_dokter && !$record->route_key) {
                $routeKey = 'dr_' . str_replace('/', '_', $record->kd_dokter);
                try {
                    // Try raw SQL to bypass MariaDB prepared statement issues
                    $escapedKdDokter = addslashes($record->kd_dokter);
                    $escapedRouteKey = addslashes($routeKey);
                    \DB::unprepared("UPDATE dokter SET route_key = '{$escapedRouteKey}' WHERE kd_dokter = '{$escapedKdDokter}'");
                    $record->refresh();
                    
                    \Log::info('Route_key generated during update (raw SQL):', [
                        'kd_dokter' => $record->kd_dokter,
                        'route_key' => $record->route_key
                    ]);
                } catch (\Exception $e) {
                    \Log::info('Route_key generation failed during update (even raw SQL):', [
                        'kd_dokter' => $record->kd_dokter,
                        'intended_route_key' => $routeKey,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            \Log::info('Level 1 dokter update successful:', [
                'id' => $record->getKey(),
                'final_status' => $record->status,
                'final_status_type' => gettype($record->status)
            ]);
            return $record;
        } catch (\Exception $e1) {
            \Log::warning('Level 1 dokter update failed, trying fresh connection:', [
                'error' => $e1->getMessage(),
                'id' => $record->getKey()
            ]);
            
            try {
                // Level 2: Fresh connection update
                $freshRecord = $record->newQuery()->find($record->getKey());
                $freshRecord->update($data);
                $freshRecord->refresh();
                
                \Log::info('Level 2 dokter update successful with fresh connection:', [
                    'id' => $record->getKey(),
                    'final_status' => $freshRecord->status,
                    'final_status_type' => gettype($freshRecord->status)
                ]);
                return $freshRecord;
            } catch (\Exception $e2) {
                \Log::warning('Level 2 dokter update failed, trying unprepared statement:', [
                    'error' => $e2->getMessage(),
                    'id' => $record->getKey()
                ]);
                
                try {
                    // Level 3: Raw unprepared statement as last resort
                    $setParts = [];
                    foreach ($data as $column => $value) {
                        if ($value === null) {
                            $setParts[] = "`{$column}` = NULL";
                        } else {
                            $escapedValue = addslashes($value);
                            $setParts[] = "`{$column}` = '{$escapedValue}'";
                        }
                    }
                    $setClause = implode(', ', $setParts);
                    $primaryKey = $record->getKey();
                    $escapedPrimaryKey = addslashes($primaryKey);
                    
                    \DB::unprepared("UPDATE dokter SET {$setClause} WHERE kd_dokter = '{$escapedPrimaryKey}'");
                    
                    // Reload the record to get fresh data
                    $record = $record->fresh();
                    
                    \Log::info('Level 3 dokter update successful with unprepared statement:', [
                        'id' => $record->getKey(),
                        'final_status' => $record->status,
                        'final_status_type' => gettype($record->status)
                    ]);
                    return $record;
                } catch (\Exception $e3) {
                    \Log::error('All 3 levels of dokter update failed:', [
                        'id' => $record->getKey(),
                        'level1_error' => $e1->getMessage(),
                        'level2_error' => $e2->getMessage(),
                        'level3_error' => $e3->getMessage(),
                        'data' => $data
                    ]);
                    
                    // Show user-friendly notification
                    Notification::make()
                        ->danger()
                        ->title('Gagal menyimpan data dokter')
                        ->body('Terjadi kesalahan database yang tidak dapat diatasi. Silakan hubungi administrator sistem.')
                        ->persistent()
                        ->send();
                    
                    return $record; // Return original record
                }
            }
        }
    }
}