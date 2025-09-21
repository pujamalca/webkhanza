<?php

namespace App\Livewire;

use App\Models\DetailPeriksaLab;
use App\Models\RegPeriksa;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class CariHasilLaboratorium extends Component
{
    use WithPagination;

    public string $noRawat;
    public $regPeriksa;

    // Search and filter
    public string $keyword = '';
    public bool $hanyaAbnormal = false;

    // Selected items for actions
    public array $selectedItems = [];
    public bool $selectAll = false;

    // Pagination
    protected $paginationTheme = 'bootstrap';

    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        $this->regPeriksa = RegPeriksa::where('no_rawat', $noRawat)->first();
    }

    public function updatedKeyword(): void
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

    public function cari(): void
    {
        $this->resetPage();
    }

    public function bersihkan(): void
    {
        $this->keyword = '';
        $this->hanyaAbnormal = false;
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function hapusPilihan(): void
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

        // Apply keyword filter (search by examination name, value, or notes)
        if ($this->keyword) {
            $query->where(function($q) {
                $q->whereHas('templateLaboratorium', function($subQuery) {
                    $subQuery->where('Pemeriksaan', 'like', '%' . $this->keyword . '%');
                })
                ->orWhere('nilai', 'like', '%' . $this->keyword . '%')
                ->orWhere('keterangan', 'like', '%' . $this->keyword . '%');
            });
        }

        // Apply abnormal filter
        if ($this->hanyaAbnormal) {
            $query->whereIn('keterangan', ['L', 'T', 'H']);
        }

        return $query;
    }

    public function render()
    {
        $hasilLab = $this->getHasilLaboratorium()->paginate(10);
        $totalResults = $this->getHasilLaboratorium()->count();
        $abnormalCount = $this->getHasilLaboratorium()->whereIn('keterangan', ['L', 'T', 'H'])->count();

        return view('livewire.cari-hasil-laboratorium', [
            'hasilLab' => $hasilLab,
            'totalResults' => $totalResults,
            'abnormalCount' => $abnormalCount
        ]);
    }
}