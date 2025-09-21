<div x-data="{
    darkMode: document.documentElement.classList.contains('dark')
}" class="space-y-4" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">

    <!-- Header Info -->
    <div class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
        <h3 class="text-md font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
            🔬 Cari Hasil Laboratorium
        </h3>
        @if($regPeriksa)
        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
            Pasien: <span class="font-medium">{{ $regPeriksa->pasien->nm_pasien ?? 'N/A' }}</span> |
            No. RM: <span class="font-medium">{{ $regPeriksa->no_rkm_medis }}</span> |
            No. Rawat: <span class="font-medium">{{ $noRawat }}</span>
        </p>
        @endif
    </div>

    <!-- Search Panel -->
    <div class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Kata Kunci
                </label>
                <input
                    type="text"
                    wire:model="keyword"
                    placeholder="Cari pemeriksaan, nilai, atau keterangan..."
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900 placeholder-gray-500'"
                >
            </div>

            <!-- Abnormal Filter -->
            <div class="flex items-end">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="hanyaAbnormal"
                        class="rounded text-blue-600 focus:ring-blue-500"
                        x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300'"
                    >
                    <span class="ml-2 text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Hanya Abnormal</span>
                </label>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <button
                wire:click="cari"
                class="px-4 py-2 text-sm rounded-md transition-colors"
                x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-500 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'"
            >
                🔍 Cari
            </button>

            <button
                wire:click="bersihkan"
                class="px-4 py-2 text-sm rounded-md transition-colors"
                x-bind:class="darkMode ? 'bg-gray-600 hover:bg-gray-500 text-gray-300' : 'bg-gray-500 hover:bg-gray-600 text-white'"
            >
                🗑️ Bersihkan
            </button>

            @if(count($selectedItems) > 0)
                <button
                    wire:click="hapusPilihan"
                    class="px-4 py-2 text-sm rounded-md transition-colors"
                    x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-500 text-gray-100' : 'bg-red-500 hover:bg-red-600 text-white'"
                >
                    Hapus Pilihan ({{ count($selectedItems) }})
                </button>
            @endif
        </div>

        <!-- Summary -->
        @if($totalResults > 0)
        <div class="mt-3 text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
            Total: {{ $totalResults }} hasil | Abnormal: {{ $abnormalCount }} hasil
        </div>
        @endif
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto rounded-lg shadow" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">
        <table class="min-w-full divide-y" x-bind:class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
            <thead x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                <tr>
                    <th class="px-4 py-3 text-left">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model="selectAll"
                                class="rounded text-blue-600 focus:ring-blue-500"
                                x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300'"
                            >
                            <span class="ml-2 text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                Pilih
                            </span>
                        </label>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Tanggal
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Jam
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Pemeriksaan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Hasil
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" x-bind:class="darkMode ? 'bg-gray-800 divide-gray-700' : 'bg-white divide-gray-200'">
                @forelse($hasilLab as $hasil)
                    <tr class="transition-colors"
                        x-bind:class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
                        @if($hasil->is_abnormal)
                            x-bind:class="darkMode ? 'bg-red-900/20' : 'bg-red-50'"
                        @endif
                    >
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input
                                type="checkbox"
                                wire:model="selectedItems"
                                value="{{ $hasil->id }}"
                                class="rounded text-blue-600 focus:ring-blue-500"
                                x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300'"
                            >
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            {{ $hasil->formatted_tgl_periksa }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            {{ $hasil->formatted_jam }}
                        </td>
                        <td class="px-4 py-3 text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            <div class="font-medium">{{ $hasil->templateLaboratorium->Pemeriksaan ?? '-' }}</div>
                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                {{ $hasil->jenisPerawatan->nm_perawatan ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="space-y-1">
                                <!-- Nilai -->
                                <div>
                                    <span class="font-medium"
                                          x-bind:class="darkMode ? ({{ $hasil->is_abnormal ? 'true' : 'false' }} ? 'text-red-400' : 'text-white') : ({{ $hasil->is_abnormal ? 'true' : 'false' }} ? 'text-red-600' : 'text-gray-900')">
                                        {{ $hasil->nilai ?: '-' }}
                                    </span>
                                    @if($hasil->templateLaboratorium->satuan)
                                        <span class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                            {{ $hasil->templateLaboratorium->satuan }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Nilai Rujukan -->
                                @if($hasil->nilai_rujukan)
                                <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                    Rujukan: {{ $hasil->nilai_rujukan }}
                                </div>
                                @endif

                                <!-- Status -->
                                @if($hasil->is_abnormal)
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                              x-bind:class="darkMode ? 'bg-red-900/50 text-red-200' : 'bg-red-100 text-red-800'">
                                            {{ $hasil->status_normal }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            <div class="flex flex-col items-center">
                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-medium">Tidak ada hasil laboratorium</p>
                                <p class="text-sm">Belum ada data hasil pemeriksaan untuk criteria ini</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($hasilLab->hasPages())
        <div class="mt-4">
            {{ $hasilLab->links() }}
        </div>
    @endif
</div>