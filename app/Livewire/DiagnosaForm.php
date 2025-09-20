<?php

namespace App\Livewire;

use App\Models\Penyakit;
use App\Models\DiagnosaPasien;
use App\Models\Icd9;
use App\Models\ProsedurPasien;
use App\Models\RegPeriksa;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiagnosaForm extends Component
{
    use WithPagination;

    public string $noRawat;
    public $regPeriksa;

    // Search
    public string $search = '';
    public string $searchProsedur = '';

    // Selected diagnosa untuk ditambahkan
    public array $selectedDiagnosa = [];
    public array $selectedProsedur = [];

    // Existing diagnosa & prosedur yang sudah diinput
    public $existingDiagnosa = [];
    public $existingProsedur = [];

    // Form data for adding diagnosa
    public string $status = 'Ralan'; // 'Ralan' or 'Ranap'
    public int $prioritas = 1; // 1=Primer, 2=Sekunder, 3=Tersier
    public string $statusPenyakit = 'Baru'; // 'Baru' or 'Lama'

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;

        // Load reg periksa data
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Set default status based on reg_periksa status
        if ($this->regPeriksa && $this->regPeriksa->status_lanjut === 'Ranap') {
            $this->status = 'Ranap';
        }

        // Load existing diagnosa and prosedur
        $this->loadExistingDiagnosa();
        $this->loadExistingProsedur();
    }

    protected function loadExistingDiagnosa(): void
    {
        $this->existingDiagnosa = DiagnosaPasien::byNoRawat($this->noRawat)
            ->with(['penyakit'])
            ->orderBy('prioritas', 'asc')
            ->get()
            ->toArray();
    }

    protected function loadExistingProsedur(): void
    {
        $this->existingProsedur = ProsedurPasien::byNoRawat($this->noRawat)
            ->with(['icd9'])
            ->orderBy('prioritas', 'asc')
            ->get()
            ->toArray();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSearchProsedur(): void
    {
        $this->resetPage();
    }

    public function toggleDiagnosa(string $kdPenyakit): void
    {
        if (in_array($kdPenyakit, $this->selectedDiagnosa)) {
            $this->selectedDiagnosa = array_diff($this->selectedDiagnosa, [$kdPenyakit]);
        } else {
            $this->selectedDiagnosa[] = $kdPenyakit;
        }
    }

    public function toggleProsedur(string $kode): void
    {
        if (in_array($kode, $this->selectedProsedur)) {
            $this->selectedProsedur = array_diff($this->selectedProsedur, [$kode]);
        } else {
            $this->selectedProsedur[] = $kode;
        }
    }

    public function resetSelectedDiagnosa(): void
    {
        $this->selectedDiagnosa = [];

        Notification::make()
            ->title('Selection Reset')
            ->body('Semua pilihan diagnosa telah direset')
            ->info()
            ->send();
    }

    public function resetSelectedProsedur(): void
    {
        $this->selectedProsedur = [];

        Notification::make()
            ->title('Selection Reset')
            ->body('Semua pilihan prosedur telah direset')
            ->info()
            ->send();
    }

    public function addSelectedDiagnosa(): void
    {
        if (empty($this->selectedDiagnosa)) {
            Notification::make()
                ->title('Pilih Diagnosa')
                ->body('Silakan pilih minimal satu diagnosa untuk ditambahkan')
                ->warning()
                ->send();
            return;
        }

        try {
            $successCount = 0;
            $currentPrioritas = 1; // Start with primer

            foreach ($this->selectedDiagnosa as $kdPenyakit) {
                // Check if diagnosa already exists
                $existing = DiagnosaPasien::where('no_rawat', $this->noRawat)
                    ->where('kd_penyakit', $kdPenyakit)
                    ->first();

                if ($existing) {
                    continue; // Skip if already exists
                }

                // Smart status detection: check if patient has had this disease before
                $hasHistoryOfDisease = DiagnosaPasien::where('kd_penyakit', $kdPenyakit)
                    ->whereHas('regPeriksa', function ($query) {
                        $query->where('no_rkm_medis', $this->regPeriksa->no_rkm_medis);
                    })
                    ->exists();

                $statusPenyakit = $hasHistoryOfDisease ? 'Lama' : 'Baru';

                $data = [
                    'no_rawat' => $this->noRawat,
                    'kd_penyakit' => $kdPenyakit,
                    'status' => 'Ralan', // Default to Ralan as requested
                    'prioritas' => $currentPrioritas, // Auto prioritas: 1=Primer, 2=Sekunder, 3=Tersier
                    'status_penyakit' => $statusPenyakit,
                ];

                DiagnosaPasien::create($data);
                $successCount++;

                // Increment prioritas for next selection (max 3)
                if ($currentPrioritas < 3) {
                    $currentPrioritas++;
                }
            }

            // Clear selections
            $this->selectedDiagnosa = [];

            // Reload existing diagnosa
            $this->loadExistingDiagnosa();

            Notification::make()
                ->title('Diagnosa Berhasil Ditambahkan')
                ->body("Berhasil menambahkan {$successCount} diagnosa")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menambahkan diagnosa: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function addSelectedProsedur(): void
    {
        if (empty($this->selectedProsedur)) {
            Notification::make()
                ->title('Pilih Prosedur')
                ->body('Silakan pilih minimal satu prosedur untuk ditambahkan')
                ->warning()
                ->send();
            return;
        }

        try {
            $successCount = 0;
            $currentPrioritas = 1; // Start with primer

            foreach ($this->selectedProsedur as $kode) {
                // Check if prosedur already exists
                $existing = ProsedurPasien::where('no_rawat', $this->noRawat)
                    ->where('kode', $kode)
                    ->first();

                if ($existing) {
                    continue; // Skip if already exists
                }

                $data = [
                    'no_rawat' => $this->noRawat,
                    'kode' => $kode,
                    'status' => 'Ralan', // Default to Ralan as requested
                    'prioritas' => $currentPrioritas, // Auto prioritas: 1=Primer, 2=Sekunder, 3=Tersier
                ];

                ProsedurPasien::create($data);
                $successCount++;

                // Increment prioritas for next selection (max 3)
                if ($currentPrioritas < 3) {
                    $currentPrioritas++;
                }
            }

            // Clear selections
            $this->selectedProsedur = [];

            // Reload existing prosedur
            $this->loadExistingProsedur();

            Notification::make()
                ->title('Prosedur Berhasil Ditambahkan')
                ->body("Berhasil menambahkan {$successCount} prosedur")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menambahkan prosedur: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteDiagnosa(string $kdPenyakit): void
    {
        try {
            $diagnosa = DiagnosaPasien::where('no_rawat', $this->noRawat)
                ->where('kd_penyakit', $kdPenyakit)
                ->first();

            if ($diagnosa) {
                $diagnosa->delete();
                $this->loadExistingDiagnosa();

                Notification::make()
                    ->title('Diagnosa Dihapus')
                    ->body('Diagnosa berhasil dihapus')
                    ->success()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus diagnosa: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteProsedur(string $kode): void
    {
        try {
            $prosedur = ProsedurPasien::where('no_rawat', $this->noRawat)
                ->where('kode', $kode)
                ->first();

            if ($prosedur) {
                $prosedur->delete();
                $this->loadExistingProsedur();

                Notification::make()
                    ->title('Prosedur Dihapus')
                    ->body('Prosedur berhasil dihapus')
                    ->success()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus prosedur: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetAllDiagnosa(): void
    {
        try {
            $deletedCount = DiagnosaPasien::byNoRawat($this->noRawat)->count();
            DiagnosaPasien::byNoRawat($this->noRawat)->delete();

            // Reload existing diagnosa
            $this->loadExistingDiagnosa();

            Notification::make()
                ->title('Semua Diagnosa Dihapus')
                ->body("Berhasil menghapus {$deletedCount} diagnosa dari rekam medis ini")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus semua diagnosa: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetAllProsedur(): void
    {
        try {
            $deletedCount = ProsedurPasien::byNoRawat($this->noRawat)->count();
            ProsedurPasien::byNoRawat($this->noRawat)->delete();

            // Reload existing prosedur
            $this->loadExistingProsedur();

            Notification::make()
                ->title('Semua Prosedur Dihapus')
                ->body("Berhasil menghapus {$deletedCount} prosedur dari rekam medis ini")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus semua prosedur: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        // Get available penyakit with optimized frequency-based ordering
        $penyakitList = Penyakit::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('penyakit.kd_penyakit', 'like', "%{$this->search}%")
                      ->orWhere('penyakit.nm_penyakit', 'like', "%{$this->search}%")
                      ->orWhere('penyakit.ciri_ciri', 'like', "%{$this->search}%");
                });
            })
            ->where('penyakit.kd_penyakit', '!=', '')
            ->where('penyakit.kd_penyakit', '!=', '-')
            ->whereNotNull('penyakit.kd_penyakit')
            ->leftJoin(\DB::raw('(
                SELECT
                    kd_penyakit,
                    COUNT(CASE WHEN reg_periksa.no_rkm_medis = "' . ($this->regPeriksa->no_rkm_medis ?? '') . '" THEN 1 END) as user_usage_count,
                    COUNT(*) as total_usage_count
                FROM diagnosa_pasien
                LEFT JOIN reg_periksa ON diagnosa_pasien.no_rawat = reg_periksa.no_rawat
                GROUP BY kd_penyakit
                ORDER BY total_usage_count DESC
                LIMIT 500
            ) as usage_stats'), 'penyakit.kd_penyakit', '=', 'usage_stats.kd_penyakit')
            ->select('penyakit.*',
                \DB::raw('COALESCE(usage_stats.user_usage_count, 0) as user_usage_count'),
                \DB::raw('COALESCE(usage_stats.total_usage_count, 0) as total_usage_count'))
            ->orderByRaw('(COALESCE(usage_stats.user_usage_count, 0) + COALESCE(usage_stats.total_usage_count, 0) * 0.1) DESC, COALESCE(usage_stats.user_usage_count, 0) DESC, COALESCE(usage_stats.total_usage_count, 0) DESC, penyakit.nm_penyakit ASC')
            ->simplePaginate(12);

        // Get available prosedur with optimized frequency-based ordering
        $prosedurList = Icd9::query()
            ->when($this->searchProsedur, function ($query) {
                $query->where(function ($q) {
                    $q->where('icd9.kode', 'like', "%{$this->searchProsedur}%")
                      ->orWhere('icd9.deskripsi_pendek', 'like', "%{$this->searchProsedur}%")
                      ->orWhere('icd9.deskripsi_panjang', 'like', "%{$this->searchProsedur}%");
                });
            })
            ->where('icd9.kode', '!=', '')
            ->where('icd9.kode', '!=', '-')
            ->whereNotNull('icd9.kode')
            ->leftJoin(\DB::raw('(
                SELECT
                    kode,
                    COUNT(CASE WHEN reg_periksa.no_rkm_medis = "' . ($this->regPeriksa->no_rkm_medis ?? '') . '" THEN 1 END) as user_usage_count,
                    COUNT(*) as total_usage_count
                FROM prosedur_pasien
                LEFT JOIN reg_periksa ON prosedur_pasien.no_rawat = reg_periksa.no_rawat
                GROUP BY kode
                ORDER BY total_usage_count DESC
                LIMIT 500
            ) as usage_stats'), 'icd9.kode', '=', 'usage_stats.kode')
            ->select('icd9.*',
                \DB::raw('COALESCE(usage_stats.user_usage_count, 0) as user_usage_count'),
                \DB::raw('COALESCE(usage_stats.total_usage_count, 0) as total_usage_count'))
            ->orderByRaw('(COALESCE(usage_stats.user_usage_count, 0) + COALESCE(usage_stats.total_usage_count, 0) * 0.1) DESC, COALESCE(usage_stats.user_usage_count, 0) DESC, COALESCE(usage_stats.total_usage_count, 0) DESC, icd9.deskripsi_pendek ASC')
            ->simplePaginate(12);

        return view('livewire.diagnosa-form', [
            'penyakitList' => $penyakitList,
            'prosedurList' => $prosedurList
        ]);
    }
}
