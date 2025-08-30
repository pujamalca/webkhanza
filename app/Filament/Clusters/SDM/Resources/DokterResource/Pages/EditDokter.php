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
                    
                    // Check common tables that might reference dokter
                    $tablesToCheck = [
                        'reg_periksa' => 'kd_dokter',
                        'jadwal' => 'kd_dokter',
                        'dokter_poli' => 'kd_dokter',
                        'beri_obat' => 'kd_dokter',
                        'pemeriksaan_ralan' => 'kd_dokter',
                        'pemeriksaan_ranap' => 'kd_dokter',
                    ];
                    
                    foreach ($tablesToCheck as $table => $column) {
                        if (DB::table($table)->where($column, $record->kd_dokter)->exists()) {
                            $constraints[] = ucfirst(str_replace('_', ' ', $table));
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
        try {
            // First attempt to update
            $record->update($data);
            return $record;
        } catch (\Exception $e) {
            // Check if it's a MariaDB prepared statement error
            if (str_contains($e->getMessage(), 'Prepared statement needs to be re-prepared') || 
                str_contains($e->getMessage(), '1615')) {
                
                // Show user-friendly notification for prepared statement error
                Notification::make()
                    ->danger()
                    ->title('Gagal menyimpan data dokter')
                    ->body('Terjadi kesalahan database MariaDB. Silakan refresh halaman dan coba lagi. Jika masalah berlanjut, hubungi administrator sistem.')
                    ->persistent()
                    ->send();
                
                \Log::error('MariaDB prepared statement error in dokter update:', [
                    'id' => $record->getKey(),
                    'data' => $data,
                    'error' => $e->getMessage(),
                    'suggestion' => 'User should refresh page and try again'
                ]);
                
                // Don't throw the exception, just return the original record
                return $record;
            } else {
                // Handle other database errors
                Notification::make()
                    ->danger()
                    ->title('Gagal menyimpan data')
                    ->body('Terjadi kesalahan database: ' . $e->getMessage())
                    ->persistent()
                    ->send();
                
                \Log::error('Dokter update failed:', [
                    'id' => $record->getKey(),
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
                
                return $record;
            }
        }
    }
}