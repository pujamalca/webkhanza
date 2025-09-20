<?php

namespace App\Livewire;

use App\Models\CatatanPasien;
use App\Models\CatatanPerawatan;
use App\Models\RegPeriksa;
use App\Models\Petugas;
use App\Models\Pegawai;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class CatatanPasienForm extends Component
{
    use WithPagination;

    public string $noRawat;
    public $regPeriksa;

    // Form fields - Catatan Pasien
    public string $catatan = '';

    // Form fields - Catatan Medis
    public string $catatanMedis = '';
    public string $nip = '';
    public string $tanggal = '';
    public string $jam = '';

    // Existing data
    public $existingCatatan;
    public $existingCatatanMedis = [];

    // Available options
    public $petugasList = [];
    public $isAdmin = false;

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        $this->tanggal = date('Y-m-d');
        $this->jam = date('H:i:s');

        // Load reg periksa data
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Load existing data
        $this->loadExistingCatatan();
        $this->loadExistingCatatanMedis();

        // Check user permissions
        $this->isAdmin = auth()->user()->hasRole(['super_admin', 'admin']) ||
                        auth()->user()->hasPermissionTo('manage_all_examinations') ||
                        auth()->user()->hasPermissionTo('manage_all_medical_notes');

        // Load petugas list for admin
        if ($this->isAdmin) {
            $this->loadPetugasList();
        }

        // Set default petugas
        $this->setDefaultPetugas();
    }

    protected function setDefaultPetugas(): void
    {
        // Set NIP based on role (following pemeriksaan ralan pattern)
        if ($this->isAdmin) {
            $this->nip = ''; // Admin can choose
        } else {
            // Non-admin uses their own NIP
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->pegawai) {
                $this->nip = $currentUser->pegawai->nik;
            } else {
                // Fallback for users without pegawai relation
                $this->nip = $currentUser->username ?? '-';
            }
        }
    }

    protected function loadPetugasList(): void
    {
        if ($this->isAdmin) {
            $this->petugasList = Pegawai::select('nik', 'nama')
                ->orderBy('nama')
                ->get()
                ->mapWithKeys(function ($pegawai) {
                    return [$pegawai->nik => $pegawai->nama];
                })
                ->toArray();
        }
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

    protected function loadExistingCatatanMedis(): void
    {
        $this->existingCatatanMedis = CatatanPerawatan::byNoRawat($this->noRawat)
            ->with(['petugas', 'dokter'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get()
            ->map(function ($catatan) {
                return [
                    'tanggal' => $catatan->tanggal,
                    'jam' => $catatan->jam,
                    'no_rawat' => $catatan->no_rawat,
                    'nip' => $catatan->nip,
                    'catatan' => $catatan->catatan,
                    'petugas_name' => $catatan->getPetugasOrDokterNameAttribute(),
                    'formatted_tanggal' => $catatan->getFormattedTanggalAttribute(),
                    'formatted_waktu' => $catatan->getFormattedWaktuAttribute(),
                ];
            })
            ->toArray();
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

    public function saveCatatanMedis(): void
    {
        // Validation
        if (empty($this->catatanMedis)) {
            Notification::make()
                ->title('Catatan Medis Diperlukan')
                ->body('Silakan masukkan catatan medis untuk disimpan')
                ->warning()
                ->send();
            return;
        }

        if (empty($this->nip)) {
            Notification::make()
                ->title('Petugas Diperlukan')
                ->body('Silakan pilih petugas')
                ->warning()
                ->send();
            return;
        }

        try {
            $data = [
                'tanggal' => $this->tanggal,
                'jam' => $this->jam,
                'no_rawat' => $this->noRawat,
                'nip' => $this->nip,
                'catatan' => $this->catatanMedis,
            ];

            CatatanPerawatan::create($data);

            // Clear form
            $this->catatanMedis = '';
            $this->tanggal = date('Y-m-d');
            $this->jam = date('H:i:s');

            // Reload existing catatan medis
            $this->loadExistingCatatanMedis();

            Notification::make()
                ->title('Catatan Medis Disimpan')
                ->body('Catatan medis berhasil disimpan')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menyimpan catatan medis: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteCatatanMedis(int $index): void
    {
        try {
            $catatan = $this->existingCatatanMedis[$index] ?? null;

            if (!$catatan) {
                Notification::make()
                    ->title('Error')
                    ->body('Catatan medis tidak ditemukan')
                    ->danger()
                    ->send();
                return;
            }

            // Find and delete the catatan medis
            $catatanModel = CatatanPerawatan::where('tanggal', $catatan['tanggal'])
                ->where('jam', $catatan['jam'])
                ->where('no_rawat', $catatan['no_rawat'])
                ->where('nip', $catatan['nip'])
                ->first();

            if ($catatanModel) {
                $catatanModel->delete();
                $this->loadExistingCatatanMedis();

                Notification::make()
                    ->title('Catatan Medis Dihapus')
                    ->body('Catatan medis berhasil dihapus')
                    ->success()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus catatan medis: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetAllCatatanMedis(): void
    {
        try {
            $deletedCount = CatatanPerawatan::byNoRawat($this->noRawat)->count();
            CatatanPerawatan::byNoRawat($this->noRawat)->delete();

            // Reload existing catatan medis
            $this->loadExistingCatatanMedis();

            Notification::make()
                ->title('Semua Catatan Medis Dihapus')
                ->body("Berhasil menghapus {$deletedCount} catatan medis dari rekam medis ini")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus semua catatan medis: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.catatan-pasien-form');
    }
}