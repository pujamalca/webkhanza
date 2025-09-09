<?php

namespace App\Livewire;

use App\Models\PemeriksaanRalan;
use Filament\Notifications\Notification;
use Livewire\Component;

class PemeriksaanRalanForm extends Component
{
    
    public $no_rawat;
    
    // Form data properties
    public $tgl_perawatan;
    public $jam_rawat;
    public $suhu_tubuh;
    public $tensi;
    public $nadi;
    public $respirasi;
    public $tinggi;
    public $berat;
    public $spo2;
    public $gcs;
    public $kesadaran;
    public $lingkar_perut;
    public $keluhan;
    public $pemeriksaan;
    public $alergi;
    public $penilaian;
    public $rtl;
    public $instruksi;
    public $evaluasi;
    
    public function mount($noRawat)
    {
        $this->no_rawat = $noRawat;
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat = now()->format('H:i');
    }
    
    public function getPemeriksaanProperty()
    {
        return PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->with('petugas')
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }
    
    public function hapus($tgl_perawatan, $jam_rawat)
    {
        PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->where('tgl_perawatan', $tgl_perawatan)
            ->where('jam_rawat', $jam_rawat)
            ->delete();
            
        // Force refresh the pemeriksaan property
        unset($this->pemeriksaan);
            
        Notification::make()
            ->title('Pemeriksaan berhasil dihapus')
            ->success()
            ->send();
    }
    
    public function simpan()
    {
        $data = [
            'no_rawat' => $this->no_rawat,
            'tgl_perawatan' => $this->tgl_perawatan,
            'jam_rawat' => $this->jam_rawat,
            'suhu_tubuh' => $this->suhu_tubuh,
            'tensi' => $this->tensi,
            'nadi' => $this->nadi,
            'respirasi' => $this->respirasi,
            'tinggi' => $this->tinggi,
            'berat' => $this->berat,
            'spo2' => $this->spo2,
            'gcs' => $this->gcs,
            'kesadaran' => $this->kesadaran,
            'lingkar_perut' => $this->lingkar_perut,
            'keluhan' => $this->keluhan,
            'pemeriksaan' => $this->pemeriksaan,
            'alergi' => $this->alergi,
            'penilaian' => $this->penilaian,
            'rtl' => $this->rtl,
            'instruksi' => $this->instruksi,
            'evaluasi' => $this->evaluasi,
            'nip' => '-', // Default petugas entry
        ];
        
        PemeriksaanRalan::create($data);
        
        // Reset form
        $this->reset([
            'suhu_tubuh', 'tensi', 'nadi', 'respirasi', 'tinggi', 'berat', 'spo2',
            'gcs', 'kesadaran', 'lingkar_perut', 'keluhan', 'pemeriksaan', 'alergi',
            'penilaian', 'rtl', 'instruksi', 'evaluasi'
        ]);
        
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat = now()->format('H:i');
        
        // Force refresh the pemeriksaan property
        unset($this->pemeriksaan);
        
        Notification::make()
            ->title('Pemeriksaan berhasil disimpan')
            ->success()
            ->send();
    }
    
    public function render()
    {
        return view('livewire.pemeriksaan-ralan-form');
    }
}