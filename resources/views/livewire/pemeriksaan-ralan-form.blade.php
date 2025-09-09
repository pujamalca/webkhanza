<div>
    <!-- Form Section -->
    <x-filament::section>
        <x-slot name="heading">Input Pemeriksaan Baru</x-slot>
        
        <form wire:submit="simpanPemeriksaan" class="space-y-6">
            {{ $this->form }}
            
            <div class="flex justify-end gap-x-3">
                <x-filament::button type="button" color="gray" wire:click="resetForm">
                    Reset
                </x-filament::button>
                <x-filament::button type="submit">
                    Simpan Pemeriksaan
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <!-- Data List Section -->
    <x-filament::section>
        <x-slot name="heading">Riwayat Pemeriksaan</x-slot>
        
        @if($this->pemeriksaanList->count() > 0)
            <div class="space-y-4">
                @foreach($this->pemeriksaanList as $pemeriksaan)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex gap-4">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $pemeriksaan->tgl_perawatan->format('d/m/Y') }}
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-400 ml-2">
                                        {{ substr($pemeriksaan->jam_rawat, 0, 5) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
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
                        </div>
                        
                        <!-- Tanda Vital -->
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4">
                            @if($pemeriksaan->suhu_tubuh)
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Suhu</span>
                                    <p class="font-medium">{{ $pemeriksaan->suhu_tubuh }}°C</p>
                                </div>
                            @endif
                            @if($pemeriksaan->tensi)
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Tensi</span>
                                    <p class="font-medium">{{ $pemeriksaan->tensi }}</p>
                                </div>
                            @endif
                            @if($pemeriksaan->nadi)
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Nadi</span>
                                    <p class="font-medium">{{ $pemeriksaan->nadi }} x/mnt</p>
                                </div>
                            @endif
                            @if($pemeriksaan->respirasi)
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Respirasi</span>
                                    <p class="font-medium">{{ $pemeriksaan->respirasi }} x/mnt</p>
                                </div>
                            @endif
                            @if($pemeriksaan->spo2)
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">SpO2</span>
                                    <p class="font-medium">{{ $pemeriksaan->spo2 }}%</p>
                                </div>
                            @endif
                            @if($pemeriksaan->kesadaran)
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Kesadaran</span>
                                    <p class="font-medium">{{ $pemeriksaan->kesadaran }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Detail Info -->
                        @if($pemeriksaan->keluhan || $pemeriksaan->pemeriksaan || $pemeriksaan->penilaian)
                            <div class="space-y-2 text-sm">
                                @if($pemeriksaan->keluhan)
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Keluhan:</span>
                                        <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $pemeriksaan->keluhan }}</p>
                                    </div>
                                @endif
                                @if($pemeriksaan->pemeriksaan)
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Pemeriksaan:</span>
                                        <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $pemeriksaan->pemeriksaan }}</p>
                                    </div>
                                @endif
                                @if($pemeriksaan->penilaian)
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">Penilaian:</span>
                                        <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $pemeriksaan->penilaian }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
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