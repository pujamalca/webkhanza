<?php

namespace App\Livewire;

use App\Models\ResepObat;
use App\Models\ResepDokter;
use App\Models\Databarang;
use App\Models\RegPeriksa;
use App\Models\Dokter;
use App\Models\JenisBarang;
use App\Models\IndustriFarmasi;
use App\Models\KategoriBarang;
use App\Models\GudangBarang;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ResepObatForm extends Component
{
    use WithPagination;

    public string $noRawat;
    public $regPeriksa;

    // Form fields
    public string $noResep = '';
    public bool $editNoResep = false;
    public string $kdDokter = '';
    public string $tglPeresepan = '';
    public string $jamPeresepan = '';
    public string $status = 'ralan';

    // Detail resep fields
    public string $searchObat = '';
    public $obatTable = []; // Array untuk menyimpan obat yang akan disimpan batch

    // Available options
    public $dokterList = [];
    public $obatList = [];

    // Existing data
    public $existingResep = [];
    public $currentResep = null;
    public $detailResep = [];
    public $isEditing = false;

    // Search and pagination
    public $searchResults = [];
    public $showSearchResults = false;

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        $this->resetFormToDefault();

        // Load reg periksa data
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Load existing resep
        $this->loadExistingResep();

        // Load dokter list
        $this->loadDokterList();

        // Set default dokter
        $this->setDefaultDokter();

        // Generate default no resep
        $this->noResep = ResepObat::generateNoResep();
    }

    protected function loadDokterList(): void
    {
        $this->dokterList = Dokter::select('kd_dokter', 'nm_dokter')
            ->where('status', '1')
            ->orderBy('nm_dokter')
            ->get()
            ->mapWithKeys(function ($dokter) {
                return [$dokter->kd_dokter => $dokter->nm_dokter];
            })
            ->toArray();
    }

    protected function setDefaultDokter(): void
    {
        // Set default dokter from reg_periksa if available
        if ($this->regPeriksa && $this->regPeriksa->kd_dokter) {
            $this->kdDokter = $this->regPeriksa->kd_dokter;
        }
    }

    protected function loadExistingResep(): void
    {
        $this->existingResep = ResepObat::byNoRawat($this->noRawat)
            ->with(['dokter', 'resepDokter.databarang.satuanKecil', 'resepDokter.databarang.jenisBarang', 'resepDokter.databarang.industriFarmasi'])
            ->orderBy('tgl_peresepan', 'desc')
            ->orderBy('jam_peresepan', 'desc')
            ->get()
            ->map(function($resep) {
                $data = $resep->toArray();
                $data['formatted_tgl_peresepan'] = $resep->formatted_tgl_peresepan;
                return $data;
            })
            ->toArray();
    }

    public function searchObat(): void
    {
        if (strlen($this->searchObat) >= 2) {
            $this->searchResults = Databarang::obat()
                ->with(['satuanKecil', 'jenisBarang', 'industriFarmasi', 'kategoriBarang'])
                ->where(function($query) {
                    $query->searchByName($this->searchObat)
                          ->orWhere('kode_brng', 'like', '%' . $this->searchObat . '%');
                })
                ->orderBy('nama_brng')
                ->limit(20)
                ->get()
                ->map(function($obat) {
                    return [
                        'kode_brng' => $obat->kode_brng,
                        'nama_brng' => $obat->nama_brng,
                        'satuan' => $obat->satuanKecil->satuan ?? '-',
                        'komposisi' => $obat->komposisi,
                        'ralan' => $obat->ralan,
                        'formatted_harga' => $obat->formatted_harga_ralan,
                        'jenis' => $obat->jenisBarang->nama ?? '-',
                        'industri' => $obat->industriFarmasi->nama_industri ?? '-',
                        'stok' => $obat->total_stok,
                        'formatted_stok' => $obat->formatted_total_stok,
                        'display_name' => $obat->display_name
                    ];
                })
                ->toArray();

            $this->showSearchResults = true;
        } else {
            $this->searchResults = [];
            $this->showSearchResults = false;
        }
    }

    public function addObatToTable($kodeObat): void
    {
        // Check if obat already in table
        $exists = false;
        foreach($this->obatTable as $index => $item) {
            if($item['kode_brng'] === $kodeObat) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $obat = Databarang::with(['satuanKecil', 'jenisBarang', 'industriFarmasi', 'kategoriBarang'])
                ->find($kodeObat);

            if ($obat) {
                // Check stock availability
                $totalStok = $obat->total_stok;
                if ($totalStok <= 0) {
                    Notification::make()
                        ->title('Stok Tidak Tersedia')
                        ->body('Obat ' . $obat->nama_brng . ' tidak memiliki stok')
                        ->warning()
                        ->send();
                    return;
                }

                $this->obatTable[] = [
                    'kode_brng' => $obat->kode_brng,
                    'nama_brng' => $obat->nama_brng,
                    'satuan' => $obat->satuanKecil->satuan ?? '-',
                    'komposisi' => $obat->komposisi,
                    'harga' => $obat->ralan,
                    'formatted_harga' => $obat->formatted_harga_ralan,
                    'jenis' => $obat->jenisBarang->nama ?? '-',
                    'industri' => $obat->industriFarmasi->nama_industri ?? '-',
                    'stok' => $totalStok,
                    'formatted_stok' => $obat->formatted_total_stok,
                    'jumlah' => 1,
                    'aturan_pakai' => '',
                    'subtotal' => $obat->ralan
                ];
            }
        }

        $this->searchObat = '';
        $this->searchResults = [];
        $this->showSearchResults = false;
    }

    public function resetFormToDefault(): void
    {
        $this->tglPeresepan = date('Y-m-d');
        $this->jamPeresepan = date('H:i:s');
        $this->obatTable = [];
        $this->isEditing = false;
        $this->editNoResep = false;
        $this->currentResep = null;
        $this->detailResep = [];
    }

    public function toggleEditNoResep(): void
    {
        $this->editNoResep = !$this->editNoResep;
        if (!$this->editNoResep && !ResepObat::validateNoResep($this->noResep)) {
            $this->noResep = ResepObat::generateNoResep();
            Notification::make()
                ->title('Format No. Resep Salah')
                ->body('Format no. resep harus YYYYMMDD0001')
                ->warning()
                ->send();
        }
    }

    public function removeObatFromTable($index): void
    {
        unset($this->obatTable[$index]);
        $this->obatTable = array_values($this->obatTable); // Re-index array
    }

    public function updatedObatTable(): void
    {
        // Update subtotal when jumlah changes
        foreach($this->obatTable as $index => $obat) {
            $this->obatTable[$index]['subtotal'] = $obat['harga'] * $obat['jumlah'];
        }
    }

    public function getTotalHargaResep(): float
    {
        $total = 0;
        foreach($this->obatTable as $obat) {
            $total += $obat['subtotal'];
        }
        return $total;
    }

    public function simpanResep(): void
    {
        // Validation
        if (empty($this->kdDokter)) {
            Notification::make()
                ->title('Dokter Diperlukan')
                ->body('Silakan pilih dokter untuk resep')
                ->warning()
                ->send();
            return;
        }

        if (empty($this->obatTable)) {
            Notification::make()
                ->title('Obat Diperlukan')
                ->body('Silakan tambahkan minimal satu obat ke resep')
                ->warning()
                ->send();
            return;
        }

        // Validate all obat have jumlah and aturan_pakai
        foreach($this->obatTable as $index => $obat) {
            if (empty($obat['jumlah']) || $obat['jumlah'] <= 0) {
                Notification::make()
                    ->title('Jumlah Obat Diperlukan')
                    ->body('Silakan masukkan jumlah untuk ' . $obat['nama_brng'])
                    ->warning()
                    ->send();
                return;
            }
            if (empty($obat['aturan_pakai'])) {
                Notification::make()
                    ->title('Aturan Pakai Diperlukan')
                    ->body('Silakan masukkan aturan pakai untuk ' . $obat['nama_brng'])
                    ->warning()
                    ->send();
                return;
            }
        }

        if (!ResepObat::validateNoResep($this->noResep)) {
            Notification::make()
                ->title('Format No. Resep Salah')
                ->body('Format no. resep harus YYYYMMDD0001')
                ->warning()
                ->send();
            return;
        }

        try {
            // Check if resep already exists (for update)
            $existingResep = ResepObat::find($this->noResep);

            // Set SQL mode to allow invalid dates (matching desktop app)
            \DB::statement("SET SESSION sql_mode = 'ALLOW_INVALID_DATES'");

            // Create or update resep obat - using raw SQL to match desktop app format
            \DB::statement("INSERT INTO resep_obat
                (no_resep, tgl_perawatan, jam, no_rawat, kd_dokter, tgl_peresepan, jam_peresepan, status, tgl_penyerahan, jam_penyerahan)
                VALUES (?, '0000-00-00', '00:00:00', ?, ?, ?, ?, ?, '0000-00-00', '00:00:00')
                ON DUPLICATE KEY UPDATE
                kd_dokter = VALUES(kd_dokter),
                tgl_peresepan = VALUES(tgl_peresepan),
                jam_peresepan = VALUES(jam_peresepan),
                status = VALUES(status)",
                [$this->noResep, $this->noRawat, $this->kdDokter, $this->tglPeresepan, $this->jamPeresepan, $this->status]);

            $resep = ResepObat::find($this->noResep);

            // Delete existing detail if updating
            if ($existingResep) {
                ResepDokter::where('no_resep', $this->noResep)->delete();
                $message = 'Resep berhasil diupdate';
            } else {
                $message = 'Resep berhasil dibuat';
            }

            // Save detail resep
            foreach($this->obatTable as $obat) {
                ResepDokter::create([
                    'no_resep' => $this->noResep,
                    'kode_brng' => $obat['kode_brng'],
                    'jml' => $obat['jumlah'],
                    'aturan_pakai' => $obat['aturan_pakai']
                ]);
            }

            $this->currentResep = $resep;

            // Reset form
            $this->resetFormToDefault();
            $this->noResep = ResepObat::generateNoResep();
            $this->setDefaultDokter();

            // Reload existing resep
            $this->loadExistingResep();

            Notification::make()
                ->title($message)
                ->body('No. Resep: ' . $resep->no_resep . ' | Total: Rp ' . number_format($this->getTotalHargaResep(), 0, ',', '.'))
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menyimpan resep: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function buatResepBaru(): void
    {
        $this->resetFormToDefault();
        $this->noResep = ResepObat::generateNoResep();
        $this->setDefaultDokter();
        $this->isEditing = false;

        Notification::make()
            ->title('Form Resep Baru')
            ->body('Silakan lengkapi data resep dan tambahkan obat')
            ->success()
            ->send();
    }

    public function editResep($noResep): void
    {
        $this->currentResep = ResepObat::with(['resepDokter.databarang.satuanKecil', 'resepDokter.databarang.jenisBarang', 'resepDokter.databarang.industriFarmasi'])
            ->find($noResep);

        if ($this->currentResep) {
            $this->isEditing = true;
            $this->noResep = $this->currentResep->no_resep;
            $this->kdDokter = $this->currentResep->kd_dokter;
            $this->tglPeresepan = $this->currentResep->tgl_peresepan ?
                $this->currentResep->tgl_peresepan->format('Y-m-d') : date('Y-m-d');
            $this->jamPeresepan = $this->currentResep->jam_peresepan ?: date('H:i:s');
            $this->status = $this->currentResep->status;

            // Load obat to table
            $this->obatTable = [];
            foreach($this->currentResep->resepDokter as $detail) {
                $obat = $detail->databarang;
                $this->obatTable[] = [
                    'kode_brng' => $obat->kode_brng,
                    'nama_brng' => $obat->nama_brng,
                    'satuan' => $obat->satuanKecil->satuan ?? '-',
                    'komposisi' => $obat->komposisi,
                    'harga' => $obat->ralan,
                    'formatted_harga' => $obat->formatted_harga_ralan,
                    'jenis' => $obat->jenisBarang->nama ?? '-',
                    'industri' => $obat->industriFarmasi->nama_industri ?? '-',
                    'stok' => $obat->total_stok,
                    'formatted_stok' => $obat->formatted_total_stok,
                    'jumlah' => $detail->jml,
                    'aturan_pakai' => $detail->aturan_pakai,
                    'subtotal' => $obat->ralan * $detail->jml
                ];
            }
        }
    }

    public function lihatDetailResep($noResep): void
    {
        $this->detailResep = ResepDokter::byNoResep($noResep)
            ->with(['databarang.satuanKecil', 'databarang.jenisBarang', 'databarang.industriFarmasi'])
            ->get()
            ->map(function($detail) {
                $obat = $detail->databarang;
                return [
                    'kode_brng' => $obat->kode_brng,
                    'nama_brng' => $obat->nama_brng,
                    'satuan' => $obat->satuanKecil->satuan ?? '-',
                    'komposisi' => $obat->komposisi,
                    'harga' => $obat->ralan,
                    'formatted_harga' => $obat->formatted_harga_ralan,
                    'jenis' => $obat->jenisBarang->nama ?? '-',
                    'industri' => $obat->industriFarmasi->nama_industri ?? '-',
                    'stok' => $obat->total_stok,
                    'formatted_stok' => $obat->formatted_total_stok,
                    'jumlah' => $detail->jml,
                    'aturan_pakai' => $detail->aturan_pakai,
                    'subtotal' => $obat->ralan * $detail->jml
                ];
            })
            ->toArray();
    }

    public function batalEdit(): void
    {
        $this->resetFormToDefault();
        $this->noResep = ResepObat::generateNoResep();
        $this->setDefaultDokter();
        $this->detailResep = [];
    }

    public function hapusResep($noResep): void
    {
        try {
            // Delete detail resep first
            ResepDokter::where('no_resep', $noResep)->delete();

            // Delete resep obat
            ResepObat::where('no_resep', $noResep)->delete();

            // Reset form if deleted resep is currently selected
            if ($this->noResep === $noResep) {
                $this->resetFormToDefault();
            }

            // Reload existing resep
            $this->loadExistingResep();

            Notification::make()
                ->title('Resep Dihapus')
                ->body('Resep berhasil dihapus')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menghapus resep: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function duplicateResep($noResep): void
    {
        $originalResep = ResepObat::with(['resepDokter.databarang.satuanKecil', 'resepDokter.databarang.jenisBarang', 'resepDokter.databarang.industriFarmasi'])
            ->find($noResep);

        if ($originalResep) {
            $this->resetFormToDefault();
            $this->noResep = ResepObat::generateNoResep();
            $this->isEditing = false;

            // Copy resep data
            $this->kdDokter = $originalResep->kd_dokter;
            $this->status = $originalResep->status;

            // Copy obat to table
            foreach($originalResep->resepDokter as $detail) {
                $obat = $detail->databarang;
                $this->obatTable[] = [
                    'kode_brng' => $obat->kode_brng,
                    'nama_brng' => $obat->nama_brng,
                    'satuan' => $obat->satuanKecil->satuan ?? '-',
                    'komposisi' => $obat->komposisi,
                    'harga' => $obat->ralan,
                    'formatted_harga' => $obat->formatted_harga_ralan,
                    'jenis' => $obat->jenisBarang->nama ?? '-',
                    'industri' => $obat->industriFarmasi->nama_industri ?? '-',
                    'stok' => $obat->total_stok,
                    'formatted_stok' => $obat->formatted_total_stok,
                    'jumlah' => $detail->jml,
                    'aturan_pakai' => $detail->aturan_pakai,
                    'subtotal' => $obat->ralan * $detail->jml
                ];
            }

            Notification::make()
                ->title('Resep Diduplikasi')
                ->body('Data resep berhasil diduplikasi ke form baru')
                ->success()
                ->send();
        }
    }

    public function updatedSearchObat(): void
    {
        $this->searchObat();
    }

    public function render()
    {
        return view('livewire.resep-obat-form');
    }
}