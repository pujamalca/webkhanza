<?php

namespace App\Livewire;

use App\Models\CatatanPasien;
use App\Models\RegPeriksa;
use Filament\Notifications\Notification;
use Livewire\Component;

class CatatanPasienForm extends Component
{
    public string $noRawat;
    public $regPeriksa;

    // Form fields
    public string $catatan = '';

    // Existing catatan
    public $existingCatatan;

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;

        // Load reg periksa data
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Load existing catatan
        $this->loadExistingCatatan();
    }

    protected function loadExistingCatatan(): void
    {
        if ($this->regPeriksa) {
            $this->existingCatatan = CatatanPasien::where('no_rkm_medis', $this->regPeriksa->no_rkm_medis)
                ->with(['pasien'])
                ->first();

            // Pre-populate the form with existing catatan
            if ($this->existingCatatan) {
                $this->catatan = $this->existingCatatan->catatan;
            }
        } else {
            $this->existingCatatan = null;
        }
    }

    public function saveCatatan(): void
    {
        // Validation
        if (empty($this->catatan)) {
            Notification::make()
                ->title('Catatan Diperlukan')
                ->body('Silakan masukkan catatan untuk disimpan')
                ->warning()
                ->send();
            return;
        }

        if (!$this->regPeriksa) {
            Notification::make()
                ->title('Error')
                ->body('Data registrasi tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        try {
            // Save or update catatan for this patient
            CatatanPasien::updateOrCreate(
                ['no_rkm_medis' => $this->regPeriksa->no_rkm_medis],
                ['catatan' => $this->catatan]
            );

            // Clear form
            $this->catatan = '';

            // Reload existing catatan
            $this->loadExistingCatatan();

            Notification::make()
                ->title('Catatan Disimpan')
                ->body('Catatan pasien berhasil disimpan')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menyimpan catatan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteCatatan(): void
    {
        try {
            if (!$this->regPeriksa) {
                Notification::make()
                    ->title('Error')
                    ->body('Data registrasi tidak ditemukan')
                    ->danger()
                    ->send();
                return;
            }

            $catatanModel = CatatanPasien::where('no_rkm_medis', $this->regPeriksa->no_rkm_medis)->first();

            if ($catatanModel) {
                $catatanModel->delete();
                $this->loadExistingCatatan();

                Notification::make()
                    ->title('Catatan Dihapus')
                    ->body('Catatan berhasil dihapus')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Info')
                    ->body('Tidak ada catatan untuk dihapus')
                    ->info()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus catatan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.catatan-pasien-form');
    }
}