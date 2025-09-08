<?php

namespace App\Filament\Clusters\Erm\Resources\QuickRegistrationResource\Pages;

use App\Filament\Clusters\Erm\Resources\QuickRegistrationResource;
use App\Models\RegPeriksa;
use App\Models\Pasien;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateQuickRegistration extends CreateRecord
{
    protected static string $resource = QuickRegistrationResource::class;

    public function getTitle(): string
    {
        return 'Registrasi Cepat Baru';
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Validate patient exists
        $patient = Pasien::where('no_rkm_medis', $data['no_rkm_medis'])->first();
        
        if (!$patient) {
            Notification::make()
                ->title('Error')
                ->body('Pasien tidak ditemukan. Silakan periksa No. RM.')
                ->danger()
                ->send();
                
            $this->halt();
        }

        // Generate registration number and no_rawat
        $today = now()->format('Y/m/d');
        $lastReg = RegPeriksa::whereDate('tgl_registrasi', today())->max('no_reg');
        $nextRegNo = str_pad(($lastReg ?? 0) + 1, 3, '0', STR_PAD_LEFT);
        $nextRegNoFormatted = str_pad(($lastReg ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        
        $noRawat = $today . '/' . $nextRegNoFormatted;

        // Create registration record
        $registration = RegPeriksa::create([
            'no_reg' => $nextRegNo,
            'no_rawat' => $noRawat,
            'tgl_registrasi' => now()->format('Y-m-d'),
            'jam_reg' => now()->format('H:i:s'),
            'kd_dokter' => $data['kd_dokter'],
            'no_rkm_medis' => $data['no_rkm_medis'],
            'kd_poli' => $data['kd_poli'],
            'p_jawab' => $patient->namakeluarga ?? '-',
            'almt_pj' => $patient->alamat ?? '-',
            'hubunganpj' => 'KELUARGA',
            'biaya_reg' => $data['biaya_reg'] ?? 0,
            'stts' => 'Belum',
            'stts_daftar' => $data['stts_daftar'] ?? 'Lama',
            'status_lanjut' => $data['status_lanjut'] ?? 'Ralan',
            'kd_pj' => $data['kd_pj'],
            'umurdaftar' => $this->calculateAge($patient->tgl_lahir),
            'sttsumur' => $this->getAgeStatus($patient->tgl_lahir),
            'status_bayar' => 'Belum Bayar',
            'status_poli' => $data['stts_daftar'] ?? 'Lama',
        ]);

        Notification::make()
            ->title('Berhasil!')
            ->body("Registrasi berhasil dibuat dengan No. Rawat: {$noRawat}")
            ->success()
            ->send();

        return $registration;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private function calculateAge($birthDate): int
    {
        if (!$birthDate) return 0;
        
        return \Carbon\Carbon::parse($birthDate)->age;
    }

    private function getAgeStatus($birthDate): string
    {
        if (!$birthDate) return 'Th';
        
        $age = \Carbon\Carbon::parse($birthDate)->age;
        
        if ($age < 1) return 'Bl'; // Bulan
        return 'Th'; // Tahun
    }
}