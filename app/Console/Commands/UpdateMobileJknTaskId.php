<?php

namespace App\Console\Commands;

use App\Models\ReferensiMobilejknBpjsErm;
use App\Models\ReferensiMobilejknBpjsTaskid;
use App\Models\RegPeriksa;
use App\Models\PemeriksaanRalan;
use App\Models\ResepObat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMobileJknTaskId extends Command
{
    protected $signature = 'mobilejkn:update-taskid
                          {--dari-tanggal= : Tanggal awal (format: Y-m-d)}
                          {--sampai-tanggal= : Tanggal akhir (format: Y-m-d)}
                          {--all : Update semua data}';

    protected $description = 'Validasi dan perbaiki urutan waktu Task ID 3-7 (tidak insert ke tabel taskid, hanya validasi)';

    public function handle()
    {
        $this->info('🔄 Validasi dan perbaiki urutan waktu Task ID...');
        $this->warn('⚠️  Command ini HANYA validasi waktu, TIDAK insert ke tabel taskid');
        $this->info('📱 Desktop app yang akan insert ke referensi_mobilejkn_bpjs_taskid saat kirim ke BPJS');
        $this->newLine();

        $dariTanggal = $this->option('dari-tanggal') ?: now()->format('Y-m-d');
        $sampaiTanggal = $this->option('sampai-tanggal') ?: now()->format('Y-m-d');
        $updateAll = $this->option('all');

        try {
            // Ambil data dari reg_periksa yang ada di rentang tanggal
            $query = DB::table('reg_periksa as r')
                ->join('pasien as p', 'r.no_rkm_medis', '=', 'p.no_rkm_medis');

            if (!$updateAll) {
                $query->whereBetween('r.tgl_registrasi', [$dariTanggal, $sampaiTanggal]);
            }

            $records = $query->select(
                'r.no_rawat',
                'r.no_rkm_medis',
                'r.tgl_registrasi',
                'r.jam_reg',
                'r.stts',
                'p.nm_pasien'
            )
            ->orderBy('r.tgl_registrasi')
            ->orderBy('r.jam_reg')
            ->get();

            if ($records->isEmpty()) {
                if ($updateAll) {
                    $this->info('✅ Tidak ada data registrasi yang perlu diupdate');
                } else {
                    $this->info("✅ Tidak ada data registrasi untuk tanggal {$dariTanggal} s/d {$sampaiTanggal}");
                }
                return 0;
            }

            if ($updateAll) {
                $this->info("📋 Ditemukan {$records->count()} data registrasi yang akan diupdate");
            } else {
                $this->info("📋 Ditemukan {$records->count()} data registrasi untuk tanggal {$dariTanggal} s/d {$sampaiTanggal}");
            }

            $bar = $this->output->createProgressBar($records->count());
            $bar->start();

            $validationResults = [];
            $totalFixed = 0;
            $totalOk = 0;
            $errors = 0;

            foreach ($records as $record) {
                try {
                    $tasks = [];
                    $issues = [];

                    // Task 3: Waktu registrasi (wajib)
                    $tasks[3] = \Carbon\Carbon::parse($record->tgl_registrasi . ' ' . $record->jam_reg);

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
                        $tasks[4] = \Carbon\Carbon::parse($pemeriksaan);
                        if ($tasks[4]->lt($tasks[3])) {
                            $issues[] = "Task 4 ({$tasks[4]->format('H:i')}) < Task 3 ({$tasks[3]->format('H:i')})";
                            $tasks[4] = $tasks[3]->copy()->addMinutes(5);
                        }
                    } else {
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
                        $tasks[5] = \Carbon\Carbon::parse($kembali);
                        if ($tasks[5]->lt($tasks[4])) {
                            $issues[] = "Task 5 ({$tasks[5]->format('H:i')}) < Task 4 ({$tasks[4]->format('H:i')})";
                            $tasks[5] = $tasks[4]->copy()->addMinutes(5);
                        }
                    } else {
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
                        $tasks[6] = \Carbon\Carbon::parse($resep);
                        if ($tasks[6]->lt($tasks[5])) {
                            $issues[] = "Task 6 ({$tasks[6]->format('H:i')}) < Task 5 ({$tasks[5]->format('H:i')})";
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
                            $tasks[7] = \Carbon\Carbon::parse($penyerahan);
                            if ($tasks[7]->lt($tasks[6])) {
                                $issues[] = "Task 7 ({$tasks[7]->format('H:i')}) < Task 6 ({$tasks[6]->format('H:i')})";
                                $tasks[7] = $tasks[6]->copy()->addMinutes(5);
                            }
                        }
                    }

                    // Simpan hasil validasi
                    if (!empty($issues)) {
                        $taskInfo = "Task 3: " . $tasks[3]->format('H:i') .
                                   " → Task 4: " . $tasks[4]->format('H:i') .
                                   " → Task 5: " . $tasks[5]->format('H:i');
                        if (isset($tasks[6])) {
                            $taskInfo .= " → Task 6: " . $tasks[6]->format('H:i');
                        }
                        if (isset($tasks[7])) {
                            $taskInfo .= " → Task 7: " . $tasks[7]->format('H:i');
                        }

                        $validationResults[] = [
                            'no_rawat' => $record->no_rawat,
                            'pasien' => $record->nm_pasien,
                            'issues' => implode(', ', $issues),
                            'tasks' => $taskInfo,
                        ];
                        $totalFixed++;
                    } else {
                        $totalOk++;
                    }

                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("❌ Error update {$record->no_rawat}: {$e->getMessage()}");
                    $errors++;
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            // Summary
            $this->table(
                ['Status', 'Jumlah'],
                [
                    ['Data Perlu Diperbaiki', $totalFixed],
                    ['Data Sudah Benar', $totalOk],
                    ['Error', $errors],
                    ['Total Data Diproses', $records->count()],
                ]
            );

            // Detail data yang perlu diperbaiki
            if (!empty($validationResults)) {
                $this->newLine();
                $this->warn('📋 Data yang perlu diperbaiki:');
                $this->newLine();

                $tableData = [];
                foreach ($validationResults as $result) {
                    $tableData[] = [
                        $result['no_rawat'],
                        $result['pasien'],
                        $result['issues'],
                        $result['tasks'],
                    ];
                }

                $this->table(
                    ['No. Rawat', 'Pasien', 'Masalah', 'Waktu Task ID (Setelah Perbaikan)'],
                    $tableData
                );
            }

            $this->newLine();
            $this->info('✨ Validasi waktu Task ID selesai!');
            $this->warn('⚠️  Task 3-5 wajib ada (otomatis +5 menit jika kosong)');
            $this->warn('⚠️  Task 6-7 opsional (hanya jika pasien ambil obat)');
            $this->info('📱 Desktop app akan membaca waktu ini dan kirim ke BPJS');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            \Log::error('Update Mobile JKN Task ID Error: ' . $e->getMessage());
            return 1;
        }
    }

}
