<?php

namespace App\Filament\Clusters\SDM\Resources\PetugasResource\Pages;

use App\Filament\Clusters\SDM\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class EditPetugas extends EditRecord
{
    protected static string $resource = PetugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action, $record) {
                    // Check if petugas is referenced by other tables
                    $constraints = [];
                    
                    // Check common tables that might reference petugas by NIP
                    $tablesToCheck = [
                        'reg_periksa' => ['kd_pj'],
                        'operasi' => ['operator1', 'operator2', 'operator3'],
                        'rawat_jl_pr' => ['nip'],
                        'rawat_inap_pr' => ['nip'],
                        'kamar_inap' => ['nip'],
                        'pemeriksaan_ralan' => ['nip'],
                        'pemeriksaan_ranap' => ['nip'],
                        'bridging_sep' => ['nip'],
                    ];
                    
                    foreach ($tablesToCheck as $table => $columns) {
                        $tableFound = false;
                        foreach ($columns as $column) {
                            try {
                                if (DB::table($table)->where($column, $record->nip)->exists()) {
                                    $constraints[] = ucfirst(str_replace('_', ' ', $table));
                                    $tableFound = true;
                                    break; // Found constraint in this table, move to next table
                                }
                            } catch (\Exception $e) {
                                // Skip if table/column doesn't exist or query fails
                                \Log::info("Skipping constraint check for {$table}.{$column}: " . $e->getMessage());
                                continue;
                            }
                        }
                    }
                    
                    if (!empty($constraints)) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus petugas')
                            ->body('Data petugas ini sedang digunakan pada: ' . implode(', ', array_unique($constraints)) . '. Hapus data terkait terlebih dahulu sebelum menghapus petugas.')
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
            $data['status'] = (string) $data['status'];
        }
        
        \Log::info('Attempting petugas update:', [
            'id' => $record->getKey(),
            'original_record' => $record->toArray(),
            'incoming_data' => $data,
            'status_value' => $data['status'] ?? 'not_set',
            'status_type' => gettype($data['status'] ?? null),
            'current_status_in_db' => $record->status,
            'current_status_type' => gettype($record->status)
        ]);
        
        try {
            // Level 1: Normal Eloquent update
            $record->update($data);
            
            // Generate route_key if missing
            if ($record->nip && !$record->route_key) {
                $routeKey = 'pg_' . str_replace('/', '_', $record->nip);
                try {
                    // Try raw SQL to bypass MariaDB prepared statement issues
                    $escapedNip = addslashes($record->nip);
                    $escapedRouteKey = addslashes($routeKey);
                    \DB::unprepared("UPDATE petugas SET route_key = '{$escapedRouteKey}' WHERE nip = '{$escapedNip}'");
                    $record->refresh();
                    
                    \Log::info('Route_key generated during petugas update (raw SQL):', [
                        'nip' => $record->nip,
                        'route_key' => $record->route_key
                    ]);
                } catch (\Exception $e) {
                    \Log::info('Route_key generation failed during petugas update (even raw SQL):', [
                        'nip' => $record->nip,
                        'intended_route_key' => $routeKey,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                $record->refresh();
            }
            
            \Log::info('Level 1 petugas update successful:', [
                'id' => $record->getKey(),
                'final_status' => $record->status,
                'final_status_type' => gettype($record->status),
                'all_attributes' => $record->toArray()
            ]);
            return $record;
        } catch (\Exception $e1) {
            \Log::warning('Level 1 petugas update failed, trying fresh connection:', [
                'error' => $e1->getMessage(),
                'id' => $record->getKey()
            ]);
            
            try {
                // Level 2: Fresh connection update
                $freshRecord = $record->newQuery()->find($record->getKey());
                $freshRecord->update($data);
                $freshRecord->refresh();
                \Log::info('Level 2 petugas update successful with fresh connection:', [
                    'id' => $record->getKey(),
                    'final_status' => $freshRecord->status,
                    'final_status_type' => gettype($freshRecord->status),
                    'all_attributes' => $freshRecord->toArray()
                ]);
                return $freshRecord;
            } catch (\Exception $e2) {
                \Log::warning('Level 2 petugas update failed, trying unprepared statement:', [
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
                    
                    \DB::unprepared("UPDATE petugas SET {$setClause} WHERE nip = '{$primaryKey}'");
                    
                    // Reload the record to get fresh data
                    $record = $record->fresh();
                    
                    \Log::info('Level 3 petugas update successful with unprepared statement:', [
                        'id' => $record->getKey(),
                        'final_status' => $record->status,
                        'final_status_type' => gettype($record->status),
                        'all_attributes' => $record->toArray()
                    ]);
                    return $record;
                } catch (\Exception $e3) {
                    \Log::error('All 3 levels of petugas update failed:', [
                        'id' => $record->getKey(),
                        'level1_error' => $e1->getMessage(),
                        'level2_error' => $e2->getMessage(),
                        'level3_error' => $e3->getMessage(),
                        'data' => $data
                    ]);
                    
                    // Show user-friendly notification
                    Notification::make()
                        ->danger()
                        ->title('Gagal menyimpan data petugas')
                        ->body('Terjadi kesalahan database yang tidak dapat diatasi. Silakan hubungi administrator sistem.')
                        ->persistent()
                        ->send();
                    
                    return $record; // Return original record
                }
            }
        }
    }
}