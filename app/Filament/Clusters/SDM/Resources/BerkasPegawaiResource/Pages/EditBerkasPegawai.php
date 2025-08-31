<?php

namespace App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class EditBerkasPegawai extends EditRecord
{
    protected static string $resource = BerkasPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        \Log::info('Attempting berkas_pegawai update:', [
            'berkas' => $record->berkas,
            'current_route_key' => $record->route_key,
            'data_keys' => array_keys($data)
        ]);
        
        try {
            // Level 1: Normal Eloquent update
            $record->update($data);
            
            // Generate route_key if missing
            if ($record->berkas && !$record->route_key) {
                $filename = pathinfo($record->berkas, PATHINFO_FILENAME);
                $routeKey = 'bp_' . $filename;
                try {
                    // Try raw SQL to bypass MariaDB prepared statement issues
                    $escapedBerkas = addslashes($record->berkas);
                    $escapedRouteKey = addslashes($routeKey);
                    \DB::unprepared("UPDATE berkas_pegawai SET route_key = '{$escapedRouteKey}' WHERE berkas = '{$escapedBerkas}'");
                    $record->refresh();
                    
                    \Log::info('Route_key generated during berkas_pegawai update (raw SQL):', [
                        'berkas' => $record->berkas,
                        'route_key' => $record->route_key
                    ]);
                } catch (\Exception $e) {
                    \Log::info('Route_key generation failed during berkas_pegawai update (even raw SQL):', [
                        'berkas' => $record->berkas,
                        'intended_route_key' => $routeKey,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            \Log::info('Level 1 berkas_pegawai update successful');
            return $record;
        } catch (\Exception $e1) {
            \Log::warning('Level 1 berkas_pegawai update failed, trying raw SQL:', [
                'error' => $e1->getMessage()
            ]);
            
            try {
                // Level 2: Raw unprepared statement as fallback
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
                $escapedBerkas = addslashes($record->berkas);
                
                \DB::unprepared("UPDATE berkas_pegawai SET {$setClause} WHERE berkas = '{$escapedBerkas}'");
                
                // Reload the record to get fresh data
                $record = $record->fresh();
                
                \Log::info('Level 2 berkas_pegawai update successful with unprepared statement');
                return $record;
            } catch (\Exception $e2) {
                \Log::error('Both levels of berkas_pegawai update failed:', [
                    'berkas' => $record->berkas,
                    'level1_error' => $e1->getMessage(),
                    'level2_error' => $e2->getMessage(),
                    'data' => $data
                ]);
                
                // Show user-friendly notification
                Notification::make()
                    ->danger()
                    ->title('Gagal menyimpan data berkas pegawai')
                    ->body('Terjadi kesalahan database yang tidak dapat diatasi. Silakan hubungi administrator sistem.')
                    ->persistent()
                    ->send();
                
                return $record; // Return original record
            }
        }
    }
}