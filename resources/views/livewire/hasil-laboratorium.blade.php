<div x-data="{
    darkMode: document.documentElement.classList.contains('dark')
}" class="p-6" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
            🔬 Hasil Laboratorium - {{ $regPeriksa->pasien->nm_pasien ?? 'Pasien' }}
        </h3>

        <!-- Filter Section -->
        <div class="rounded-lg p-4 mb-4" x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                        Cari Pemeriksaan
                    </label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchKeyword"
                        placeholder="Nama pemeriksaan atau nilai..."
                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                    >
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                        Tanggal Mulai
                    </label>
                    <input
                        type="date"
                        wire:model.live="tanggalMulai"
                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                        Tanggal Selesai
                    </label>
                    <input
                        type="date"
                        wire:model.live="tanggalSelesai"
                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                    >
                </div>

                <!-- Abnormal Filter -->
                <div class="flex items-end">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model.live="hanyaAbnormal"
                            class="rounded text-blue-600 focus:ring-blue-500"
                            x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300'"
                        >
                        <span class="ml-2 text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Hanya Abnormal</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 mt-4">
                <button
                    wire:click="clearFilters"
                    class="px-4 py-2 text-sm rounded-md transition-colors"
                    x-bind:class="darkMode ? 'bg-gray-600 hover:bg-gray-500 text-gray-300' : 'bg-gray-500 hover:bg-gray-600 text-white'"
                >
                    Reset Filter
                </button>

                @if(count($selectedItems) > 0)
                    <button
                        wire:click="clearSelection"
                        class="px-4 py-2 text-sm rounded-md transition-colors"
                        x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-500 text-gray-100' : 'bg-red-500 hover:bg-red-600 text-white'"
                    >
                        Hapus Pilihan ({{ count($selectedItems) }})
                    </button>
                @endif
            </div>
        </div>

        <!-- Summary Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-blue-900/20' : 'bg-blue-50'">
                <div class="text-2xl font-bold" x-bind:class="darkMode ? 'text-blue-400' : 'text-blue-600'">{{ $totalResults }}</div>
                <div class="text-sm" x-bind:class="darkMode ? 'text-blue-300' : 'text-blue-800'">Total Hasil</div>
            </div>
            <div class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-yellow-900/20' : 'bg-yellow-50'">
                <div class="text-2xl font-bold" x-bind:class="darkMode ? 'text-yellow-400' : 'text-yellow-600'">{{ $abnormalCount }}</div>
                <div class="text-sm" x-bind:class="darkMode ? 'text-yellow-300' : 'text-yellow-800'">Hasil Abnormal</div>
            </div>
            <div class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-green-900/20' : 'bg-green-50'">
                <div class="text-2xl font-bold" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">{{ $totalResults - $abnormalCount }}</div>
                <div class="text-sm" x-bind:class="darkMode ? 'text-green-300' : 'text-green-800'">Hasil Normal</div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto rounded-lg shadow" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">
        <table class="min-w-full divide-y" x-bind:class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
            <thead x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="rounded text-blue-600 focus:ring-blue-500"
                                x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300'"
                            >
                            <span class="ml-2 text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                Pilih
                            </span>
                        </label>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Tanggal/Jam
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Pemeriksaan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Nilai
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Nilai Rujukan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Satuan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" x-bind:class="darkMode ? 'bg-gray-800 divide-gray-700' : 'bg-white divide-gray-200'">
                @forelse($hasilLab as $hasil)
                    <tr class="transition-colors" x-bind:class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
                        @if($hasil->is_abnormal)
                            x-bind:class="darkMode ? 'bg-red-900/20' : 'bg-red-50'"
                        @endif
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input
                                type="checkbox"
                                wire:model.live="selectedItems"
                                value="{{ $hasil->id }}"
                                class="rounded text-blue-600 focus:ring-blue-500"
                                x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300'"
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            <div>{{ $hasil->formatted_tgl_periksa }}</div>
                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $hasil->formatted_jam }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            <div class="font-medium">{{ $hasil->templateLaboratorium->Pemeriksaan ?? '-' }}</div>
                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $hasil->jenisPerawatan->nm_perawatan ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="font-medium {{ $hasil->is_abnormal ? 'text-red-600' : '' }}"
                                  x-bind:class="darkMode ? ({{ $hasil->is_abnormal ? 'true' : 'false' }} ? 'text-red-400' : 'text-white') : ({{ $hasil->is_abnormal ? 'true' : 'false' }} ? 'text-red-600' : 'text-gray-900')">
                                {{ $hasil->nilai ?: '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            {{ $hasil->nilai_rujukan ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            {{ $hasil->templateLaboratorium->satuan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($hasil->is_abnormal)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      x-bind:class="darkMode ? 'bg-red-900/50 text-red-200' : 'bg-red-100 text-red-800'">
                                    {{ $hasil->status_normal }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      x-bind:class="darkMode ? 'bg-green-900/50 text-green-200' : 'bg-green-100 text-green-800'">
                                    Normal
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada hasil laboratorium</p>
                                <p class="text-sm">Belum ada data hasil pemeriksaan laboratorium untuk periode ini</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($hasilLab->hasPages())
        <div class="mt-6">
            {{ $hasilLab->links() }}
        </div>
    @endif
</div>
