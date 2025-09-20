<div x-data="{
    darkMode: document.documentElement.classList.contains('dark'),
    activeTab: 'catatan-pasien'
}" class="p-6">
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                    📝 Catatan Pasien - {{ $noRawat }}
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
                <button @click="activeTab = 'catatan-pasien'"
                        class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                        x-bind:class="activeTab === 'catatan-pasien'
                            ? (darkMode ? 'border-blue-400 text-blue-400' : 'border-blue-500 text-blue-600')
                            : (darkMode ? 'border-transparent text-gray-400 hover:text-gray-300' : 'border-transparent text-gray-500 hover:text-gray-700')">
                    📝 Catatan Pasien
                    <span class="inline-flex items-center gap-x-1.5 py-0.5 px-2 ml-2 rounded-full text-xs font-medium"
                          x-bind:class="{{$existingCatatan ? 'true' : 'false'}}
                            ? (darkMode ? 'bg-green-800 text-green-300' : 'bg-green-100 text-green-700')
                            : (darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600')">
                        {{ $existingCatatan ? 'Ada' : 'Kosong' }}
                    </span>
                </button>
                <button @click="activeTab = 'catatan-medis'"
                        class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                        x-bind:class="activeTab === 'catatan-medis'
                            ? (darkMode ? 'border-blue-400 text-blue-400' : 'border-blue-500 text-blue-600')
                            : (darkMode ? 'border-transparent text-gray-400 hover:text-gray-300' : 'border-transparent text-gray-500 hover:text-gray-700')">
                    🩺 Catatan Medis Pasien ({{ count($existingCatatanMedis) }})
                </button>
            </nav>
        </div>

        {{-- Catatan Pasien Tab Content --}}
        <div x-show="activeTab === 'catatan-pasien'" class="space-y-6">
            {{-- Form Input Section --}}
            <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        {{ $existingCatatan ? '✏️ Edit Catatan' : '📝 Tambah Catatan' }}
                    </h3>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Catatan Textarea -->
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Catatan Pasien
                        </label>
                        <textarea wire:model="catatan"
                                  rows="6"
                                  placeholder="Masukkan catatan untuk pasien ini..."
                                  class="w-full px-3 py-2 border rounded-md text-sm resize-none transition-colors"
                                  x-bind:class="darkMode
                                    ? 'bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500'">{{ $existingCatatan ? $existingCatatan->catatan : '' }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button wire:click="saveCatatan"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                x-bind:class="darkMode
                                    ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                    : 'bg-blue-600 hover:bg-blue-700 text-white'">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            {{ $existingCatatan ? 'Update Catatan' : 'Simpan Catatan' }}
                        </button>

                        @if($existingCatatan)
                            <button wire:click="deleteCatatan"
                                    wire:confirm="Apakah Anda yakin ingin menghapus catatan ini?"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                    x-bind:class="darkMode
                                        ? 'bg-red-600 hover:bg-red-700 text-white'
                                        : 'bg-red-600 hover:bg-red-700 text-white'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Catatan
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Existing Catatan Display --}}
            @if($existingCatatan)
                <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold flex items-center gap-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            <svg class="w-4 h-4" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                            📋 Catatan Saat Ini
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="p-4 rounded-lg border" x-bind:class="darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'">
                            <p class="text-sm whitespace-pre-wrap leading-relaxed" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $existingCatatan->catatan }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Catatan Medis Pasien Tab Content --}}
        <div x-show="activeTab === 'catatan-medis'" class="space-y-6">
            {{-- Form Input Section --}}
            <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        🩺 Tambah Catatan Medis
                    </h3>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Form Fields Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                                Tanggal
                            </label>
                            <input type="date" wire:model="tanggal"
                                   class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                                   x-bind:class="darkMode
                                     ? 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                     : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                        </div>

                        <!-- Jam -->
                        <div>
                            <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                                Jam
                            </label>
                            <input type="time" wire:model="jam" step="1"
                                   class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                                   x-bind:class="darkMode
                                     ? 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                     : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                        </div>

                        <!-- Petugas -->
                        <div>
                            <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                                Petugas
                            </label>
                            @if($isAdmin)
                                <select wire:model="nip"
                                        class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                                        x-bind:class="darkMode
                                          ? 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                          : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                                    <option value="">Pilih Petugas</option>
                                    @foreach($petugasList as $nipPetugas => $namaPetugas)
                                        <option value="{{ $nipPetugas }}">{{ $namaPetugas }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" value="{{ auth()->user()->pegawai->nama ?? auth()->user()->name ?? 'User' }}" readonly
                                       class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                                       x-bind:class="darkMode
                                         ? 'bg-gray-600 border-gray-600 text-gray-300 cursor-not-allowed'
                                         : 'bg-gray-100 border-gray-300 text-gray-600 cursor-not-allowed'">
                                <input type="hidden" wire:model="nip">
                            @endif
                        </div>
                    </div>

                    <!-- Catatan Medis Textarea -->
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Catatan Medis
                        </label>
                        <textarea wire:model="catatanMedis"
                                  rows="4"
                                  placeholder="Masukkan catatan medis untuk pasien ini..."
                                  class="w-full px-3 py-2 border rounded-md text-sm resize-none transition-colors"
                                  x-bind:class="darkMode
                                    ? 'bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500'"></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button wire:click="saveCatatanMedis"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                x-bind:class="darkMode
                                    ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                    : 'bg-blue-600 hover:bg-blue-700 text-white'">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Simpan Catatan Medis
                        </button>

                        @if(count($existingCatatanMedis) > 0)
                            <button wire:click="resetAllCatatanMedis"
                                    wire:confirm="Apakah Anda yakin ingin menghapus semua catatan medis?"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors"
                                    x-bind:class="darkMode
                                        ? 'bg-red-600 hover:bg-red-700 text-white'
                                        : 'bg-red-600 hover:bg-red-700 text-white'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Semua
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Existing Catatan Medis List --}}
            @if(count($existingCatatanMedis) > 0)
                <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                        <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            📋 Riwayat Catatan Medis ({{ count($existingCatatanMedis) }})
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        @foreach($existingCatatanMedis as $index => $catatan)
                            <div class="p-4 rounded-lg border" x-bind:class="darkMode ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <!-- Header -->
                                        <div class="flex items-center gap-4 mb-2">
                                            <span class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                                                📅 {{ \Carbon\Carbon::parse($catatan['tanggal'])->format('d/m/Y') }}
                                                🕒 {{ $catatan['jam'] }}
                                            </span>
                                            <span class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                                👤 {{ $catatan['petugas_name'] }}
                                            </span>
                                        </div>

                                        <!-- Catatan Content -->
                                        <div class="mt-2">
                                            <p class="text-sm whitespace-pre-wrap leading-relaxed" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $catatan['catatan'] }}</p>
                                        </div>
                                    </div>

                                    <!-- Delete Button -->
                                    <div class="ml-4">
                                        <button wire:click="deleteCatatanMedis({{ $index }})"
                                                wire:confirm="Apakah Anda yakin ingin menghapus catatan medis ini?"
                                                class="p-2 rounded-md transition-colors"
                                                x-bind:class="darkMode
                                                    ? 'text-red-400 hover:text-red-200 hover:bg-red-900'
                                                    : 'text-red-600 hover:text-red-800 hover:bg-red-50'"
                                                title="Hapus catatan medis">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="p-8 text-center">
                        <div class="mb-4" x-bind:class="darkMode ? 'text-gray-500' : 'text-gray-400'">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada catatan medis untuk pasien ini
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>