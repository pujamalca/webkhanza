<div x-data="{
    darkMode: document.documentElement.classList.contains('dark'),
    showInputForm: true
}" class="space-y-6" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">

    <!-- Header Info -->
    <div class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
        <h3 class="text-md font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
            🧪 Pemeriksaan Laboratorium
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
            @click="showInputForm = !showInputForm"
            class="px-4 py-2 text-sm rounded-md transition-colors"
            x-bind:class="darkMode ? 'bg-gray-600 hover:bg-gray-500 text-white' : 'bg-gray-500 hover:bg-gray-600 text-white'"
        >
            <span x-show="!showInputForm">📋 Tampilkan Form</span>
            <span x-show="showInputForm">🔼 Sembunyikan Form</span>
        </button>

        @if($isEditing)
        <button
            wire:click="hapusPemeriksaan"
            onclick="return confirm('Yakin ingin menghapus pemeriksaan laboratorium ini?')"
            class="px-4 py-2 text-sm rounded-md transition-colors"
            x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-500 text-white' : 'bg-red-500 hover:bg-red-600 text-white'"
        >
            🗑️ Hapus Pemeriksaan
        </button>
        @endif
    </div>

    <!-- Input Form -->
    <div x-show="showInputForm" x-transition class="p-4 rounded-lg" x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Tanggal Periksa
                </label>
                <input
                    type="date"
                    wire:model="tanggalPeriksa"
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Jam Periksa
                </label>
                <input
                    type="time"
                    wire:model="jamPeriksa"
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
                    <option value="Ralan">Rawat Jalan</option>
                    <option value="Ranap">Rawat Inap</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Jenis Perawatan
                </label>
                <select
                    wire:model="kdJenisPrw"
                    class="w-full px-3 py-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                    x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 text-white' : 'border-gray-300 bg-white text-gray-900'"
                >
                    <option value="">Pilih Jenis Perawatan</option>
                    @foreach($jenisPerawatan as $jenis)
                        <option value="{{ $jenis->kd_jenis_prw }}">{{ $jenis->nm_perawatan }}</option>
                    @endforeach
                </select>
            </div>

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
        </div>

        <!-- Add Template Button -->
        <div class="mb-4">
            <button
                wire:click="addTemplate"
                type="button"
                class="px-4 py-2 text-sm rounded-md transition-colors"
                x-bind:class="darkMode ? 'bg-green-600 hover:bg-green-500 text-white' : 'bg-green-500 hover:bg-green-600 text-white'"
                @if(!$kdJenisPrw) disabled @endif
            >
                📋 Tambah Template Pemeriksaan
            </button>
            @if(!$kdJenisPrw)
                <p class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                    Pilih jenis perawatan terlebih dahulu
                </p>
            @endif
        </div>

        <!-- Template Items -->
        @if(count($templateItems) > 0)
        <div class="space-y-3 mb-4">
            <h4 class="font-medium" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">
                Daftar Pemeriksaan:
            </h4>
            @foreach($templateItems as $index => $item)
            <div class="p-3 border rounded-lg" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-300 bg-white'">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h5 class="font-medium" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">
                            {{ $item['pemeriksaan'] }}
                        </h5>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                            Satuan: {{ $item['satuan'] }} | Rujukan: {{ $item['nilai_rujukan'] }}
                        </p>
                    </div>
                    <button
                        wire:click="removeTemplate({{ $index }})"
                        type="button"
                        class="px-2 py-1 text-xs rounded transition-colors"
                        x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-500 text-white' : 'bg-red-500 hover:bg-red-600 text-white'"
                    >
                        ❌
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                            Nilai
                        </label>
                        <input
                            type="text"
                            wire:model="templateItems.{{ $index }}.nilai"
                            placeholder="Masukkan nilai hasil"
                            class="w-full px-2 py-1 text-sm border rounded focus:ring-blue-500 focus:border-blue-500"
                            x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white' : 'border-gray-300 bg-white text-gray-900'"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                            Keterangan
                        </label>
                        <input
                            type="text"
                            wire:model="templateItems.{{ $index }}.keterangan"
                            placeholder="Keterangan tambahan"
                            class="w-full px-2 py-1 text-sm border rounded focus:ring-blue-500 focus:border-blue-500"
                            x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white' : 'border-gray-300 bg-white text-gray-900'"
                        >
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Save Button -->
        @if(count($templateItems) > 0)
        <div class="flex gap-2">
            <button
                wire:click="simpanPemeriksaan"
                type="button"
                class="px-6 py-2 text-sm rounded-md transition-colors"
                x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-500 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'"
            >
                💾 {{ $isEditing ? 'Update' : 'Simpan' }} Pemeriksaan
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

    <!-- Existing Results -->
    @if($hasilLab->count() > 0)
    <div class="space-y-4">
        <h4 class="font-medium" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">
            Hasil Pemeriksaan Sebelumnya:
        </h4>

        <div class="overflow-x-auto rounded-lg shadow" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">
            <table class="min-w-full divide-y" x-bind:class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
                <thead x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Tanggal/Jam
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Pemeriksaan
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Nilai
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y" x-bind:class="darkMode ? 'bg-gray-800 divide-gray-700' : 'bg-white divide-gray-200'">
                    @foreach($hasilLab as $hasil)
                    <tr class="transition-colors" x-bind:class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'">
                        <td class="px-4 py-3 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            <div>{{ $hasil->formatted_tgl_periksa }}</div>
                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $hasil->formatted_jam }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                            <div class="font-medium">{{ $hasil->templateLaboratorium->Pemeriksaan ?? '-' }}</div>
                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $hasil->jenisPerawatan->nm_perawatan ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span class="font-medium" x-bind:class="darkMode ? ({{ $hasil->is_abnormal ? 'true' : 'false' }} ? 'text-red-400' : 'text-white') : ({{ $hasil->is_abnormal ? 'true' : 'false' }} ? 'text-red-600' : 'text-gray-900')">
                                {{ $hasil->nilai ?: '-' }}
                            </span>
                            @if($hasil->templateLaboratorium->satuan)
                                <span class="text-xs ml-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                    {{ $hasil->templateLaboratorium->satuan }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($hasil->is_abnormal)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                      x-bind:class="darkMode ? 'bg-red-900/50 text-red-200' : 'bg-red-100 text-red-800'">
                                    {{ $hasil->status_normal }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                      x-bind:class="darkMode ? 'bg-green-900/50 text-green-200' : 'bg-green-100 text-green-800'">
                                    Normal
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($hasilLab->hasPages())
            <div class="mt-4">
                {{ $hasilLab->links() }}
            </div>
        @endif
    </div>
    @endif
</div>