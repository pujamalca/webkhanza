<?php

namespace App\Livewire;

use App\Models\MasterBerkasPegawai;
use App\Models\BerkasPegawai;
use Livewire\Component;
use Filament\Notifications\Notification;

class MasterBerkasManager extends Component
{
    public $masterBerkas = [];

    public function mount()
    {
        $this->loadMasterBerkas();
    }

    public function loadMasterBerkas()
    {
        $this->masterBerkas = MasterBerkasPegawai::orderBy('kode')
            ->get()
            ->map(function ($item) {
                $inUse = BerkasPegawai::where('kode_berkas', $item->kode)->exists();
                return [
                    'kode' => $item->kode,
                    'nama_berkas' => $item->nama_berkas,
                    'kategori' => $item->kategori,
                    'no_urut' => $item->no_urut,
                    'in_use' => $inUse,
                    'usage_count' => BerkasPegawai::where('kode_berkas', $item->kode)->count()
                ];
            })
            ->toArray();
    }

    public function deleteMasterBerkas($kode)
    {
        try {
            $masterBerkas = MasterBerkasPegawai::find($kode);
            
            if (!$masterBerkas) {
                Notification::make()
                    ->danger()
                    ->title('Data tidak ditemukan')
                    ->body("Jenis berkas dengan kode {$kode} tidak ditemukan.")
                    ->send();
                return;
            }

            // Check if berkas is being used
            $inUse = BerkasPegawai::where('kode_berkas', $kode)->exists();
            if ($inUse) {
                $usageCount = BerkasPegawai::where('kode_berkas', $kode)->count();
                Notification::make()
                    ->danger()
                    ->title('Tidak dapat menghapus')
                    ->body("Jenis berkas {$kode} sedang digunakan oleh {$usageCount} berkas pegawai. Hapus berkas pegawai yang menggunakan jenis ini terlebih dahulu.")
                    ->persistent()
                    ->send();
                return;
            }

            // Delete the master berkas
            $masterBerkas->delete();

            Notification::make()
                ->success()
                ->title('Berhasil dihapus')
                ->body("Jenis berkas {$kode} - {$masterBerkas->nama_berkas} berhasil dihapus.")
                ->send();

            // Reload the data
            $this->loadMasterBerkas();

            // Emit event to refresh parent component if needed
            $this->dispatch('master-berkas-updated');
            
            // Also refresh the parent form's select options
            $this->dispatch('refreshSelectOptions');

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal menghapus')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->persistent()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.master-berkas-manager');
    }
}
