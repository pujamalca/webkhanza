<?php

namespace App\Filament\Clusters\SDM\Resources\PetugasResource\Pages;

use App\Filament\Clusters\SDM\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

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
}