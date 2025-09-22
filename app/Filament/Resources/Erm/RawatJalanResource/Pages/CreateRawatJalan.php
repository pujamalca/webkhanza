<?php

namespace App\Filament\Resources\Erm\RawatJalanResource\Pages;

use App\Filament\Resources\Erm\RawatJalanResource;
use App\Models\RegPeriksa;
use Filament\Resources\Pages\CreateRecord;

class CreateRawatJalan extends CreateRecord
{
    protected static string $resource = RawatJalanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure status_lanjut is set
        $data['status_lanjut'] = 'Ralan';
        
        // Ensure stts is set to Belum
        $data['stts'] = 'Belum';
        
        // Ensure status_bayar is set
        $data['status_bayar'] = 'Belum Bayar';
        
        // Generate no_reg properly with leading zeros
        if ($data['kd_poli']) {
            $maxReg = RegPeriksa::whereDate('tgl_registrasi', now())
                ->where('kd_poli', $data['kd_poli'])
                ->max('no_reg');
            $data['no_reg'] = str_pad(($maxReg ?? 0) + 1, 3, '0', STR_PAD_LEFT);
        }
        
        return $data;
    }
}