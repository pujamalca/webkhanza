<?php

namespace App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource\Pages;

use App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ValidasiTaskId extends Page
{
    protected static string $resource = ServiceJknErmResource::class;

    public function getTitle(): string
    {
        return 'Validasi Task ID 3-7';
    }

    public function getView(): string
    {
        return 'filament.clusters.administrator.resources.service-jkn-erm-resource.pages.validasi-task-id';
    }

    public $dariTanggal;
    public $sampaiTanggal;
    public $validationResults = [];
    public $summary = [
        'totalFixed' => 0,
        'totalOk' => 0,
        'totalProcessed' => 0,
    ];
    public $hasValidated = false;
    public $currentPage = 1;
    public $perPage = 3;

    public function mount(): void
    {
        $this->dariTanggal = now()->format('Y-m-d');
        $this->sampaiTanggal = now()->format('Y-m-d');
    }

    protected function getViewData(): array
    {
        $paginatedResults = $this->getPaginatedResults();

        return [
            'validationResults' => $paginatedResults['data'],
            'summary' => $this->summary,
            'hasValidated' => $this->hasValidated,
            'pagination' => $paginatedResults['pagination'],
        ];
    }

    public function getPaginatedValidationResultsProperty()
    {
        $paginatedResults = $this->getPaginatedResults();
        return $paginatedResults['data'];
    }

    public function getPaginationProperty()
    {
        $paginatedResults = $this->getPaginatedResults();
        return $paginatedResults['pagination'];
    }

    protected function getPaginatedResults(): array
    {
        $total = count($this->validationResults);
        $totalPages = ceil($total / $this->perPage);

        // Pastikan current page valid
        if ($this->currentPage > $totalPages && $totalPages > 0) {
            $this->currentPage = $totalPages;
        }
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }

        $offset = ($this->currentPage - 1) * $this->perPage;
        $data = array_slice($this->validationResults, $offset, $this->perPage);

        return [
            'data' => $data,
            'pagination' => [
                'currentPage' => $this->currentPage,
                'perPage' => $this->perPage,
                'total' => $total,
                'totalPages' => $totalPages,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $this->perPage, $total),
            ]
        ];
    }

    public function goToPage($page)
    {
        $this->currentPage = $page;
    }

    public function nextPage()
    {
        $totalPages = ceil(count($this->validationResults) / $this->perPage);
        if ($this->currentPage < $totalPages) {
            $this->currentPage++;
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function validateTaskIds()
    {
        if (empty($this->dariTanggal) || empty($this->sampaiTanggal)) {
            Notification::make()
                ->title('Tanggal harus diisi')
                ->danger()
                ->send();
            return;
        }

        $this->validationResults = [];
        $this->currentPage = 1; // Reset ke halaman pertama
        $totalFixed = 0;
        $totalOk = 0;

        // Query data registrasi
        $records = DB::table('reg_periksa as r')
            ->join('pasien as p', 'r.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->whereBetween('r.tgl_registrasi', [$this->dariTanggal, $this->sampaiTanggal])
            ->select([
                'r.no_rawat',
                'r.no_rkm_medis',
                'r.tgl_registrasi',
                'r.jam_reg',
                'p.nm_pasien'
            ])
            ->orderBy('r.tgl_registrasi')
            ->orderBy('r.jam_reg')
            ->get();

        foreach ($records as $record) {
            try {
                $tasks = [];
                $issues = [];
                $originalTasks = [];

                // Task 3: Waktu registrasi (wajib)
                $tasks[3] = Carbon::parse($record->tgl_registrasi . ' ' . $record->jam_reg);
                $originalTasks[3] = $tasks[3]->copy();

                // Task 4: Mulai pemeriksaan
                $pemeriksaan = DB::table('pemeriksaan_ralan')
                    ->where('no_rawat', $record->no_rawat)
                    ->selectRaw('CONCAT(tgl_perawatan, " ", jam_rawat) as waktu')
                    ->value('waktu');

                if (!$pemeriksaan || $pemeriksaan == '0000-00-00 00:00:00') {
                    $pemeriksaan = DB::table('mutasi_berkas')
                        ->where('no_rawat', $record->no_rawat)
                        ->where('diterima', '!=', '0000-00-00 00:00:00')
                        ->whereNotNull('diterima')
                        ->value('diterima');
                }

                if ($pemeriksaan && $pemeriksaan != '0000-00-00 00:00:00') {
                    $originalTasks[4] = Carbon::parse($pemeriksaan);
                    $tasks[4] = $originalTasks[4]->copy();
                    if ($tasks[4]->lt($tasks[3])) {
                        $issues[] = "Task 4 ({$originalTasks[4]->format('H:i')}) < Task 3 ({$tasks[3]->format('H:i')})";
                        $tasks[4] = $tasks[3]->copy()->addMinutes(5);
                    }
                } else {
                    $originalTasks[4] = null;
                    $tasks[4] = $tasks[3]->copy()->addMinutes(5);
                    $issues[] = "Task 4 kosong, otomatis +5 menit";
                }

                // Task 5: Selesai pemeriksaan
                $kembali = DB::table('mutasi_berkas')
                    ->where('no_rawat', $record->no_rawat)
                    ->where('kembali', '!=', '0000-00-00 00:00:00')
                    ->whereNotNull('kembali')
                    ->value('kembali');

                if ($kembali && $kembali != '0000-00-00 00:00:00') {
                    $originalTasks[5] = Carbon::parse($kembali);
                    $tasks[5] = $originalTasks[5]->copy();
                    if ($tasks[5]->lt($tasks[4])) {
                        $issues[] = "Task 5 ({$originalTasks[5]->format('H:i')}) < Task 4 ({$tasks[4]->format('H:i')})";
                        $tasks[5] = $tasks[4]->copy()->addMinutes(5);
                    }
                } else {
                    $originalTasks[5] = null;
                    $tasks[5] = $tasks[4]->copy()->addMinutes(5);
                    $issues[] = "Task 5 kosong, otomatis +5 menit";
                }

                // Task 6: Resep dibuat (opsional)
                $resep = DB::table('resep_obat')
                    ->where('no_rawat', $record->no_rawat)
                    ->where('tgl_perawatan', '!=', '0000-00-00')
                    ->whereNotNull('tgl_perawatan')
                    ->where('status', 'ralan')
                    ->selectRaw('CONCAT(tgl_perawatan, " ", jam) as waktu')
                    ->value('waktu');

                if ($resep && $resep != '0000-00-00 00:00:00') {
                    $originalTasks[6] = Carbon::parse($resep);
                    $tasks[6] = $originalTasks[6]->copy();
                    if ($tasks[6]->lt($tasks[5])) {
                        $issues[] = "Task 6 ({$originalTasks[6]->format('H:i')}) < Task 5 ({$tasks[5]->format('H:i')})";
                        $tasks[6] = $tasks[5]->copy()->addMinutes(5);
                    }

                    // Task 7: Obat diserahkan (opsional)
                    $penyerahan = DB::table('resep_obat')
                        ->where('no_rawat', $record->no_rawat)
                        ->where('status', 'ralan')
                        ->where('tgl_penyerahan', '!=', '0000-00-00')
                        ->whereNotNull('tgl_penyerahan')
                        ->selectRaw('CONCAT(tgl_penyerahan, " ", jam_penyerahan) as waktu')
                        ->value('waktu');

                    if ($penyerahan && $penyerahan != '0000-00-00 00:00:00') {
                        $originalTasks[7] = Carbon::parse($penyerahan);
                        $tasks[7] = $originalTasks[7]->copy();
                        if ($tasks[7]->lt($tasks[6])) {
                            $issues[] = "Task 7 ({$originalTasks[7]->format('H:i')}) < Task 6 ({$tasks[6]->format('H:i')})";
                            $tasks[7] = $tasks[6]->copy()->addMinutes(5);
                        }
                    }
                }

                // Simpan hasil validasi
                if (!empty($issues)) {
                    $this->validationResults[] = [
                        'no_rawat' => $record->no_rawat,
                        'no_rkm_medis' => $record->no_rkm_medis,
                        'pasien' => $record->nm_pasien,
                        'issues' => $issues,
                        'tasks' => $tasks,
                        'originalTasks' => $originalTasks,
                    ];
                    $totalFixed++;
                } else {
                    $totalOk++;
                }

            } catch (\Exception $e) {
                \Log::error("Error validating {$record->no_rawat}: {$e->getMessage()}");
            }
        }

        $this->summary = [
            'totalFixed' => $totalFixed,
            'totalOk' => $totalOk,
            'totalProcessed' => $records->count(),
        ];

        $this->hasValidated = true;

        if (empty($this->validationResults)) {
            Notification::make()
                ->title('Semua data sudah benar')
                ->body('Tidak ada data yang perlu diperbaiki!')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Validasi selesai')
                ->body("Ditemukan {$totalFixed} data yang perlu diperbaiki")
                ->warning()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
