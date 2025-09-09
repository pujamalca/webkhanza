<div>
    <!-- Form Section -->
    <x-filament::section>
        <x-slot name="heading">Input Pemeriksaan Baru</x-slot>
        
        <form wire:submit="simpanPemeriksaan" class="space-y-6 ">
            {{ $this->form }}

        </form>
    </x-filament::section>

    <x-filament::section>
            <div class="flex p-5 gap-x-5 ">
                <x-filament::button type="button" color="gray" wire:click="resetForm">
                    Reset
                </x-filament::button>
                <x-filament::button type="submit">
                    Simpan Pemeriksaan
                </x-filament::button>
            </div>
    </x-filament::section>

    <!-- Data List Section -->
    <x-filament::section>
        <x-slot name="heading">Riwayat Pemeriksaan</x-slot>
        
        @if($this->pemeriksaanList->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Jam</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Suhu</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Tensi</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nadi</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">RR</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">SpO2</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">TB</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">BB</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">GCS</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Kesadaran</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Keluhan</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Pemeriksaan</th>
                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Penilaian</th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->pemeriksaanList as $pemeriksaan)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->tgl_perawatan->format('d/m/Y') }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ substr($pemeriksaan->jam_rawat, 0, 5) }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->suhu_tubuh ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->tensi ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->nadi ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->respirasi ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->spo2 ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->tinggi ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->berat ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->gcs ?: '-' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">{{ $pemeriksaan->kesadaran ?: '-' }}</td>
                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-white max-w-32"><div class="truncate">{{ $pemeriksaan->keluhan ?: '-' }}</div></td>
                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-white max-w-32"><div class="truncate">{{ $pemeriksaan->pemeriksaan ?: '-' }}</div></td>
                                <td class="px-2 py-2 text-xs text-gray-900 dark:text-white max-w-32"><div class="truncate">{{ $pemeriksaan->penilaian ?: '-' }}</div></td>
                                <td class="px-2 py-2 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-1">
                                        <x-filament::button 
                                            size="xs" 
                                            color="gray"
                                            wire:click="editPemeriksaan('{{ $pemeriksaan->tgl_perawatan->format('Y-m-d') }}', '{{ $pemeriksaan->jam_rawat }}')"
                                        >
                                            Edit
                                        </x-filament::button>
                                        <x-filament::button 
                                            size="xs" 
                                            color="danger"
                                            wire:click="hapusPemeriksaan('{{ $pemeriksaan->tgl_perawatan->format('Y-m-d') }}', '{{ $pemeriksaan->jam_rawat }}')"
                                            wire:confirm="Yakin ingin menghapus pemeriksaan ini?"
                                        >
                                            Hapus
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 dark:text-gray-600 mb-2">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Belum ada pemeriksaan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan input pemeriksaan baru menggunakan form di atas</p>
            </div>
        @endif
    </x-filament::section>
</div>