<?php

namespace App\Livewire;

use App\Models\PeriksaLab;
use App\Models\DetailPeriksaLab;
use App\Models\TemplateLaboratorium;
use App\Models\JenisPerawatan;
use App\Models\RegPeriksa;
use App\Models\Dokter;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PemeriksaanLaboratorium extends Component
{
    use WithPagination;

    public string $noRawat;
    public $regPeriksa;

    // Form data
    public string $tanggalPeriksa = '';
    public string $jamPeriksa = '';
    public string $kdJenisPrw = '';
    public string $dokterPerujuk = '';
    public string $status = 'Ralan';

    // Template dan detail pemeriksaan
    public array $templateItems = [];
    public array $selectedTemplates = [];

    // Edit mode
    public bool $isEditing = false;
    public $periksaLabRecord = null;

    // Pagination
    protected $paginationTheme = 'bootstrap';

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Set default values
        $this->tanggalPeriksa = now()->format('Y-m-d');
        $this->jamPeriksa = now()->format('H:i:s');

        if ($this->regPeriksa) {
            $this->dokterPerujuk = $this->regPeriksa->kd_dokter ?? '';
        }

        // Check if there's existing lab examination
        $this->checkExistingLab();
    }

    public function checkExistingLab(): void
    {
        $this->periksaLabRecord = PeriksaLab::where('no_rawat', $this->noRawat)->first();

        if ($this->periksaLabRecord) {
            $this->isEditing = true;
            $this->tanggalPeriksa = $this->periksaLabRecord->tgl_periksa->format('Y-m-d');
            $this->jamPeriksa = $this->periksaLabRecord->jam;
            $this->kdJenisPrw = $this->periksaLabRecord->kd_jenis_prw;
            $this->dokterPerujuk = $this->periksaLabRecord->dokter_perujuk;
            $this->status = $this->periksaLabRecord->status;

            // Load existing details
            $this->loadExistingDetails();
        }
    }

    public function loadExistingDetails(): void
    {
        $details = DetailPeriksaLab::where('no_rawat', $this->noRawat)
            ->with('templateLaboratorium')
            ->get();

        $this->templateItems = [];
        foreach ($details as $detail) {
            $this->templateItems[] = [
                'kd_jenis_prw' => $detail->kd_jenis_prw,
                'id_template' => $detail->id_template,
                'pemeriksaan' => $detail->templateLaboratorium->Pemeriksaan ?? '',
                'satuan' => $detail->templateLaboratorium->satuan ?? '',
                'nilai_rujukan' => $detail->templateLaboratorium->nilai_rujukan_ld . '-' . $detail->templateLaboratorium->nilai_rujukan_la ?? '',
                'nilai' => $detail->nilai,
                'keterangan' => $detail->keterangan,
                'biaya_item' => $detail->biaya_item
            ];
        }
    }

    public function updatedKdJenisPrw(): void
    {
        $this->selectedTemplates = [];
        $this->templateItems = [];
    }

    public function addTemplate(): void
    {
        if (!$this->kdJenisPrw) {
            session()->flash('error', 'Pilih jenis perawatan terlebih dahulu');
            return;
        }

        try {
            // Debug: Log the selected kd_jenis_prw
            \Log::info('Adding template for kd_jenis_prw: ' . $this->kdJenisPrw);

            $templates = TemplateLaboratorium::where('kd_jenis_prw', $this->kdJenisPrw)
                ->orderBy('urut')
                ->get();

            \Log::info('Found templates count: ' . $templates->count());

            if ($templates->isEmpty()) {
                // Try to get any templates for debugging
                $allTemplates = TemplateLaboratorium::select('kd_jenis_prw')
                    ->distinct()
                    ->pluck('kd_jenis_prw')
                    ->toArray();

                session()->flash('error', 'Tidak ada template laboratorium untuk jenis perawatan ini. Kode yang tersedia: ' . implode(', ', $allTemplates));
                return;
            }

            // Clear existing items first
            $this->templateItems = [];

            foreach ($templates as $template) {
                $this->templateItems[] = [
                    'kd_jenis_prw' => $template->kd_jenis_prw,
                    'id_template' => $template->id_template,
                    'pemeriksaan' => $template->Pemeriksaan,
                    'satuan' => $template->satuan ?? '',
                    'nilai_rujukan' => ($template->nilai_rujukan_ld ?? '') . '-' . ($template->nilai_rujukan_la ?? ''),
                    'nilai' => '',
                    'keterangan' => '',
                    'biaya_item' => $template->biaya_item ?? 0
                ];
            }

            session()->flash('success', 'Template berhasil ditambahkan: ' . $templates->count() . ' pemeriksaan');

        } catch (\Exception $e) {
            \Log::error('Error adding template: ' . $e->getMessage());
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function removeTemplate($index): void
    {
        unset($this->templateItems[$index]);
        $this->templateItems = array_values($this->templateItems);
    }

    public function simpanPemeriksaan(): void
    {
        $this->validate([
            'tanggalPeriksa' => 'required|date',
            'jamPeriksa' => 'required',
            'kdJenisPrw' => 'required',
            'templateItems' => 'required|array|min:1',
        ], [
            'templateItems.required' => 'Minimal harus ada satu pemeriksaan',
            'templateItems.min' => 'Minimal harus ada satu pemeriksaan',
        ]);

        try {
            DB::beginTransaction();

            // Get template for biaya calculation
            $jenisPerawatan = JenisPerawatan::where('kd_jenis_prw', $this->kdJenisPrw)->first();
            if (!$jenisPerawatan) {
                throw new \Exception('Jenis perawatan tidak ditemukan');
            }

            // Calculate total cost
            $totalBiaya = 0;
            foreach ($this->templateItems as $item) {
                $totalBiaya += floatval($item['biaya_item'] ?? 0);
            }

            // Create or update periksa_lab
            $periksaLabData = [
                'no_rawat' => $this->noRawat,
                'nip' => Auth::user()->nip ?? '',
                'kd_jenis_prw' => $this->kdJenisPrw,
                'tgl_periksa' => $this->tanggalPeriksa,
                'jam' => $this->jamPeriksa,
                'dokter_perujuk' => $this->dokterPerujuk,
                'bagian_rs' => $jenisPerawatan->total_byrdr ?? 0,
                'bhp' => 0,
                'bagian_perujuk' => $jenisPerawatan->total_byrpr ?? 0,
                'bagian_dokter' => $jenisPerawatan->total_byrdrpr ?? 0,
                'bagian_laborat' => 1000,
                'kso' => 0,
                'menejemen' => 0,
                'biaya_item' => $totalBiaya,
                'kd_dokter' => $this->dokterPerujuk,
                'status' => $this->status
            ];

            if ($this->isEditing) {
                PeriksaLab::where('no_rawat', $this->noRawat)->update($periksaLabData);
                // Delete existing details
                DetailPeriksaLab::where('no_rawat', $this->noRawat)->delete();
            } else {
                PeriksaLab::create($periksaLabData);
            }

            // Create detail_periksa_lab records
            foreach ($this->templateItems as $item) {
                $template = TemplateLaboratorium::where('kd_jenis_prw', $this->kdJenisPrw)
                    ->where('id_template', $item['id_template'])
                    ->first();

                if ($template) {
                    DetailPeriksaLab::create([
                        'no_rawat' => $this->noRawat,
                        'kd_jenis_prw' => $this->kdJenisPrw,
                        'tgl_periksa' => $this->tanggalPeriksa,
                        'jam' => $this->jamPeriksa,
                        'id_template' => $item['id_template'],
                        'nilai' => $item['nilai'] ?? '',
                        'nilai_rujukan' => $template->nilai_rujukan_ld . '-' . $template->nilai_rujukan_la,
                        'keterangan' => $this->determineKeterangan($item['nilai'] ?? '', $template),
                        'bagian_rs' => $template->bagian_rs ?? 0,
                        'bhp' => $template->bhp ?? 0,
                        'bagian_perujuk' => $template->bagian_perujuk ?? 0,
                        'bagian_dokter' => $template->bagian_dokter ?? 0,
                        'bagian_laborat' => $template->bagian_laborat ?? 0,
                        'kso' => $template->kso ?? 0,
                        'menejemen' => $template->menejemen ?? 0,
                        'biaya_item' => $template->biaya_item ?? 0
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', $this->isEditing ? 'Pemeriksaan laboratorium berhasil diperbarui' : 'Pemeriksaan laboratorium berhasil disimpan');
            $this->checkExistingLab();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan pemeriksaan: ' . $e->getMessage());
        }
    }

    private function determineKeterangan(string $nilai, $template): string
    {
        if (empty($nilai) || !is_numeric($nilai)) {
            return '';
        }

        $nilaiFloat = floatval($nilai);
        $batasRendah = floatval($template->nilai_rujukan_ld ?? 0);
        $batasTinggi = floatval($template->nilai_rujukan_la ?? 0);

        if ($batasRendah > 0 && $nilaiFloat < $batasRendah) {
            return 'L'; // Low
        }

        if ($batasTinggi > 0 && $nilaiFloat > $batasTinggi) {
            return 'H'; // High
        }

        return ''; // Normal
    }

    public function hapusPemeriksaan(): void
    {
        try {
            DB::beginTransaction();

            DetailPeriksaLab::where('no_rawat', $this->noRawat)->delete();
            PeriksaLab::where('no_rawat', $this->noRawat)->delete();

            DB::commit();

            $this->isEditing = false;
            $this->periksaLabRecord = null;
            $this->templateItems = [];

            session()->flash('success', 'Pemeriksaan laboratorium berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus pemeriksaan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $jenisPerawatan = JenisPerawatan::where(function($query) {
                $query->where('nm_perawatan', 'like', '%lab%')
                      ->orWhere('nm_perawatan', 'like', '%Labor%')
                      ->orWhere('nm_perawatan', 'like', '%LABOR%')
                      ->orWhere('kd_kategori', 'like', '%LAB%');
            })
            ->where('status', '1')
            ->orderBy('nm_perawatan')
            ->get();

        $dokters = Dokter::where('status', '1')
            ->orderBy('nm_dokter')
            ->get();

        $hasilLab = DetailPeriksaLab::with(['templateLaboratorium', 'jenisPerawatan'])
            ->byNoRawat($this->noRawat)
            ->orderBy('tgl_periksa', 'desc')
            ->orderBy('jam', 'desc')
            ->paginate(10);

        return view('livewire.pemeriksaan-laboratorium', [
            'jenisPerawatan' => $jenisPerawatan,
            'dokters' => $dokters,
            'hasilLab' => $hasilLab
        ]);
    }
}