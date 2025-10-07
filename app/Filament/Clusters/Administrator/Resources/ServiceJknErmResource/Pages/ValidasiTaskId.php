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
    public $dataExported = false;

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
        $this->dataExported = false; // Reset flag export
        $totalFixed = 0;
        $totalOk = 0;

        // Query data registrasi rawat jalan dengan jadwal dokter
        $records = DB::table('reg_periksa as r')
            ->join('pasien as p', 'r.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('referensi_mobilejkn_bpjs as mjkn', 'r.no_rawat', '=', 'mjkn.no_rawat')
            ->leftJoin('jadwal as j', function($join) {
                $join->on('r.kd_dokter', '=', 'j.kd_dokter')
                     ->on('r.kd_poli', '=', 'j.kd_poli')
                     ->whereRaw('DAYNAME(r.tgl_registrasi) = CASE
                        WHEN j.hari_kerja = "SENIN" THEN "Monday"
                        WHEN j.hari_kerja = "SELASA" THEN "Tuesday"
                        WHEN j.hari_kerja = "RABU" THEN "Wednesday"
                        WHEN j.hari_kerja = "KAMIS" THEN "Thursday"
                        WHEN j.hari_kerja = "JUMAT" THEN "Friday"
                        WHEN j.hari_kerja = "SABTU" THEN "Saturday"
                        WHEN j.hari_kerja = "AKHAD" THEN "Sunday"
                     END');
            })
            ->whereBetween('r.tgl_registrasi', [$this->dariTanggal, $this->sampaiTanggal])
            ->where('r.status_lanjut', 'Ralan')
            ->select([
                'r.no_rawat',
                'r.no_rkm_medis',
                'r.tgl_registrasi',
                'r.jam_reg',
                'r.kd_dokter',
                'r.kd_poli',
                'p.nm_pasien',
                'j.jam_mulai as jam_praktek',
                'mjkn.validasi'
            ])
            ->orderBy('r.tgl_registrasi')
            ->orderBy('r.jam_reg')
            ->get();

        // Group records by dokter+poli+tanggal untuk sequential timing
        $groupedRecords = [];
        foreach ($records as $record) {
            $key = $record->kd_dokter . '|' . $record->kd_poli . '|' . $record->tgl_registrasi;
            if (!isset($groupedRecords[$key])) {
                $groupedRecords[$key] = [];
            }
            $groupedRecords[$key][] = $record;
        }

        foreach ($groupedRecords as $key => $groupRecords) {
            $sequenceNumber = 0;

            foreach ($groupRecords as $record) {
                try {
                    $tasks = [];
                    $issues = [];
                    $originalTasks = [];

                    // Task 3: Waktu tunggu poli (jam_reg atau jam_praktek) + sequence
                    $jamReg = Carbon::parse($record->tgl_registrasi . ' ' . $record->jam_reg);
                    $originalTasks[3] = $jamReg->copy();

                    // Jika ada jam praktek dokter dan jam_reg lebih awal, gunakan jam praktek
                    if (!empty($record->jam_praktek)) {
                        $jamPraktek = Carbon::parse($record->tgl_registrasi . ' ' . $record->jam_praktek);

                        if ($jamReg->lt($jamPraktek)) {
                            $tasks[3] = $jamPraktek->copy()->addSeconds($sequenceNumber * 7);
                            $issues[] = "Jam Reg ({$jamReg->format('H:i')}) < Jam Praktek ({$jamPraktek->format('H:i')}), diubah ke jam praktek";
                        } else {
                            $tasks[3] = $jamReg->copy()->addSeconds($sequenceNumber * 7);
                        }
                    } else {
                        // Tidak ada jadwal dokter, pakai jam_reg + sequence
                        $tasks[3] = $jamReg->copy()->addSeconds($sequenceNumber * 7);
                    }

                    $sequenceNumber++;

                // Task 3 Alternatif: Validasi SEP Mobile JKN (hanya untuk info)
                $tasks['3_sep'] = null;
                if (!empty($record->validasi) && $record->validasi != '0000-00-00 00:00:00') {
                    $tasks['3_sep'] = Carbon::parse($record->validasi);
                }

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
                    // Bandingkan dengan originalTasks[3] (waktu asli), bukan tasks[3] (yang sudah + sequence)
                    if ($originalTasks[4]->lte($originalTasks[3])) {
                        $issues[] = "Task 4 ({$originalTasks[4]->format('H:i:s')}) <= Task 3 ({$originalTasks[3]->format('H:i:s')})";
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
                    // Bandingkan dengan originalTasks[4] (waktu asli)
                    if (isset($originalTasks[4]) && $originalTasks[5]->lte($originalTasks[4])) {
                        $issues[] = "Task 5 ({$originalTasks[5]->format('H:i:s')}) <= Task 4 ({$originalTasks[4]->format('H:i:s')})";
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
                    // Bandingkan dengan originalTasks[5] (waktu asli)
                    if (isset($originalTasks[5]) && $originalTasks[6]->lte($originalTasks[5])) {
                        $issues[] = "Task 6 ({$originalTasks[6]->format('H:i:s')}) <= Task 5 ({$originalTasks[5]->format('H:i:s')})";
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
                        // Bandingkan dengan originalTasks[6] (waktu asli)
                        if (isset($originalTasks[6]) && $originalTasks[7]->lte($originalTasks[6])) {
                            $issues[] = "Task 7 ({$originalTasks[7]->format('H:i:s')}) <= Task 6 ({$originalTasks[6]->format('H:i:s')})";
                            $tasks[7] = $tasks[6]->copy()->addMinutes(5);
                        }
                    }
                }

                // Simpan hasil validasi - SEMUA data (yang bermasalah dan yang OK)
                $this->validationResults[] = [
                    'no_rawat' => $record->no_rawat,
                    'no_rkm_medis' => $record->no_rkm_medis,
                    'pasien' => $record->nm_pasien,
                    'issues' => $issues,
                    'tasks' => $tasks,
                    'originalTasks' => $originalTasks,
                    'has_issues' => !empty($issues), // Flag untuk warna berbeda
                ];

                if (!empty($issues)) {
                    $totalFixed++;
                } else {
                    $totalOk++;
                }

                } catch (\Exception $e) {
                    \Log::error("Error validating {$record->no_rawat}: {$e->getMessage()}");
                }
            }
        }

        // Sort: yang bermasalah di atas, yang OK di bawah
        usort($this->validationResults, function($a, $b) {
            return $b['has_issues'] <=> $a['has_issues'];
        });

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

    public function updateTaskIds()
    {
        if (empty($this->validationResults)) {
            Notification::make()
                ->title('Tidak ada data untuk diupdate')
                ->warning()
                ->send();
            return;
        }

        $updated = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($this->validationResults as $result) {
                // Skip jika tidak ada masalah
                if (!$result['has_issues']) {
                    continue;
                }

                $noRawat = $result['no_rawat'];
                $tasks = $result['tasks'];
                $originalTasks = $result['originalTasks'];

                // Update Task 3: jam_reg di reg_periksa (jika berubah)
                if (isset($tasks[3]) && isset($originalTasks[3])) {
                    if ($tasks[3]->ne($originalTasks[3])) {
                        // Jam reg berubah (karena disesuaikan dengan jam praktek)
                        // Gunakan unprepared statement untuk menghindari cache issue
                        $jamRegBaru = $tasks[3]->format('H:i:s');
                        $noRawatEscaped = DB::connection()->getPdo()->quote($noRawat);
                        $jamRegEscaped = DB::connection()->getPdo()->quote($jamRegBaru);
                        DB::unprepared("UPDATE reg_periksa SET jam_reg = {$jamRegEscaped} WHERE no_rawat = {$noRawatEscaped}");
                    }
                }

                // Update Task 4: pemeriksaan_ralan DAN mutasi_berkas.diterima
                if (isset($tasks[4])) {
                    if (isset($originalTasks[4])) {
                        // Ada data asli - cek di pemeriksaan_ralan
                        $oldRecord = DB::table('pemeriksaan_ralan')
                            ->where('no_rawat', $noRawat)
                            ->where('tgl_perawatan', $originalTasks[4]->format('Y-m-d'))
                            ->where('jam_rawat', $originalTasks[4]->format('H:i:s'))
                            ->first();

                        if ($oldRecord) {
                            // DELETE old dari pemeriksaan_ralan, INSERT new
                            DB::table('pemeriksaan_ralan')
                                ->where('no_rawat', $noRawat)
                                ->where('tgl_perawatan', $originalTasks[4]->format('Y-m-d'))
                                ->where('jam_rawat', $originalTasks[4]->format('H:i:s'))
                                ->delete();

                            $newRecord = (array) $oldRecord;
                            $newRecord['tgl_perawatan'] = $tasks[4]->format('Y-m-d');
                            $newRecord['jam_rawat'] = $tasks[4]->format('H:i:s');

                            DB::table('pemeriksaan_ralan')->insert($newRecord);
                        } else {
                            // Data asli dari mutasi_berkas.diterima, bukan pemeriksaan_ralan
                            // INSERT baru ke pemeriksaan_ralan dengan waktu yang sudah diperbaiki
                            DB::table('pemeriksaan_ralan')->insert([
                                'no_rawat' => $noRawat,
                                'tgl_perawatan' => $tasks[4]->format('Y-m-d'),
                                'jam_rawat' => $tasks[4]->format('H:i:s'),
                                'suhu_tubuh' => '36',
                                'tensi' => '120/80',
                                'nadi' => '80',
                                'respirasi' => '20',
                                'tinggi' => '0',
                                'berat' => '0',
                                'spo2' => '99',
                                'gcs' => '456',
                                'kesadaran' => 'Compos Mentis',
                                'keluhan' => '-',
                                'pemeriksaan' => '-',
                                'alergi' => '-',
                                'lingkar_perut' => '0',
                                'rtl' => '-',
                                'penilaian' => '-',
                                'instruksi' => '-',
                                'evaluasi' => '-',
                                'nip' => '-',
                            ]);
                        }

                        // Update juga mutasi_berkas.diterima jika ada
                        DB::table('mutasi_berkas')
                            ->where('no_rawat', $noRawat)
                            ->update(['diterima' => $tasks[4]->format('Y-m-d H:i:s')]);

                    } else {
                        // Data kosong (auto-generated) - INSERT baru
                        DB::table('pemeriksaan_ralan')->insert([
                            'no_rawat' => $noRawat,
                            'tgl_perawatan' => $tasks[4]->format('Y-m-d'),
                            'jam_rawat' => $tasks[4]->format('H:i:s'),
                            'suhu_tubuh' => '36',
                            'tensi' => '120/80',
                            'nadi' => '80',
                            'respirasi' => '20',
                            'tinggi' => '0',
                            'berat' => '0',
                            'spo2' => '99',
                            'gcs' => '456',
                            'kesadaran' => 'Compos Mentis',
                            'keluhan' => '-',
                            'pemeriksaan' => '-',
                            'alergi' => '-',
                            'lingkar_perut' => '0',
                            'rtl' => '-',
                            'penilaian' => '-',
                            'instruksi' => '-',
                            'evaluasi' => '-',
                            'nip' => '-',
                        ]);
                    }
                }

                // Update Task 5: mutasi_berkas (kembali) - UPDATE or INSERT
                if (isset($tasks[5])) {
                    $exists = DB::table('mutasi_berkas')
                        ->where('no_rawat', $noRawat)
                        ->exists();

                    if ($exists) {
                        DB::table('mutasi_berkas')
                            ->where('no_rawat', $noRawat)
                            ->update(['kembali' => $tasks[5]->format('Y-m-d H:i:s')]);
                    } else {
                        // INSERT baru jika tidak ada record mutasi_berkas
                        // 7 kolom: no_rawat, status, dikirim, diterima, kembali, tidakada, ranap
                        // Disable strict mode untuk insert, karena database asli punya '0000-00-00 00:00:00'
                        DB::statement("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
                        DB::statement(
                            "INSERT INTO mutasi_berkas VALUES (?, 'Sudah Kembali', NOW(), ?, ?, '0000-00-00 00:00:00', '0000-00-00 00:00:00')",
                            [$noRawat, $tasks[4]->format('Y-m-d H:i:s'), $tasks[5]->format('Y-m-d H:i:s')]
                        );
                        // Restore sql_mode
                        DB::statement("SET SESSION sql_mode = (SELECT @@GLOBAL.sql_mode)");
                    }
                }

                // Update Task 6: resep_obat - DELETE old, INSERT new OR INSERT if empty
                if (isset($tasks[6])) {
                    if (isset($originalTasks[6])) {
                        // Ada data asli - DELETE old, INSERT new
                        $oldResep = DB::table('resep_obat')
                            ->where('no_rawat', $noRawat)
                            ->where('status', 'ralan')
                            ->where('tgl_perawatan', $originalTasks[6]->format('Y-m-d'))
                            ->where('jam', $originalTasks[6]->format('H:i:s'))
                            ->first();

                        if ($oldResep) {
                            DB::table('resep_obat')
                                ->where('no_rawat', $noRawat)
                                ->where('status', 'ralan')
                                ->where('tgl_perawatan', $originalTasks[6]->format('Y-m-d'))
                                ->where('jam', $originalTasks[6]->format('H:i:s'))
                                ->delete();

                            $newResep = (array) $oldResep;
                            $newResep['tgl_perawatan'] = $tasks[6]->format('Y-m-d');
                            $newResep['jam'] = $tasks[6]->format('H:i:s');

                            // Disable strict mode untuk insert resep_obat (mungkin ada '0000-00-00')
                            DB::statement("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
                            DB::table('resep_obat')->insert($newResep);
                            DB::statement("SET SESSION sql_mode = (SELECT @@GLOBAL.sql_mode)");
                        }
                    } else {
                        // Data kosong (auto-generated) - INSERT baru dengan data minimal
                        DB::table('resep_obat')->insert([
                            'no_resep' => 'AUTO-' . time(),
                            'tgl_perawatan' => $tasks[6]->format('Y-m-d'),
                            'jam' => $tasks[6]->format('H:i:s'),
                            'no_rawat' => $noRawat,
                            'kd_dokter' => '-',
                            'tgl_peresepan' => $tasks[6]->format('Y-m-d'),
                            'jam_peresepan' => $tasks[6]->format('H:i:s'),
                            'status' => 'ralan',
                            'tgl_penyerahan' => isset($tasks[7]) ? $tasks[7]->format('Y-m-d') : $tasks[6]->format('Y-m-d'),
                            'jam_penyerahan' => isset($tasks[7]) ? $tasks[7]->format('H:i:s') : $tasks[6]->format('H:i:s'),
                        ]);
                    }
                }

                // Update Task 7: resep_obat penyerahan - bisa langsung update
                if (isset($tasks[7])) {
                    DB::table('resep_obat')
                        ->where('no_rawat', $noRawat)
                        ->where('status', 'ralan')
                        ->update([
                            'tgl_penyerahan' => $tasks[7]->format('Y-m-d'),
                            'jam_penyerahan' => $tasks[7]->format('H:i:s'),
                        ]);
                }

                $updated++;
            }

            DB::commit();

            Notification::make()
                ->title('Update berhasil!')
                ->body("Berhasil mengupdate {$updated} data ke database. Desktop app dapat mengambil data terbaru.")
                ->success()
                ->send();

            // Set flag bahwa data sudah di-update
            $this->dataExported = true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error updating task IDs: {$e->getMessage()}");

            Notification::make()
                ->title('Update gagal!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
