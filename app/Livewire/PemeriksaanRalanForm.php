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

    #[Validate('required')]
    public $nip;

    // History data
    public $riwayatPemeriksaan = [];
    
    // Edit mode
    public $editingId = null;
    
    // Pagination
    public $currentPage = 1;
    public $perPage = 2;

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

        if ($this->editingId) {
            // Update existing record
            try {
                PemeriksaanRalan::where('no_rawat', $this->noRawat)
                    ->where('tgl_perawatan', $this->tgl_perawatan)
                    ->where('jam_rawat', $this->jam_rawat)
                    ->update($data);
                $message = 'Pemeriksaan SOAP berhasil diupdate';
            } catch (\Exception $e) {
                // If update fails, try to create new
                PemeriksaanRalan::create($data);
                $message = 'Pemeriksaan SOAP berhasil disimpan';
            }
        } else {
            // Create new record
            PemeriksaanRalan::create($data);
            $message = 'Pemeriksaan SOAP berhasil disimpan';
        }

        $this->resetForm();
        // Reset to first page after save to show latest data
        $this->currentPage = 1;
        $this->loadRiwayat();

        Notification::make()
            ->title($message)
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
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
    
    public function editPemeriksaan($tglPerawatan, $jamRawat): void
    {
        try {
            \Log::info('EditPemeriksaan called', [
                'noRawat' => $this->noRawat,
                'tglPerawatan' => $tglPerawatan,
                'jamRawat' => $jamRawat
            ]);
            
            $pemeriksaan = PemeriksaanRalan::where('no_rawat', $this->noRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->first();
                
            \Log::info('Pemeriksaan found', ['found' => $pemeriksaan ? 'yes' : 'no']);
                
            if ($pemeriksaan) {
                $rawAttrs = $pemeriksaan->getAttributes();
                $this->editingId = $rawAttrs['tgl_perawatan'] . '-' . $rawAttrs['jam_rawat'];
                $this->tgl_perawatan = $rawAttrs['tgl_perawatan'];
                $this->jam_rawat = substr($rawAttrs['jam_rawat'], 0, 5); // Format HH:MM
                $this->suhu_tubuh = $pemeriksaan->suhu_tubuh ?? '';
                $this->tensi = $pemeriksaan->tensi ?? '';
                $this->nadi = $pemeriksaan->nadi ?? '';
                $this->respirasi = $pemeriksaan->respirasi ?? '';
                $this->spo2 = $pemeriksaan->spo2 ?? '';
                $this->tinggi = $pemeriksaan->tinggi ?? '';
                $this->berat = $pemeriksaan->berat ?? '';
                $this->gcs = $pemeriksaan->gcs ?? '';
                $this->kesadaran = $pemeriksaan->kesadaran ?? '';
                $this->keluhan = $pemeriksaan->keluhan ?? '';
                $this->pemeriksaan = $pemeriksaan->pemeriksaan ?? '';
                $this->penilaian = $pemeriksaan->penilaian ?? '';
                $this->alergi = $pemeriksaan->alergi ?? '';
                $this->lingkar_perut = $pemeriksaan->lingkar_perut ?? '';
                $this->rtl = $pemeriksaan->rtl ?? '';
                $this->instruksi = $pemeriksaan->instruksi ?? '';
                $this->evaluasi = $pemeriksaan->evaluasi ?? '';
                
                \Log::info('Data loaded', ['keluhan' => $this->keluhan]);
                
                Notification::make()
                    ->title('Data berhasil dimuat untuk edit')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Data tidak ditemukan')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            \Log::error('Edit pemeriksaan error', ['error' => $e->getMessage()]);
            Notification::make()
                ->title('Error loading data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function loadRiwayat(): void
    {
        $totalQuery = PemeriksaanRalan::where('no_rawat', $this->noRawat);
        $this->totalRecords = $totalQuery->count();
        $this->totalPages = ceil($this->totalRecords / $this->perPage);
        
        // Ensure current page is valid
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
        
        $offset = ($this->currentPage - 1) * $this->perPage;
        
        $data = PemeriksaanRalan::where('no_rawat', $this->noRawat)
            ->with(['petugas:nik,nama'])
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->limit($this->perPage)
            ->offset($offset)
            ->get();
            
        $this->riwayatPemeriksaan = $data->map(function($item) {
            $array = $item->toArray();
            // Keep raw values for edit function - use raw database values
            $rawAttrs = $item->getAttributes();
            $array['tgl_perawatan_raw'] = $rawAttrs['tgl_perawatan'];
            $array['jam_rawat_raw'] = $rawAttrs['jam_rawat'];
            return $array;
        })->toArray();
    }
    
    public $totalRecords = 0;
    public $totalPages = 0;
    
    public function nextPage(): void
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadRiwayat();
        }
    }
    
    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadRiwayat();
        }
    }
    
    public function goToPage($page): void
    {
        if ($page >= 1 && $page <= $this->totalPages) {
            $this->currentPage = $page;
            $this->loadRiwayat();
        }
    }
    
    public function render()
    {
        return view('livewire.pemeriksaan-ralan-form');
    }
}