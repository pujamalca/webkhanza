<?php

namespace App\Filament\Clusters\SDM\Resources\DokterResource\Pages;

use App\Filament\Clusters\SDM\Resources\DokterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

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
}