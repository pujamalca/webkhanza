<?php

namespace App\Livewire;

use App\Models\PemeriksaanRalan;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Validate;

class PemeriksaanRalanForm extends Component
{
    public string $noRawat;
    
    #[Validate('required|date')]
    public $tgl_perawatan;
    
    #[Validate('required')]
    public $jam_rawat;
    
    #[Validate('nullable|numeric')]
    public $suhu_tubuh;
    
    #[Validate('nullable')]
    public $tensi;
    
    #[Validate('nullable|numeric')]
    public $nadi;
    
    #[Validate('nullable|numeric')]
    public $respirasi;
    
    #[Validate('nullable|numeric|min:0|max:100')]
    public $spo2;
    
    #[Validate('nullable|numeric')]
    public $tinggi;
    
    #[Validate('nullable|numeric')]
    public $berat;
    
    #[Validate('nullable')]
    public $gcs;
    
    #[Validate('nullable')]
    public $kesadaran;
    
    // SOAP fields
    #[Validate('nullable')]
    public $keluhan; // Subjective
    
    #[Validate('nullable')]
    public $pemeriksaan; // Objective
    
    #[Validate('nullable')]
    public $penilaian; // Assessment
    
    #[Validate('nullable')]
    public $rtl; // Plan
    
    #[Validate('nullable')]
    public $instruksi; // Intervention
    
    #[Validate('nullable')]
    public $evaluasi; // Evaluation
    
    #[Validate('nullable|max:50')]
    public $alergi;
    
    #[Validate('nullable|numeric')]
    public $lingkar_perut;

    // History data
    public $riwayatPemeriksaan = [];

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        $this->resetForm();
        $this->loadRiwayat();
    }
    
    public function simpanPemeriksaan(): void
    {
        $this->validate();

        $data = [
            'no_rawat' => $this->noRawat,
            'tgl_perawatan' => $this->tgl_perawatan,
            'jam_rawat' => $this->jam_rawat,
            'suhu_tubuh' => $this->suhu_tubuh,
            'tensi' => $this->tensi,
            'nadi' => $this->nadi,
            'respirasi' => $this->respirasi,
            'spo2' => $this->spo2,
            'tinggi' => $this->tinggi,
            'berat' => $this->berat,
            'gcs' => $this->gcs,
            'kesadaran' => $this->kesadaran,
            'keluhan' => $this->keluhan,
            'pemeriksaan' => $this->pemeriksaan,
            'penilaian' => $this->penilaian,
            'alergi' => $this->alergi,
            'lingkar_perut' => $this->lingkar_perut,
            'rtl' => $this->rtl,
            'instruksi' => $this->instruksi,
            'evaluasi' => $this->evaluasi,
            'nip' => auth()->user()->pegawai->nik ?? '-',
        ];

        PemeriksaanRalan::create($data);

        $this->resetForm();
        $this->loadRiwayat();

        Notification::make()
            ->title('Pemeriksaan SOAP berhasil disimpan')
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat = now()->format('H:i');
        $this->suhu_tubuh = '';
        $this->tensi = '';
        $this->nadi = '';
        $this->respirasi = '';
        $this->spo2 = '';
        $this->tinggi = '';
        $this->berat = '';
        $this->gcs = '';
        $this->kesadaran = '';
        $this->keluhan = '';
        $this->pemeriksaan = '';
        $this->penilaian = '';
        $this->alergi = '';
        $this->lingkar_perut = '';
        $this->rtl = '';
        $this->instruksi = '';
        $this->evaluasi = '';
    }
    
    public function loadRiwayat(): void
    {
        $this->riwayatPemeriksaan = PemeriksaanRalan::where('no_rawat', $this->noRawat)
            ->with(['petugas:nik,nama'])
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get()
            ->toArray();
    }
    
    public function render()
    {
        return view('livewire.pemeriksaan-ralan-form');
    }
}