<?php

namespace App\Livewire;

use App\Models\JnsPerawatan;
use App\Models\RawatJlDr;
use App\Models\RawatJlPr;
use App\Models\RawatJlDrPr;
use App\Models\RegPeriksa;
use App\Models\Dokter;
use App\Models\Petugas;
use App\Models\Pegawai;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InputTindakanForm extends Component
{
    use WithPagination;

    public string $noRawat;
    public $regPeriksa;

    // Search
    public string $search = '';

    // Selected tindakan untuk ditambahkan
    public array $selectedTindakan = [];

    // Existing tindakan yang sudah diinput
    public $existingTindakanDr = [];
    public $existingTindakanPr = [];
    public $existingTindakanDrPr = [];

    // Form data for adding tindakan
    public string $selectedJenisTindakan = 'dr'; // 'dr', 'pr', 'drpr'
    public string $kdDokter = '';
    public string $nipPetugas = '';
    public string $tglPerawatan = '';
    public string $jamRawat = '';

    // Available options
    public $dokterList = [];
    public $petugasList = [];
    public $isAdmin = false;

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        $this->tglPerawatan = date('Y-m-d');
        $this->jamRawat = date('H:i:s');

        // Load reg periksa data
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Check user permissions
        $this->isAdmin = auth()->user()->hasRole(['super_admin', 'admin']) ||
                        auth()->user()->hasPermissionTo('manage_all_examinations') ||
                        auth()->user()->hasPermissionTo('manage_all_medical_notes') ||
                        auth()->user()->hasPermissionTo('manage_all_input_tindakan');

        // Load existing tindakan
        $this->loadExistingTindakan();

        // Load available dokter and petugas
        $this->loadDokterPetugas();

        // Set default values
        $this->setDefaults();
    }

    protected function setDefaults(): void
    {
        // Set default dokter from reg_periksa if available
        if ($this->regPeriksa && $this->regPeriksa->kd_dokter) {
            $this->kdDokter = $this->regPeriksa->kd_dokter;
        }

        // Set NIP based on role (following pemeriksaan ralan pattern)
        if ($this->isAdmin) {
            $this->nipPetugas = ''; // Admin can choose
        } else {
            // Non-admin uses their own NIP
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->pegawai) {
                $this->nipPetugas = $currentUser->pegawai->nik;
            } else {
                // Fallback for users without pegawai relation
                $this->nipPetugas = $currentUser->username ?? '-';
            }
        }
    }

    protected function loadDokterPetugas(): void
    {
        // Load active dokter
        $this->dokterList = Dokter::select('kd_dokter', 'nm_dokter')
            ->where('status', '1')
            ->orderBy('nm_dokter')
            ->get()
            ->mapWithKeys(function ($dokter) {
                return [$dokter->kd_dokter => $dokter->nm_dokter];
            })
            ->toArray();

        // Load petugas list for admin using pegawai
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

    protected function loadExistingTindakan(): void
    {
        // Load existing rawat_jl_dr
        $this->existingTindakanDr = RawatJlDr::byNoRawat($this->noRawat)
            ->with(['jenisPerawatan', 'dokter'])
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get()
            ->toArray();

        // Load existing rawat_jl_pr
        $this->existingTindakanPr = RawatJlPr::byNoRawat($this->noRawat)
            ->with(['jenisPerawatan', 'petugas'])
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get()
            ->toArray();

        // Load existing rawat_jl_drpr
        $this->existingTindakanDrPr = RawatJlDrPr::byNoRawat($this->noRawat)
            ->with(['jenisPerawatan', 'dokter', 'petugas'])
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get()
            ->toArray();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedJenisTindakan(): void
    {
        // Clear selected tindakan when type changes
        $this->selectedTindakan = [];
        $this->resetPage();
    }

    public function toggleTindakan(string $kdJenisPerawatan): void
    {
        if (in_array($kdJenisPerawatan, $this->selectedTindakan)) {
            $this->selectedTindakan = array_diff($this->selectedTindakan, [$kdJenisPerawatan]);
        } else {
            $this->selectedTindakan[] = $kdJenisPerawatan;
        }
    }

    public function resetSelectedTindakan(): void
    {
        $this->selectedTindakan = [];

        Notification::make()
            ->title('Selection Reset')
            ->body('Semua pilihan tindakan telah direset')
            ->info()
            ->send();
    }

    public function addSelectedTindakan(): void
    {
        if (empty($this->selectedTindakan)) {
            Notification::make()
                ->title('Pilih Tindakan')
                ->body('Silakan pilih minimal satu tindakan untuk ditambahkan')
                ->warning()
                ->send();
            return;
        }

        // Validate required fields based on jenis tindakan
        if ($this->selectedJenisTindakan === 'dr' && empty($this->kdDokter)) {
            Notification::make()
                ->title('Dokter Harus Dipilih')
                ->body('Pilih dokter untuk tindakan dokter')
                ->danger()
                ->send();
            return;
        }

        if ($this->selectedJenisTindakan === 'pr' && empty($this->nipPetugas)) {
            Notification::make()
                ->title('Petugas Harus Dipilih')
                ->body('Pilih petugas untuk tindakan petugas')
                ->danger()
                ->send();
            return;
        }

        if ($this->selectedJenisTindakan === 'drpr' && (empty($this->kdDokter) || empty($this->nipPetugas))) {
            Notification::make()
                ->title('Dokter dan Petugas Harus Dipilih')
                ->body('Pilih dokter dan petugas untuk tindakan kolaborasi')
                ->danger()
                ->send();
            return;
        }

        try {
            $successCount = 0;

            foreach ($this->selectedTindakan as $kdJenisPerawatan) {
                $jenisPerawatan = JnsPerawatan::find($kdJenisPerawatan);

                if (!$jenisPerawatan) {
                    continue;
                }

                $baseData = [
                    'no_rawat' => $this->noRawat,
                    'kd_jenis_prw' => $kdJenisPerawatan,
                    'tgl_perawatan' => $this->tglPerawatan,
                    'jam_rawat' => $this->jamRawat,
                    'material' => $jenisPerawatan->material ?? 0,
                    'bhp' => $jenisPerawatan->bhp ?? 0,
                    'kso' => $jenisPerawatan->kso ?? 0,
                    'menejemen' => $jenisPerawatan->menejemen ?? 0,
                    'stts_bayar' => 'Belum'
                ];

                switch ($this->selectedJenisTindakan) {
                    case 'dr':
                        $data = array_merge($baseData, [
                            'kd_dokter' => $this->kdDokter,
                            'tarif_tindakandr' => $jenisPerawatan->tarif_tindakandr ?? 0,
                            'biaya_rawat' => $jenisPerawatan->total_byrdr ?? 0
                        ]);

                        RawatJlDr::create($data);
                        break;

                    case 'pr':
                        $data = array_merge($baseData, [
                            'nip' => $this->nipPetugas,
                            'tarif_tindakanpr' => $jenisPerawatan->tarif_tindakanpr ?? 0,
                            'biaya_rawat' => $jenisPerawatan->total_byrpr ?? 0
                        ]);

                        RawatJlPr::create($data);
                        break;

                    case 'drpr':
                        $data = array_merge($baseData, [
                            'kd_dokter' => $this->kdDokter,
                            'nip' => $this->nipPetugas,
                            'tarif_tindakandr' => $jenisPerawatan->tarif_tindakandr ?? 0,
                            'tarif_tindakanpr' => $jenisPerawatan->tarif_tindakanpr ?? 0,
                            'biaya_rawat' => $jenisPerawatan->total_byrdrpr ?? 0
                        ]);

                        RawatJlDrPr::create($data);
                        break;
                }

                $successCount++;
            }

            // Clear selections
            $this->selectedTindakan = [];

            // Reload existing tindakan
            $this->loadExistingTindakan();

            Notification::make()
                ->title('Tindakan Berhasil Ditambahkan')
                ->body("Berhasil menambahkan {$successCount} tindakan")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menambahkan tindakan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteTindakan(string $type, array $keys): void
    {
        try {
            switch ($type) {
                case 'dr':
                    $tindakan = RawatJlDr::find($keys);
                    break;
                case 'pr':
                    $tindakan = RawatJlPr::find($keys);
                    break;
                case 'drpr':
                    $tindakan = RawatJlDrPr::find($keys);
                    break;
                default:
                    throw new \Exception('Tipe tindakan tidak valid');
            }

            if ($tindakan) {
                $tindakan->delete();
                $this->loadExistingTindakan();

                Notification::make()
                    ->title('Tindakan Dihapus')
                    ->body('Tindakan berhasil dihapus')
                    ->success()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus tindakan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetAllTindakan(): void
    {
        try {
            $deletedCount = 0;

            // Delete all rawat_jl_dr records for this no_rawat
            $deletedDr = RawatJlDr::byNoRawat($this->noRawat)->count();
            RawatJlDr::byNoRawat($this->noRawat)->delete();
            $deletedCount += $deletedDr;

            // Delete all rawat_jl_pr records for this no_rawat
            $deletedPr = RawatJlPr::byNoRawat($this->noRawat)->count();
            RawatJlPr::byNoRawat($this->noRawat)->delete();
            $deletedCount += $deletedPr;

            // Delete all rawat_jl_drpr records for this no_rawat
            $deletedDrPr = RawatJlDrPr::byNoRawat($this->noRawat)->count();
            RawatJlDrPr::byNoRawat($this->noRawat)->delete();
            $deletedCount += $deletedDrPr;

            // Reload existing tindakan to update the display
            $this->loadExistingTindakan();

            Notification::make()
                ->title('Semua Tindakan Dihapus')
                ->body("Berhasil menghapus {$deletedCount} tindakan dari rekam medis ini")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus semua tindakan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        // Get current user NIP for personalized ordering
        $currentUserNip = Auth::user()->pegawai->nik ?? Auth::user()->username ?? '-';

        // Get available jenis perawatan with search and pagination, filtered by type
        $jenisPerawatan = JnsPerawatan::active()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('jns_perawatan.kd_jenis_prw', 'like', "%{$this->search}%")
                      ->orWhere('jns_perawatan.nm_perawatan', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedJenisTindakan === 'dr', function ($query) {
                // Filter for dokter: show only if total_byrdr > 0
                $query->where('jns_perawatan.total_byrdr', '>', 0);
            })
            ->when($this->selectedJenisTindakan === 'pr', function ($query) {
                // Filter for petugas: show only if total_byrpr > 0
                $query->where('jns_perawatan.total_byrpr', '>', 0);
            })
            ->when($this->selectedJenisTindakan === 'drpr', function ($query) {
                // Filter for dokter+petugas: show only if total_byrdrpr > 0
                $query->where('jns_perawatan.total_byrdrpr', '>', 0);
            })
            // Add frequency ordering based on usage
            ->leftJoin(\DB::raw($this->buildUsageStatsQuery($currentUserNip)), 'jns_perawatan.kd_jenis_prw', '=', 'usage_stats.kd_jenis_prw')
            ->orderByRaw('COALESCE(usage_stats.user_usage, 0) DESC') // User's frequently used first
            ->orderByRaw('COALESCE(usage_stats.total_usage, 0) DESC') // Then general frequency
            ->orderBy('jns_perawatan.nm_perawatan') // Finally alphabetical
            ->select('jns_perawatan.*',
                    \DB::raw('COALESCE(usage_stats.total_usage, 0) as total_usage'),
                    \DB::raw('COALESCE(usage_stats.user_usage, 0) as user_usage'))
            ->simplePaginate(8); // Use simple pagination with 8 items per page

        return view('livewire.input-tindakan-form', [
            'jenisPerawatan' => $jenisPerawatan
        ]);
    }

    private function getTableByType(): string
    {
        return match($this->selectedJenisTindakan) {
            'dr' => 'rawat_jl_dr',
            'pr' => 'rawat_jl_pr',
            'drpr' => 'rawat_jl_drpr',
            default => 'rawat_jl_dr'
        };
    }

    private function buildUsageStatsQuery(string $currentUserNip): string
    {
        $table = $this->getTableByType();

        // Build user identification based on table type
        $userCondition = match($this->selectedJenisTindakan) {
            'dr' => "0", // Cannot identify user for dokter records easily
            'pr' => "CASE WHEN {$table}.nip = '{$currentUserNip}' THEN 1 ELSE 0 END",
            'drpr' => "CASE WHEN {$table}.nip = '{$currentUserNip}' THEN 1 ELSE 0 END",
            default => "0"
        };

        return "(
            SELECT
                kd_jenis_prw,
                COUNT(*) as total_usage,
                SUM({$userCondition}) as user_usage
            FROM {$table}
            GROUP BY kd_jenis_prw
            ORDER BY total_usage DESC
            LIMIT 500
        ) usage_stats";
    }
}