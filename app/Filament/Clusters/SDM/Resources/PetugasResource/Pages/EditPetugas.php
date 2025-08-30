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
                    
                    // Check common tables that might reference petugas
                    $tablesToCheck = [
                        'reg_periksa' => 'kd_pj',
                        'kamar_inap' => 'kd_kamar',
                        'operasi' => 'operator1',
                        'operasi' => 'operator2',
                        'operasi' => 'operator3',
                        'rawat_jl_dr' => 'nip',
                        'rawat_jl_pr' => 'nip',
                        'rawat_jl_drpr' => 'nip',
                        'rawat_inap_dr' => 'nip',
                        'rawat_inap_pr' => 'nip',
                        'rawat_inap_drpr' => 'nip',
                    ];
                    
                    foreach ($tablesToCheck as $table => $column) {
                        if (DB::table($table)->where($column, $record->nip)->exists()) {
                            $constraints[] = ucfirst(str_replace('_', ' ', $table));
                            break; // Avoid duplicates for same table
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
                    ->title('Gagal menyimpan data petugas')
                    ->body('Terjadi kesalahan database MariaDB. Silakan refresh halaman dan coba lagi. Jika masalah berlanjut, hubungi administrator sistem.')
                    ->persistent()
                    ->send();
                
                \Log::error('MariaDB prepared statement error in petugas update:', [
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
                
                \Log::error('Petugas update failed:', [
                    'id' => $record->getKey(),
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
                
                return $record;
            }
        }
    }
}