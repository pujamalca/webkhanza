<?php

namespace App\Livewire;

use App\Models\PermintaanLab;
use App\Models\DetailPermintaanLab;
use App\Models\TemplateLaboratorium;
use App\Models\JenisPerawatan;
use App\Models\JenisPerawatanLab;
use App\Models\RegPeriksa;
use App\Models\Dokter;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PermintaanLaboratorium extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $noRawat;
    public $regPeriksa;

    // Form data
    public string $tanggalPermintaan = '';
    public string $jamPermintaan = '';
    public string $dokterPerujuk = '';
    public string $diagnosisKlinis = '';
    public string $informasiTambahan = '';
    public string $status = 'ralan';

    // Template dan detail pemeriksaan
    public array $selectedPemeriksaan = [];

    // Search and filter for templates
    public string $searchTemplate = '';
    public string $selectedKategori = '';
    public bool $selectAllTemplates = false;
    public int $perPage = 25;

    // Edit mode
    public bool $isEditing = false;
    public $permintaanLabRecord = null;


    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Set default values
        $this->tanggalPermintaan = now()->format('Y-m-d');
        $this->jamPermintaan = now()->format('H:i:s');

        if ($this->regPeriksa) {
            $this->dokterPerujuk = $this->regPeriksa->kd_dokter ?? '';
        }

        // Check if there's existing lab request
        $this->checkExistingPermintaan();
    }

    public function checkExistingPermintaan(): void
    {
        $this->permintaanLabRecord = PermintaanLab::where('no_rawat', $this->noRawat)->first();

        if ($this->permintaanLabRecord) {
            $this->isEditing = true;
            $this->tanggalPermintaan = $this->permintaanLabRecord->tgl_permintaan->format('Y-m-d');
            $this->jamPermintaan = $this->permintaanLabRecord->jam_permintaan;
            $this->dokterPerujuk = $this->permintaanLabRecord->dokter_perujuk;
            $this->diagnosisKlinis = $this->permintaanLabRecord->diagnosa_klinis ?? '';
            $this->informasiTambahan = $this->permintaanLabRecord->informasi_tambahan ?? '';
            $this->status = $this->permintaanLabRecord->status ?? 'ralan';

            // Load existing details
            $this->loadExistingDetails();
        }
    }

    public function updatedSearchTemplate(): void
    {
        $this->resetPage('templates');
    }

    public function updatedSelectedKategori(): void
    {
        $this->resetPage('templates');
    }

    public function updatedPerPage(): void
    {
        $this->resetPage('templates');
    }



    public function updatedSelectAllTemplates(): void
    {
        if ($this->selectAllTemplates) {
            $templates = $this->getFilteredTemplates();
            foreach ($templates as $template) {
                $this->togglePemeriksaan($template->kd_jenis_prw, $template->id_template, true);
            }
        } else {
            $this->selectedPemeriksaan = [];
        }
    }

    public function selectAllFiltered(): void
    {
        $templates = $this->getCurrentDisplayedTemplates();
        foreach ($templates as $template) {
            $this->togglePemeriksaan($template->kd_jenis_prw, $template->id_template, true);
        }
        session()->flash('success', "Semua template berhasil dipilih ({$templates->count()} item)");
    }

    public function selectAllInCategory(string $kdJenisPrw): void
    {
        // Get the currently displayed templates (from the paginated/filtered results)
        $currentTemplates = $this->getCurrentDisplayedTemplates();
        $templates = $currentTemplates->where('kd_jenis_prw', $kdJenisPrw);

        foreach ($templates as $template) {
            $this->togglePemeriksaan($template->kd_jenis_prw, $template->id_template, true);
        }

        $categoryName = $templates->first()?->jenisPerawatanLab?->nm_perawatan ?? 'kategori';
        session()->flash('success', "Semua pemeriksaan dalam {$categoryName} berhasil dipilih ({$templates->count()} item)");
    }

    private function getCurrentDisplayedTemplates()
    {
        // Get the same data as render() method to ensure consistency
        $templatesQuery = TemplateLaboratorium::with('jenisPerawatanLab')
            ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->where('jns_perawatan_lab.status', '1')
            ->where('jns_perawatan_lab.kategori', 'PK');

        if ($this->searchTemplate) {
            $templatesQuery->where('jns_perawatan_lab.nm_perawatan', 'like', '%' . $this->searchTemplate . '%');
        }

        if ($this->selectedKategori) {
            $templatesQuery->where('template_laboratorium.kd_jenis_prw', $this->selectedKategori);
        }

        // Use the same pagination logic as render()
        return $templatesQuery
            ->orderBy('jns_perawatan_lab.nm_perawatan')
            ->orderBy('template_laboratorium.Pemeriksaan')
            ->select('template_laboratorium.*')
            ->paginate($this->perPage, ['*'], 'templates');
    }

    public function deselectAllInCategory(string $kdJenisPrw): void
    {
        // Remove all selected items from this category
        $this->selectedPemeriksaan = collect($this->selectedPemeriksaan)
            ->reject(function($item) use ($kdJenisPrw) {
                return $item['kd_jenis_prw'] == $kdJenisPrw;
            })
            ->values()
            ->toArray();

        $templates = $this->getFilteredTemplates()->where('kd_jenis_prw', $kdJenisPrw);
        $categoryName = $templates->first()?->jenisPerawatanLab?->nm_perawatan ?? 'kategori';
        session()->flash('success', "Semua pilihan dalam {$categoryName} berhasil dibatalkan");
    }

    public function clearAllSelection(): void
    {
        $this->selectedPemeriksaan = [];
        $this->selectAllTemplates = false;
        session()->flash('success', 'Semua pilihan berhasil dihapus');
    }

    private function getFilteredTemplates()
    {
        $query = TemplateLaboratorium::with('jenisPerawatanLab')
            ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->where('jns_perawatan_lab.status', '1')
            ->where('jns_perawatan_lab.kategori', 'PK');

        if ($this->searchTemplate) {
            $query->where('jns_perawatan_lab.nm_perawatan', 'like', '%' . $this->searchTemplate . '%');
        }

        if ($this->selectedKategori) {
            $query->where('template_laboratorium.kd_jenis_prw', $this->selectedKategori);
        }

        return $query
            ->orderBy('jns_perawatan_lab.nm_perawatan')
            ->orderBy('template_laboratorium.Pemeriksaan')
            ->select('template_laboratorium.*')
            ->get();
    }

    public function loadExistingDetails(): void
    {
        $details = DB::select("SELECT dpl.*, tl.Pemeriksaan, tl.satuan, tl.nilai_rujukan_ld, tl.nilai_rujukan_la
            FROM permintaan_detail_permintaan_lab dpl
            LEFT JOIN template_laboratorium tl ON dpl.kd_jenis_prw = tl.kd_jenis_prw AND dpl.id_template = tl.id_template
            WHERE dpl.noorder = ?", [$this->permintaanLabRecord->noorder]);

        $this->selectedPemeriksaan = [];
        foreach ($details as $detail) {
            $this->selectedPemeriksaan[] = [
                'kd_jenis_prw' => $detail->kd_jenis_prw,
                'id_template' => $detail->id_template,
                'pemeriksaan' => $detail->Pemeriksaan ?? '',
                'satuan' => $detail->satuan ?? '',
                'nilai_rujukan' => ($detail->nilai_rujukan_ld ?? '') . '-' . ($detail->nilai_rujukan_la ?? ''),
                'stts_bayar' => $detail->stts_bayar ?? 'Belum',
                'checked' => true
            ];
        }
    }

    public function togglePemeriksaan($kdJenisPrw, $idTemplate, $forceAdd = false): void
    {
        $key = $kdJenisPrw . '_' . $idTemplate;
        $existing = collect($this->selectedPemeriksaan)->firstWhere(function($item) use ($kdJenisPrw, $idTemplate) {
            return $item['kd_jenis_prw'] == $kdJenisPrw && $item['id_template'] == $idTemplate;
        });

        if ($existing && !$forceAdd) {
            // Remove if exists (only if not forcing add)
            $this->selectedPemeriksaan = collect($this->selectedPemeriksaan)->reject(function($item) use ($kdJenisPrw, $idTemplate) {
                return $item['kd_jenis_prw'] == $kdJenisPrw && $item['id_template'] == $idTemplate;
            })->values()->toArray();
        } elseif (!$existing) {
            // Add if not exists
            $template = TemplateLaboratorium::where('kd_jenis_prw', $kdJenisPrw)
                ->where('id_template', $idTemplate)
                ->first();

            if ($template) {
                $this->selectedPemeriksaan[] = [
                    'kd_jenis_prw' => $template->kd_jenis_prw,
                    'id_template' => $template->id_template,
                    'pemeriksaan' => $template->Pemeriksaan,
                    'satuan' => $template->satuan ?? '',
                    'nilai_rujukan' => ($template->nilai_rujukan_ld ?? '') . '-' . ($template->nilai_rujukan_la ?? ''),
                    'stts_bayar' => 'Belum',
                    'checked' => true
                ];
            }
        }
    }

    public function simpanPermintaan(): void
    {
        $this->validate([
            'tanggalPermintaan' => 'required|date',
            'jamPermintaan' => 'required',
            'dokterPerujuk' => 'required',
            'diagnosisKlinis' => 'required',
            'selectedPemeriksaan' => 'required|array|min:1',
        ], [
            'selectedPemeriksaan.required' => 'Minimal harus ada satu pemeriksaan yang dipilih',
            'selectedPemeriksaan.min' => 'Minimal harus ada satu pemeriksaan yang dipilih',
            'diagnosisKlinis.required' => 'Diagnosis klinis harus diisi',
        ]);

        try {
            DB::beginTransaction();

            $noOrder = $this->isEditing ?
                $this->permintaanLabRecord->noorder :
                PermintaanLab::generateNoOrder();

            // Create or update permintaan_lab using raw query like resep obat
            if ($this->isEditing) {
                DB::statement("UPDATE permintaan_lab SET
                    no_rawat = ?, tgl_permintaan = ?, jam_permintaan = ?,
                    dokter_perujuk = ?, status = ?, diagnosa_klinis = ?, informasi_tambahan = ?
                    WHERE noorder = ?", [
                    $this->noRawat, $this->tanggalPermintaan, $this->jamPermintaan,
                    $this->dokterPerujuk, $this->status, $this->diagnosisKlinis,
                    $this->informasiTambahan, $noOrder
                ]);
                // Delete existing details
                DB::statement("DELETE FROM permintaan_detail_permintaan_lab WHERE noorder = ?", [$noOrder]);
            } else {
                DB::statement("INSERT INTO permintaan_lab
                    (noorder, no_rawat, tgl_permintaan, jam_permintaan, tgl_sampel, jam_sampel,
                     tgl_hasil, jam_hasil, dokter_perujuk, status, diagnosa_klinis, informasi_tambahan)
                    VALUES (?, ?, ?, ?, ?, '00:00:00', ?, '00:00:00', ?, ?, ?, ?)", [
                    $noOrder, $this->noRawat, $this->tanggalPermintaan, $this->jamPermintaan,
                    $this->tanggalPermintaan, $this->tanggalPermintaan, // Use same date as request date
                    $this->dokterPerujuk, $this->status, $this->diagnosisKlinis, $this->informasiTambahan
                ]);
            }

            // Create detail_permintaan_lab records using raw query
            foreach ($this->selectedPemeriksaan as $pemeriksaan) {
                DB::statement("INSERT INTO permintaan_detail_permintaan_lab
                    (noorder, kd_jenis_prw, id_template, stts_bayar)
                    VALUES (?, ?, ?, ?)", [
                    $noOrder,
                    $pemeriksaan['kd_jenis_prw'],
                    $pemeriksaan['id_template'],
                    $pemeriksaan['stts_bayar'] ?? 'Belum'
                ]);
            }

            DB::commit();

            session()->flash('success', $this->isEditing ?
                'Permintaan laboratorium berhasil diperbarui' :
                'Permintaan laboratorium berhasil disimpan dengan No. Order: ' . $noOrder);

            $this->checkExistingPermintaan();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan permintaan: ' . $e->getMessage());
        }
    }

    public function hapusPermintaan(): void
    {
        try {
            DB::beginTransaction();

            DB::statement("DELETE FROM permintaan_detail_permintaan_lab WHERE noorder = ?", [$this->permintaanLabRecord->noorder]);
            DB::statement("DELETE FROM permintaan_lab WHERE noorder = ?", [$this->permintaanLabRecord->noorder]);

            DB::commit();

            $this->isEditing = false;
            $this->permintaanLabRecord = null;
            $this->selectedPemeriksaan = [];

            session()->flash('success', 'Permintaan laboratorium berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus permintaan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $jenisPerawatanLab = JenisPerawatanLab::aktif()
            ->PK()
            ->orderBy('nm_perawatan')
            ->get();

        $dokters = Dokter::where('status', '1')
            ->orderBy('nm_dokter')
            ->get();

        // Get templates with pagination - only PK category
        $templatesQuery = TemplateLaboratorium::with('jenisPerawatanLab')
            ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->where('jns_perawatan_lab.status', '1')
            ->where('jns_perawatan_lab.kategori', 'PK');

        // Apply search filter - only search category names
        if ($this->searchTemplate) {
            $templatesQuery->where('jns_perawatan_lab.nm_perawatan', 'like', '%' . $this->searchTemplate . '%');
        }

        // Apply category filter
        if ($this->selectedKategori) {
            $templatesQuery->where('template_laboratorium.kd_jenis_prw', $this->selectedKategori);
        }

        // Use pagination with configurable perPage
        $templates = $templatesQuery
            ->orderBy('jns_perawatan_lab.nm_perawatan')
            ->orderBy('template_laboratorium.Pemeriksaan')
            ->select('template_laboratorium.*')
            ->paginate($this->perPage, ['*'], 'templates');

        // Group for display
        $groupedForDisplay = $templates->groupBy('kd_jenis_prw');

        // Get existing lab requests
        $permintaanLab = PermintaanLab::with(['regPeriksa.pasien', 'dokter', 'detailPermintaan'])
            ->byNoRawat($this->noRawat)
            ->orderBy('tgl_permintaan', 'desc')
            ->orderBy('jam_permintaan', 'desc')
            ->paginate(5, ['*'], 'history');

        return view('livewire.permintaan-laboratorium', [
            'jenisPerawatanLab' => $jenisPerawatanLab,
            'dokters' => $dokters,
            'templates' => $templates,
            'groupedTemplates' => $groupedForDisplay,
            'permintaanLab' => $permintaanLab
        ]);
    }
}