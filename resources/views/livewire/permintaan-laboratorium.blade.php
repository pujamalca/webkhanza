<div x-data="{
    darkMode: document.documentElement.classList.contains('dark'),
    showRequestForm: true
}" class="space-y-6" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">

    <!-- Header Info -->
    <div class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
        <h3 class="text-md font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
            🧪 Permintaan Pemeriksaan Laboratorium
        </h3>
        @if($regPeriksa)
        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
            Pasien: <span class="font-medium">{{ $regPeriksa->pasien->nm_pasien ?? 'N/A' }}</span> |
            No. RM: <span class="font-medium">{{ $regPeriksa->no_rkm_medis }}</span> |
            No. Rawat: <span class="font-medium">{{ $noRawat }}</span>
        </p>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2">
        <button
            @click="showRequestForm = !showRequestForm"
            class="px-4 py-2 text-sm rounded-md transition-colors"
            x-bind:class="darkMode ? 'bg-gray-600 hover:bg-gray-500 text-white' : 'bg-gray-500 hover:bg-gray-600 text-white'"
        >
            <span x-show="!showRequestForm">📋 Tampilkan Form</span>
            <span x-show="showRequestForm">🔼 Sembunyikan Form</span>
        </button>

        @if($isEditing)
        <button
            wire:click="hapusPermintaan"
            onclick="return confirm('Yakin ingin menghapus permintaan laboratorium ini?')"
            class="px-4 py-2 text-sm rounded-md transition-colors"
            x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-500 text-white' : 'bg-red-500 hover:bg-red-600 text-white'"
        >
            🗑️ Hapus Permintaan
        </button>
        @endif
    </div>

    <!-- Request Form -->
    <div x-show="showRequestForm" x-transition class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Tanggal Permintaan
                </label>
                <input
                    type="date"
                    wire:model="tanggalPermintaan"
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Jam Permintaan
                </label>
                <input
                    type="time"
                    wire:model="jamPermintaan"
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Status
                </label>
                <select
                    wire:model="status"
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                >
                    <option value="ralan">Rawat Jalan</option>
                    <option value="ranap">Rawat Inap</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Dokter Perujuk
                </label>
                <select
                    wire:model="dokterPerujuk"
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                >
                    <option value="">Pilih Dokter</option>
                    @foreach($dokters as $dokter)
                        <option value="{{ $dokter->kd_dokter }}">{{ $dokter->nm_dokter }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Diagnosis Klinis <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    wire:model="diagnosisKlinis"
                    placeholder="Masukkan diagnosis klinis"
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900 placeholder-gray-500'"
                >
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                Informasi Tambahan
            </label>
            <textarea
                wire:model="informasiTambahan"
                placeholder="Informasi tambahan untuk laboratorium..."
                rows="2"
                class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900 placeholder-gray-500'"
            ></textarea>
        </div>

        <!-- Lab Selection -->
        <div class="mb-4">
            <h4 class="font-medium mb-3" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">
                Pilih Pemeriksaan Laboratorium:
            </h4>

            <!-- Search and Filter Controls -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <input
                        type="text"
                        wire:model.live="searchTemplate"
                        placeholder="Cari kategori laboratorium..."
                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900 placeholder-gray-500'"
                    >
                </div>
                <div>
                    <select
                        wire:model.live="selectedKategori"
                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($jenisPerawatanLab as $jenis)
                            <option value="{{ $jenis->kd_jenis_prw }}">{{ $jenis->nm_perawatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select
                        wire:model.live="perPage"
                        class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                        x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                    >
                        <option value="10">10 per halaman</option>
                        <option value="25">25 per halaman</option>
                        <option value="50">50 per halaman</option>
                        <option value="100">100 per halaman</option>
                    </select>
                </div>
            </div>

            <!-- Global Actions -->
            <div class="flex gap-2 mb-4">
                <button
                    wire:click="selectAllFiltered"
                    class="px-3 py-1 text-xs rounded-md transition-colors"
                    x-bind:class="darkMode ? 'bg-green-600 hover:bg-green-500 text-white' : 'bg-green-500 hover:bg-green-600 text-white'"
                >
                    ✓ Pilih Semua Template
                </button>
                <button
                    wire:click="clearAllSelection"
                    class="px-3 py-1 text-xs rounded-md transition-colors"
                    x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-500 text-white' : 'bg-red-500 hover:bg-red-600 text-white'"
                >
                    ✗ Hapus Semua Pilihan
                </button>
                <span class="px-3 py-1 text-xs rounded-md" x-bind:class="darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'">
                    {{ count($selectedPemeriksaan) }} dipilih
                </span>
            </div>

            @if($templates->count() > 0)
                <!-- Templates by Category -->
                <div class="space-y-4 mb-4">
                    @foreach($groupedTemplates as $kdJenisPrw => $templateGroup)
                        <div class="p-3 border rounded-lg" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300 bg-white'">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="font-medium" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">
                                    {{ $templateGroup->first()->jenisPerawatanLab->nm_perawatan ?? 'Laboratorium' }}
                                    <span class="text-xs ml-2" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                        ({{ $templateGroup->count() }} pemeriksaan)
                                    </span>
                                </h5>
                                <div class="flex gap-2">
                                    <button
                                        wire:click="selectAllInCategory('{{ $kdJenisPrw }}')"
                                        class="px-2 py-1 text-xs rounded transition-colors"
                                        x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-500 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'"
                                    >
                                        ✓ Pilih Semua
                                    </button>
                                    <button
                                        wire:click="deselectAllInCategory('{{ $kdJenisPrw }}')"
                                        class="px-2 py-1 text-xs rounded transition-colors"
                                        x-bind:class="darkMode ? 'bg-gray-600 hover:bg-gray-500 text-white' : 'bg-gray-500 hover:bg-gray-600 text-white'"
                                    >
                                        ✗ Batal
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($templateGroup as $template)
                                    <label class="flex items-center p-2 rounded hover:shadow-sm transition-shadow cursor-pointer"
                                           x-bind:class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'">
                                        <input
                                            type="checkbox"
                                            wire:click="togglePemeriksaan('{{ $template->kd_jenis_prw }}', '{{ $template->id_template }}')"
                                            @checked(collect($selectedPemeriksaan)->contains(function($item) use ($template) {
                                                return $item['kd_jenis_prw'] == $template->kd_jenis_prw &&
                                                       $item['id_template'] == $template->id_template;
                                            }))
                                            class="rounded text-blue-600 focus:ring-blue-500"
                                            x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300'"
                                        >
                                        <span class="ml-2 text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                            {{ $template->Pemeriksaan }}
                                            @if($template->satuan)
                                                <span class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                    ({{ $template->satuan }})
                                                </span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <div class="p-8 text-center" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-gray-50'">
                    <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        @if($searchTemplate || $selectedKategori)
                            Tidak ada kategori laboratorium yang sesuai dengan pencarian
                        @else
                            Tidak ada kategori laboratorium yang tersedia
                        @endif
                    </p>
                </div>
            @endif

            <!-- Pagination Controls -->
            @if($templates->hasPages())
            <div class="mt-4 flex justify-between items-center">
                <div class="flex gap-2">
                    <button
                        wire:click="previousPage('templates')"
                        @if($templates->currentPage() == 1) disabled @endif
                        class="px-3 py-2 text-sm rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        x-bind:class="darkMode ? 'bg-gray-700 hover:bg-gray-600 text-white border-gray-600' : 'bg-white hover:bg-gray-50 text-gray-700 border-gray-300'"
                    >
                        ← Sebelumnya
                    </button>
                    <button
                        wire:click="nextPage('templates')"
                        @if(!$templates->hasMorePages()) disabled @endif
                        class="px-3 py-2 text-sm rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        x-bind:class="darkMode ? 'bg-gray-700 hover:bg-gray-600 text-white border-gray-600' : 'bg-white hover:bg-gray-50 text-gray-700 border-gray-300'"
                    >
                        Selanjutnya →
                    </button>
                </div>
                <div class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                    Halaman {{ $templates->currentPage() }} dari {{ $templates->lastPage() }}
                    ({{ $templates->firstItem() }}-{{ $templates->lastItem() }} dari {{ $templates->total() }} total)
                </div>
            </div>
            @endif

            <!-- Info when no pagination needed -->
            @if(!$templates->hasPages())
            <div class="mt-4 text-center">
                <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                    @if($searchTemplate)
                        Menampilkan semua {{ $templates->total() }} hasil pencarian "{{ $searchTemplate }}"
                    @elseif($selectedKategori)
                        Menampilkan semua {{ $templates->total() }} template dalam kategori
                    @else
                        Menampilkan semua {{ $templates->total() }} template laboratorium
                    @endif
                </p>
            </div>
            @endif
        </div>

        <!-- Selected Items Preview -->
        @if(count($selectedPemeriksaan) > 0)
        <div class="mb-4 p-3 rounded-lg" x-bind:class="darkMode ? 'bg-gray-600' : 'bg-blue-50'">
            <h5 class="font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">
                Pemeriksaan Terpilih ({{ count($selectedPemeriksaan) }}):
            </h5>
            <div class="flex flex-wrap gap-2">
                @foreach($selectedPemeriksaan as $index => $pemeriksaan)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                          x-bind:class="darkMode ? 'bg-blue-900/50 text-blue-200' : 'bg-blue-100 text-blue-800'">
                        {{ $pemeriksaan['pemeriksaan'] }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Save Button -->
        @if(count($selectedPemeriksaan) > 0)
        <div class="flex gap-2">
            <button
                wire:click="simpanPermintaan"
                type="button"
                class="px-6 py-2 text-sm rounded-md transition-colors"
                x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-500 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'"
            >
                💾 {{ $isEditing ? 'Update' : 'Simpan' }} Permintaan
            </button>
        </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-md" x-bind:class="darkMode ? 'bg-green-900/50 text-green-200' : 'bg-green-50 text-green-800'">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-md" x-bind:class="darkMode ? 'bg-red-900/50 text-red-200' : 'bg-red-50 text-red-800'">
            {{ session('error') }}
        </div>
    @endif

    <!-- Existing Requests -->
    @if($permintaanLab->count() > 0)
    <div class="space-y-4">
        <h4 class="font-medium" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">
            Riwayat Permintaan Laboratorium:
        </h4>

        <div class="overflow-x-auto rounded-lg shadow" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">
            <table class="min-w-full divide-y" x-bind:class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
                <thead x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            No. Order
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Tanggal/Jam
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Dokter Perujuk
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Diagnosis
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Pemeriksaan
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y" x-bind:class="darkMode ? 'bg-gray-800 divide-gray-700' : 'bg-white divide-gray-200'">
                    @foreach($permintaanLab as $permintaan)
                    <tr class="transition-colors" x-bind:class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            {{ $permintaan->noorder }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            <div>{{ $permintaan->formatted_tgl_permintaan }}</div>
                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $permintaan->formatted_jam_permintaan }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            {{ $permintaan->dokter->nm_dokter ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            {{ $permintaan->diagnosa_klinis ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                  x-bind:class="darkMode ? 'bg-blue-900/50 text-blue-200' : 'bg-blue-100 text-blue-800'">
                                {{ $permintaan->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            <div class="max-w-xs">
                                @foreach($permintaan->detailPermintaan as $detail)
                                    <div class="text-xs mb-1">
                                        • {{ $detail->templateLaboratorium->Pemeriksaan ?? 'N/A' }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @endif
</div>