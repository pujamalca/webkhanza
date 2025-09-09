<div>
    <!-- Form Section untuk Input Pemeriksaan Baru -->
    <x-filament::section class="mb-6">
        <x-slot name="heading">Input Pemeriksaan Baru</x-slot>
        
        <form wire:submit="simpanPemeriksaan">
            {{ $this->form }}
            
            <!-- Tombol aksi di dalam form yang sama -->
            <div class="flex items-center gap-x-3 mt-6">
                <x-filament::button type="button" color="gray" size="sm" wire:click="resetForm">
                    Reset
                </x-filament::button>
                <x-filament::button type="submit" size="sm">
                    Simpan Pemeriksaan
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <!-- Table Section untuk Riwayat Pemeriksaan -->
    <x-filament::section>
        <x-slot name="heading">Riwayat Pemeriksaan</x-slot>
        
        @if($this->pemeriksaanList->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jam</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vital Signs</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Physical</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Neurologi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keluhan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->pemeriksaanList as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $item->tgl_perawatan ? $item->tgl_perawatan->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $item->jam_rawat ?? '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    <div class="space-y-1">
                                        @if($item->suhu_tubuh)
                                            <div>Suhu: {{ $item->suhu_tubuh }}°C</div>
                                        @endif
                                        @if($item->tensi)
                                            <div>Tensi: {{ $item->tensi }} mmHg</div>
                                        @endif
                                        @if($item->nadi)
                                            <div>Nadi: {{ $item->nadi }} x/mnt</div>
                                        @endif
                                        @if($item->respirasi)
                                            <div>RR: {{ $item->respirasi }} x/mnt</div>
                                        @endif
                                        @if($item->spo2)
                                            <div>SpO2: {{ $item->spo2 }}%</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    <div class="space-y-1">
                                        @if($item->tinggi)
                                            <div>TB: {{ $item->tinggi }} cm</div>
                                        @endif
                                        @if($item->berat)
                                            <div>BB: {{ $item->berat }} kg</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    <div class="space-y-1">
                                        @if($item->gcs)
                                            <div>GCS: {{ $item->gcs }}</div>
                                        @endif
                                        @if($item->kesadaran)
                                            <div>{{ $item->kesadaran }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 max-w-xs">
                                    <div class="space-y-2">
                                        @if($item->keluhan)
                                            <div>
                                                <span class="font-medium">Keluhan:</span>
                                                <p class="text-xs mt-1">{{ Str::limit($item->keluhan, 50) }}</p>
                                            </div>
                                        @endif
                                        @if($item->pemeriksaan)
                                            <div>
                                                <span class="font-medium">Pemeriksaan:</span>
                                                <p class="text-xs mt-1">{{ Str::limit($item->pemeriksaan, 50) }}</p>
                                            </div>
                                        @endif
                                        @if($item->penilaian)
                                            <div>
                                                <span class="font-medium">Penilaian:</span>
                                                <p class="text-xs mt-1">{{ Str::limit($item->penilaian, 50) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $item->petugas->nama ?? $item->nip ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>Belum ada data pemeriksaan</p>
            </div>
        @endif
    </x-filament::section>
</div>