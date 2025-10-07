<?php

namespace App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource\Pages;

use App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class AntreanPerTanggal extends Page
{
    protected static string $resource = ServiceJknErmResource::class;

    public function getTitle(): string
    {
        return 'Antrean Per Tanggal';
    }

    public function getView(): string
    {
        return 'filament.clusters.administrator.resources.service-jkn-erm-resource.pages.antrean-per-tanggal';
    }

    public $tanggal_awal;
    public $tanggal_akhir;
    public $antreanData = [];
    public $statistics = [
        'total' => 0,
        'jkn' => 0,
        'non_jkn' => 0,
        'checkin' => 0,
        'dilayani' => 0,
        'selesai' => 0,
        'batal' => 0,
    ];

    public function mount(): void
    {
        $this->tanggal_awal = now()->format('Y-m-d');
        $this->tanggal_akhir = now()->format('Y-m-d');
    }

    protected function getViewData(): array
    {
        return [
            'antreanData' => $this->antreanData,
            'statistics' => $this->statistics,
        ];
    }

    public function fetchAntreanData()
    {
        try {
            if (empty($this->tanggal_awal) || empty($this->tanggal_akhir)) {
                Notification::make()
                    ->title('Tanggal harus diisi')
                    ->danger()
                    ->send();
                return;
            }

            $apiUrl = config('services.bpjs.mobile_jkn_url');
            $apiKey = config('services.bpjs.api_key');
            $secretKey = config('services.bpjs.secret_key');

            if (empty($apiUrl) || empty($apiKey)) {
                Notification::make()
                    ->title('Konfigurasi API belum lengkap')
                    ->body('Silakan isi BPJS_API_KEY dan BPJS_SECRET_KEY di .env')
                    ->warning()
                    ->send();
                return;
            }

            // Format tanggal untuk API
            $tanggalAwal = date('Y-m-d', strtotime($this->tanggal_awal));
            $tanggalAkhir = date('Y-m-d', strtotime($this->tanggal_akhir));

            // Generate signature
            $timestamp = time();
            $data = $apiKey . "&" . $timestamp;
            $signature = hash_hmac('sha256', $data, $secretKey, false);

            // Call API
            $response = Http::withHeaders([
                'x-cons-id' => $apiKey,
                'x-timestamp' => $timestamp,
                'x-signature' => $signature,
                'user_key' => $secretKey,
                'Content-Type' => 'application/json',
            ])->get($apiUrl . '/antrean/pendaftaran/tanggal/' . $tanggalAwal . '/sampai/' . $tanggalAkhir);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['metadata']['code']) && $result['metadata']['code'] == 200) {
                    $this->antreanData = $result['response']['list'] ?? [];
                    $this->calculateStatistics();

                    Notification::make()
                        ->title('Data berhasil dimuat')
                        ->body('Ditemukan ' . count($this->antreanData) . ' data antrean')
                        ->success()
                        ->send();
                } else {
                    throw new \Exception($result['metadata']['message'] ?? 'Gagal mengambil data');
                }
            } else {
                throw new \Exception('API error: ' . $response->status());
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();

            \Log::error('Fetch Antrean Error: ' . $e->getMessage());
        }
    }

    protected function calculateStatistics()
    {
        $data = collect($this->antreanData);

        $this->statistics = [
            'total' => $data->count(),
            'jkn' => $data->where('jenispasien', 'JKN')->count(),
            'non_jkn' => $data->where('jenispasien', 'NON JKN')->count(),
            'checkin' => $data->where('status', 'Checkin')->count(),
            'dilayani' => $data->where('status', 'Dilayani')->count(),
            'selesai' => $data->where('status', 'Selesai')->count(),
            'batal' => $data->where('status', 'Batal')->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
