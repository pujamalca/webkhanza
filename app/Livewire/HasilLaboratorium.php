<?php

namespace App\Livewire;

use App\Models\DetailPeriksaLab;
use App\Models\TemplateLaboratorium;
use App\Models\RegPeriksa;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class HasilLaboratorium extends Component
{
    use WithPagination;

    public string $noRawat;
    public $regPeriksa;

    // Search and filter
    public string $searchKeyword = '';
    public string $tanggalMulai = '';
    public string $tanggalSelesai = '';
    public bool $hanyaAbnormal = false;

    // Selected items
    public array $selectedItems = [];
    public bool $selectAll = false;

    // Pagination
    protected $paginationTheme = 'bootstrap';

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;

        // Load reg periksa data
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();

        // Set default date range (last 30 days)
        $this->tanggalSelesai = now()->format('Y-m-d');
        $this->tanggalMulai = now()->subDays(30)->format('Y-m-d');
    }

    public function updatedSearchKeyword(): void
    {
        $this->resetPage();
    }

    public function updatedTanggalMulai(): void
    {
        $this->resetPage();
    }

    public function updatedTanggalSelesai(): void
    {
        $this->resetPage();
    }

    public function updatedHanyaAbnormal(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedItems = $this->getHasilLaboratorium()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function clearFilters(): void
    {
        $this->searchKeyword = '';
        $this->hanyaAbnormal = false;
        $this->tanggalSelesai = now()->format('Y-m-d');
        $this->tanggalMulai = now()->subDays(30)->format('Y-m-d');
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function clearSelection(): void
    {
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    private function getHasilLaboratorium()
    {
        $query = DetailPeriksaLab::with(['templateLaboratorium', 'jenisPerawatan'])
            ->byNoRawat($this->noRawat)
            ->orderBy('tgl_periksa', 'desc')
            ->orderBy('jam', 'desc');

        // Apply date filter
        if ($this->tanggalMulai && $this->tanggalSelesai) {
            $query->byTanggal($this->tanggalMulai, $this->tanggalSelesai);
        } elseif ($this->tanggalMulai) {
            $query->byTanggal($this->tanggalMulai);
        }

        // Apply search filter
        if ($this->searchKeyword) {
            $query->searchByKeyword($this->searchKeyword);
        }

        // Apply abnormal filter
        if ($this->hanyaAbnormal) {
            $query->whereIn('keterangan', ['L', 'T', 'H']);
        }

        return $query;
    }

    public function render()
    {
        $hasilLab = $this->getHasilLaboratorium()->paginate(15);
        $totalResults = $this->getHasilLaboratorium()->count();
        $abnormalCount = $this->getHasilLaboratorium()->whereIn('keterangan', ['L', 'T', 'H'])->count();

        return view('livewire.hasil-laboratorium', [
            'hasilLab' => $hasilLab,
            'totalResults' => $totalResults,
            'abnormalCount' => $abnormalCount
        ]);
    }
}
