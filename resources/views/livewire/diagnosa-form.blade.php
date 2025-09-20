<div x-data="{
    darkMode: document.documentElement.classList.contains('dark'),
    activeTab: 'diagnosa'
}" class="p-6">
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                    🩺 Diagnosa & Prosedur - {{ $noRawat }}
                </h2>
                @if($regPeriksa)
                <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                    Pasien: {{ $regPeriksa->pasien->nm_pasien ?? 'N/A' }} |
                    No. RM: {{ $regPeriksa->no_rkm_medis }} |
                    Poli: {{ $regPeriksa->poliklinik->nm_poli ?? 'N/A' }} |
                    Cara Bayar: {{ $regPeriksa->penjab->png_jawab ?? 'N/A' }}
                </p>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
            <nav class="flex space-x-8">
                <button @click="activeTab = 'diagnosa'"
                        class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                        x-bind:class="activeTab === 'diagnosa'
                            ? (darkMode ? 'border-blue-400 text-blue-400' : 'border-blue-500 text-blue-600')
                            : (darkMode ? 'border-transparent text-gray-400 hover:text-gray-300' : 'border-transparent text-gray-500 hover:text-gray-700')">
                    🩺 Diagnosa Penyakit ({{ count($existingDiagnosa) }})
                </button>
                <button @click="activeTab = 'prosedur'"
                        class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                        x-bind:class="activeTab === 'prosedur'
                            ? (darkMode ? 'border-blue-400 text-blue-400' : 'border-blue-500 text-blue-600')
                            : (darkMode ? 'border-transparent text-gray-400 hover:text-gray-300' : 'border-transparent text-gray-500 hover:text-gray-700')">
                    🔧 Prosedur Medis ({{ count($existingProsedur) }})
                </button>
            </nav>
        </div>

        {{-- Diagnosa Tab Content --}}
        <div x-show="activeTab === 'diagnosa'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Panel: Pilih Diagnosa --}}
            <div class="space-y-6">
                {{-- Search Form --}}
                <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            🔍 Cari Diagnosa Penyakit
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                                Pencarian (kode atau nama penyakit)
                            </label>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   placeholder="Masukkan kode atau nama penyakit..."
                                   class="w-full px-3 py-2 rounded-lg border transition-colors focus:ring-2 focus:ring-blue-500"
                                   x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'">
                        </div>

                        @if(count($selectedDiagnosa) > 0)
                        <div class="text-center">
                            <button wire:click="resetSelectedDiagnosa"
                                    class="px-4 py-2 rounded-lg font-medium transition-colors"
                                    x-bind:class="darkMode ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-yellow-500 hover:bg-yellow-600 text-white'">
                                🔄 Reset Pilihan ({{ count($selectedDiagnosa) }})
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Disease List --}}
                <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            📋 Daftar Penyakit
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="max-h-64 overflow-y-auto space-y-2">
                            @forelse($penyakitList as $penyakit)
                            <div class="flex items-start space-x-3 p-3 rounded-lg cursor-pointer transition-colors"
                                 x-bind:class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
                                 wire:click="toggleDiagnosa('{{ $penyakit->kd_penyakit }}')">
                                <input type="checkbox"
                                       {{ in_array($penyakit->kd_penyakit, $selectedDiagnosa) ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                {{ $penyakit->kd_penyakit }}
                                            </p>
                                            <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                                {{ $penyakit->nm_penyakit }}
                                            </p>
                                            @if($penyakit->ciri_ciri)
                                            <p class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                {{ Str::limit($penyakit->ciri_ciri, 100) }}
                                            </p>
                                            @endif
                                            <div class="flex items-center gap-1 mt-1">
                                                @if(isset($penyakit->user_usage_count) && $penyakit->user_usage_count > 0)
                                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 font-medium">
                                                    👤 Anda: {{ $penyakit->user_usage_count }}x
                                                </span>
                                                @endif
                                                @if(isset($penyakit->total_usage_count) && $penyakit->total_usage_count > 0)
                                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">
                                                    🏥 Total: {{ $penyakit->total_usage_count }}x
                                                </span>
                                                @endif
                                                @if(isset($penyakit->user_usage_count) && $penyakit->user_usage_count > 0 || isset($penyakit->total_usage_count) && $penyakit->total_usage_count > 5)
                                                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 font-medium">
                                                    ⭐ Sering Digunakan
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded-full {{ $penyakit->status === 'Menular' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $penyakit->status === 'Menular' ? '⚠️ Menular' : '✅ Tidak Menular' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                <p>Tidak ada penyakit ditemukan</p>
                            </div>
                            @endforelse
                        </div>

                        @if($penyakitList->hasPages())
                        <div class="p-4 border-t" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                            {{ $penyakitList->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="flex space-x-3">
                    <button wire:click="addSelectedDiagnosa"
                            class="flex-1 px-4 py-2 rounded-lg font-medium transition-colors"
                            x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'">
                        💾 Simpan Diagnosa ({{ count($selectedDiagnosa) }})
                    </button>
                </div>
            </div>

            {{-- Right Panel: Existing Diagnosa --}}
            <div class="space-y-6">
                @if(count($existingDiagnosa) > 0)
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        📋 Diagnosa Tersimpan
                    </h2>
                    <button wire:click="resetAllDiagnosa"
                            wire:confirm="Apakah Anda yakin ingin menghapus SEMUA diagnosa yang sudah disimpan?"
                            class="px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2"
                            x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-red-500 hover:bg-red-600 text-white'">
                        🗑️ Hapus Semua
                    </button>
                </div>
                @endif

                @if(count($existingDiagnosa) > 0)
                <div class="rounded-lg shadow" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            🩺 Daftar Diagnosa ({{ count($existingDiagnosa) }})
                        </h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @foreach($existingDiagnosa as $diagnosa)
                        <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-xs px-2 py-1 rounded-full font-medium
                                            {{ $diagnosa['prioritas'] == 1 ? 'bg-red-100 text-red-800' :
                                               ($diagnosa['prioritas'] == 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $diagnosa['prioritas'] == 1 ? '🔴 Primer' :
                                               ($diagnosa['prioritas'] == 2 ? '🟡 Sekunder' : '🔵 Tersier') }}
                                        </span>
                                        <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-800">
                                            {{ $diagnosa['status_penyakit'] === 'Baru' ? '🆕 Baru' : '🔄 Lama' }}
                                        </span>
                                    </div>
                                    <p class="font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                        {{ $diagnosa['kd_penyakit'] }}
                                    </p>
                                    <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                        {{ $diagnosa['penyakit']['nm_penyakit'] ?? 'N/A' }}
                                    </p>
                                </div>
                                <button wire:click="deleteDiagnosa('{{ $diagnosa['kd_penyakit'] }}')"
                                        wire:confirm="Hapus diagnosa ini?"
                                        class="ml-3 p-2 rounded-lg transition-colors text-red-600 hover:bg-red-50"
                                        x-bind:class="darkMode ? 'hover:bg-red-900/50' : 'hover:bg-red-50'">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="rounded-lg shadow p-6 text-center" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="text-gray-400 mb-3">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-900'">
                        Belum ada diagnosa
                    </h3>
                    <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Pilih penyakit dari daftar untuk menambahkan diagnosa
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Prosedur Tab Content --}}
        <div x-show="activeTab === 'prosedur'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Panel: Pilih Prosedur --}}
            <div class="space-y-6">
                {{-- Search Form --}}
                <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            🔍 Cari Prosedur Medis
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                                Pencarian (kode atau deskripsi prosedur)
                            </label>
                            <input type="text" wire:model.live.debounce.300ms="searchProsedur"
                                   placeholder="Masukkan kode atau deskripsi prosedur..."
                                   class="w-full px-3 py-2 rounded-lg border transition-colors focus:ring-2 focus:ring-blue-500"
                                   x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'">
                        </div>

                        @if(count($selectedProsedur) > 0)
                        <div class="text-center">
                            <button wire:click="resetSelectedProsedur"
                                    class="px-4 py-2 rounded-lg font-medium transition-colors"
                                    x-bind:class="darkMode ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-yellow-500 hover:bg-yellow-600 text-white'">
                                🔄 Reset Pilihan ({{ count($selectedProsedur) }})
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Prosedur List --}}
                <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            🔧 Daftar Prosedur
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="max-h-64 overflow-y-auto space-y-2">
                            @forelse($prosedurList as $prosedur)
                            <div class="flex items-start space-x-3 p-3 rounded-lg cursor-pointer transition-colors"
                                 x-bind:class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-50'"
                                 wire:click="toggleProsedur('{{ $prosedur->kode }}')">
                                <input type="checkbox"
                                       {{ in_array($prosedur->kode, $selectedProsedur) ? 'checked' : '' }}
                                       class="mt-1 h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <div class="flex-1 min-w-0">
                                    <div>
                                        <p class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            {{ $prosedur->kode }}
                                        </p>
                                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                            {{ $prosedur->deskripsi_pendek }}
                                        </p>
                                        @if($prosedur->deskripsi_panjang && $prosedur->deskripsi_panjang !== $prosedur->deskripsi_pendek)
                                        <p class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                            {{ Str::limit($prosedur->deskripsi_panjang, 100) }}
                                        </p>
                                        @endif
                                        <div class="flex items-center gap-1 mt-1">
                                            @if(isset($prosedur->user_usage_count) && $prosedur->user_usage_count > 0)
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 font-medium">
                                                👤 Anda: {{ $prosedur->user_usage_count }}x
                                            </span>
                                            @endif
                                            @if(isset($prosedur->total_usage_count) && $prosedur->total_usage_count > 0)
                                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">
                                                🏥 Total: {{ $prosedur->total_usage_count }}x
                                            </span>
                                            @endif
                                            @if(isset($prosedur->user_usage_count) && $prosedur->user_usage_count > 0 || isset($prosedur->total_usage_count) && $prosedur->total_usage_count > 5)
                                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 font-medium">
                                                ⭐ Sering Digunakan
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                <p>Tidak ada prosedur ditemukan</p>
                            </div>
                            @endforelse
                        </div>

                        @if($prosedurList->hasPages())
                        <div class="p-4 border-t" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                            {{ $prosedurList->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="flex space-x-3">
                    <button wire:click="addSelectedProsedur"
                            class="flex-1 px-4 py-2 rounded-lg font-medium transition-colors"
                            x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'">
                        💾 Simpan Prosedur ({{ count($selectedProsedur) }})
                    </button>
                </div>
            </div>

            {{-- Right Panel: Existing Prosedur --}}
            <div class="space-y-6">
                @if(count($existingProsedur) > 0)
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        🔧 Prosedur Tersimpan
                    </h2>
                    <button wire:click="resetAllProsedur"
                            wire:confirm="Apakah Anda yakin ingin menghapus SEMUA prosedur yang sudah disimpan?"
                            class="px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2"
                            x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-red-500 hover:bg-red-600 text-white'">
                        🗑️ Hapus Semua
                    </button>
                </div>
                @endif

                @if(count($existingProsedur) > 0)
                <div class="rounded-lg shadow" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            🔧 Daftar Prosedur ({{ count($existingProsedur) }})
                        </h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @foreach($existingProsedur as $prosedur)
                        <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-xs px-2 py-1 rounded-full font-medium
                                            {{ $prosedur['prioritas'] == 1 ? 'bg-red-100 text-red-800' :
                                               ($prosedur['prioritas'] == 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ $prosedur['prioritas'] == 1 ? '🔴 Primer' :
                                               ($prosedur['prioritas'] == 2 ? '🟡 Sekunder' : '🔵 Tersier') }}
                                        </span>
                                    </div>
                                    <p class="font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                        {{ $prosedur['kode'] }}
                                    </p>
                                    <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                        {{ $prosedur['icd9']['deskripsi_pendek'] ?? 'N/A' }}
                                    </p>
                                </div>
                                <button wire:click="deleteProsedur('{{ $prosedur['kode'] }}')"
                                        wire:confirm="Hapus prosedur ini?"
                                        class="ml-3 p-2 rounded-lg transition-colors text-red-600 hover:bg-red-50"
                                        x-bind:class="darkMode ? 'hover:bg-red-900/50' : 'hover:bg-red-50'">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="rounded-lg shadow p-6 text-center" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="text-gray-400 mb-3">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-900'">
                        Belum ada prosedur
                    </h3>
                    <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                        Pilih prosedur dari daftar untuk menambahkan prosedur medis
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>