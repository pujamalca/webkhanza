<?php

namespace App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\PegawaiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, $record) {
                    // Check if pegawai is referenced by other tables
                    $constraints = [];
                    
                    // Check if this pegawai is used as a dokter
                    if (DB::table('dokter')->where('kd_dokter', $record->nik)->exists()) {
                        $constraints[] = 'Dokter';
                    }
                    
                    // Check other possible references (you can add more as needed)
                    if (DB::table('petugas')->where('nip', $record->nik)->exists()) {
                        $constraints[] = 'Petugas';
                    }
                    
                    if (!empty($constraints)) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Tidak dapat menghapus pegawai')
                            ->body('Data pegawai ini sedang digunakan pada: ' . implode(', ', $constraints) . '. Hapus data terkait terlebih dahulu sebelum menghapus pegawai.')
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
        \Log::info('Attempting pegawai update:', [
            'id' => $record->getKey(),
            'data_keys' => array_keys($data)
        ]);
        
        try {
            // First attempt to update
            $record->update($data);
            
            // Log successful update
            \Log::info('Pegawai update successful:', [
                'id' => $record->getKey()
            ]);
            
            return $record;
        } catch (\Exception $e) {
            // Check if it's a MariaDB prepared statement error
            if (str_contains($e->getMessage(), 'Prepared statement needs to be re-prepared') || 
                str_contains($e->getMessage(), '1615')) {
                
                \Log::warning('MariaDB prepared statement error detected, attempting alternative update...', [
                    'id' => $record->getKey(),
                    'error' => $e->getMessage()
                ]);
                
                try {
                    // Force complete database reconnection
                    DB::purge('mariadb'); 
                    DB::disconnect('mariadb');
                    
                    // Wait a moment for connection cleanup
                    usleep(100000); // 100ms
                    
                    // Use a new connection for the retry
                    $freshConnection = DB::connection('mariadb');
                    
                    // Try with fresh Eloquent update first
                    $freshRecord = $freshConnection->table('pegawai')->where('id', $record->getKey())->first();
                    if ($freshRecord) {
                        $result = $freshConnection->table('pegawai')
                            ->where('id', $record->getKey())
                            ->update($data);
                        
                        if ($result) {
                            // Refresh the model to reflect changes
                            $record->refresh();
                            
                            Notification::make()
                                ->success()
                                ->title('Data berhasil disimpan')
                                ->body('Pegawai berhasil diperbarui.')
                                ->send();
                            
                            \Log::info('Fresh connection update successful:', [
                                'id' => $record->getKey()
                            ]);
                            
                            return $record;
                        }
                    }
                    
                } catch (\Exception $retryException) {
                    \Log::error('Fresh connection update also failed:', [
                        'id' => $record->getKey(),
                        'error' => $retryException->getMessage()
                    ]);
                    
                    // Last resort: try unprepared statement
                    try {
                        \Log::info('Attempting last resort unprepared update...');
                        
                        $id = $record->getKey();
                        $setParts = [];
                        
                        foreach ($data as $key => $value) {
                            if ($key !== 'id') {
                                $escaped_value = is_string($value) ? "'" . addslashes($value) . "'" : $value;
                                $setParts[] = "`{$key}` = {$escaped_value}";
                            }
                        }
                        
                        if (!empty($setParts)) {
                            $sql = "UPDATE pegawai SET " . implode(', ', $setParts) . " WHERE id = {$id}";
                            
                            DB::unprepared($sql);
                            
                            // Refresh the model to reflect changes
                            $record->refresh();
                            
                            Notification::make()
                                ->success()
                                ->title('Data berhasil disimpan')
                                ->body('Pegawai berhasil diperbarui.')
                                ->send();
                            
                            \Log::info('Unprepared update successful:', [
                                'id' => $record->getKey()
                            ]);
                            
                            return $record;
                        }
                    } catch (\Exception $lastResortException) {
                        \Log::error('Last resort update also failed:', [
                            'id' => $record->getKey(),
                            'error' => $lastResortException->getMessage()
                        ]);
                    }
                }
                
                // Show user-friendly notification for prepared statement error
                Notification::make()
                    ->danger()
                    ->title('Gagal menyimpan data pegawai')
                    ->body('Terjadi kesalahan database MariaDB. Silakan refresh halaman dan coba lagi. Jika masalah berlanjut, hubungi administrator sistem.')
                    ->persistent()
                    ->send();
                
                return $record;
            } else {
                // Handle other database errors
                Notification::make()
                    ->danger()
                    ->title('Gagal menyimpan data')
                    ->body('Terjadi kesalahan database: ' . $e->getMessage())
                    ->persistent()
                    ->send();
                
                \Log::error('Pegawai update failed:', [
                    'id' => $record->getKey(),
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
                
                return $record;
            }
        }
    }
}