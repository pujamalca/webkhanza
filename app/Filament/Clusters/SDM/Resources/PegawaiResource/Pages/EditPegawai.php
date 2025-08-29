<?php

namespace App\Filament\Clusters\SDM\Resources\PegawaiResource\Pages;

use App\Filament\Clusters\SDM\Resources\PegawaiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

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
}