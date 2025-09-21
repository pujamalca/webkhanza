<div x-data="{
    darkMode: document.documentElement.classList.contains('dark')
}" class="p-6">
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                    💊 Resep Obat - {{ $noRawat }}
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

        {{-- Form Resep --}}
        <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                    {{ $isEditing ? '📝 Edit Resep: ' . $noResep : '➕ Buat Resep Baru' }}
                </h3>
            </div>

            <div class="p-4 space-y-4">
                {{-- First Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- No Resep --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            No. Resep
                        </label>
                        <div class="flex">
                            <input type="text"
                                   wire:model="noResep"
                                   class="flex-1 px-3 py-2 border rounded-l-md text-sm transition-colors"
                                   x-bind:class="darkMode
                                     ? (@if(!$editNoResep) 'bg-gray-600 border-gray-600 text-gray-100' @else 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500' @endif)
                                     : (@if(!$editNoResep) 'bg-gray-100 border-gray-300 text-gray-900' @else 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500' @endif)"
                                   @if(!$editNoResep) readonly @endif>
                            <button type="button"
                                    wire:click="toggleEditNoResep"
                                    class="px-3 py-2 rounded-r-md border border-l-0 text-sm transition-colors"
                                    x-bind:class="darkMode
                                      ? 'border-gray-600 bg-gray-600 hover:bg-gray-500 text-gray-300'
                                      : 'border-gray-300 bg-gray-50 hover:bg-gray-100 text-gray-700'">
                                @if($editNoResep)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                @endif
                            </button>
                        </div>
                        @if($editNoResep)
                            <p class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">Format: YYYYMMDD0001</p>
                        @endif
                    </div>

                    {{-- Dokter --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Dokter
                        </label>
                        <select wire:model="kdDokter"
                                class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                                x-bind:class="darkMode
                                  ? 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                  : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                            <option value="">Pilih Dokter</option>
                            @foreach($dokterList as $kode => $nama)
                                <option value="{{ $kode }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Second Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Tanggal Peresepan --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Tanggal Peresepan
                        </label>
                        <input type="date"
                               wire:model="tglPeresepan"
                               class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                               x-bind:class="darkMode
                                 ? 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                 : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                    </div>

                    {{-- Jam Peresepan --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Jam Peresepan
                        </label>
                        <input type="time"
                               wire:model="jamPeresepan"
                               step="1"
                               class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                               x-bind:class="darkMode
                                 ? 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                 : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                    </div>
                </div>

                {{-- Hidden Status Field --}}
                <input type="hidden" wire:model="status" value="ralan">

                {{-- Search Obat --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Cari dan Tambah Obat
                        </label>
                        <div class="flex gap-2">
                            <button type="button"
                                    wire:click="openTemplateModal"
                                    class="px-3 py-1 text-xs rounded-md transition-colors inline-flex items-center gap-1"
                                    x-bind:class="darkMode
                                      ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                      : 'bg-blue-500 hover:bg-blue-600 text-white'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Template
                            </button>
                            <button type="button"
                                    wire:click="openCreateTemplateModal"
                                    class="px-3 py-1 text-xs rounded-md transition-colors inline-flex items-center gap-1"
                                    x-bind:class="darkMode
                                      ? 'bg-green-600 hover:bg-green-700 text-white'
                                      : 'bg-green-500 hover:bg-green-600 text-white'">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Simpan Template
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="text"
                               wire:model.live.debounce.300ms="searchObat"
                               placeholder="Cari obat berdasarkan nama atau kode..."
                               class="w-full px-3 py-2 pr-10 border rounded-md text-sm transition-colors"
                               x-bind:class="darkMode
                                 ? 'bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                                 : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500'">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-4 h-4" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        {{-- Search Results --}}
                        @if($showSearchResults && !empty($searchResults))
                            <div class="absolute z-10 w-full mt-1 rounded-md shadow-lg max-h-96 overflow-y-auto"
                                 x-bind:class="darkMode ? 'bg-gray-800 border border-gray-600' : 'bg-white border border-gray-200'">
                                @foreach($searchResults as $obat)
                                    <div @if($obat['stok'] > 0) wire:click="addObatToTable('{{ $obat['kode_brng'] }}')" @endif
                                         class="p-3 border-b last:border-b-0 transition-colors @if($obat['stok'] > 0) cursor-pointer @else cursor-not-allowed opacity-60 @endif"
                                         x-bind:class="darkMode
                                           ? (@if($obat['stok'] > 0) 'hover:bg-gray-700' @else 'bg-red-900/20' @endif) + ' border-gray-600'
                                           : (@if($obat['stok'] > 0) 'hover:bg-gray-50' @else 'bg-red-50' @endif) + ' border-gray-200'">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="font-medium" x-bind:class="darkMode ? (@if($obat['stok'] > 0) 'text-gray-100' @else 'text-red-300' @endif) : (@if($obat['stok'] > 0) 'text-gray-900' @else 'text-red-600' @endif)">
                                                    {{ $obat['display_name'] }}
                                                    @if($obat['stok'] <= 0)
                                                        <span class="text-xs font-normal @if($obat['stok'] <= 0) text-red-500 @endif">(Stok Habis)</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm grid grid-cols-2 gap-2 mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                                    <div>Satuan: {{ $obat['satuan'] }}</div>
                                                    <div>Jenis: {{ $obat['jenis'] }}</div>
                                                    <div>Harga: {{ $obat['formatted_harga'] }}</div>
                                                    <div class="@if($obat['stok'] <= 0) text-red-500 font-semibold @endif">Stok: {{ $obat['formatted_stok'] }}</div>
                                                </div>
                                                @if($obat['komposisi'] !== '-')
                                                    <div class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                        Komposisi: {{ $obat['komposisi'] }}
                                                    </div>
                                                @endif
                                                <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                    Industri: {{ $obat['industri'] }}
                                                </div>
                                            </div>
                                            @if($obat['stok'] > 0)
                                                <button class="ml-2 transition-colors" x-bind:class="darkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-800'">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                </button>
                                            @else
                                                <div class="ml-2" x-bind:class="darkMode ? 'text-red-400' : 'text-red-500'">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Table Obat --}}
                @if(!empty($obatTable))
                    <div>
                        <h3 class="text-lg font-medium mb-3" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Daftar Obat Resep</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y" x-bind:class="darkMode ? 'divide-gray-600' : 'divide-gray-200'">
                                <thead x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Obat</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Info</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Stok</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Harga</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Jumlah</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Aturan Pakai</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Subtotal</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" x-bind:class="darkMode ? 'bg-gray-800 divide-gray-600' : 'bg-white divide-gray-200'">
                                    @foreach($obatTable as $index => $obat)
                                        <tr>
                                            <td class="px-3 py-4">
                                                <div class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                    {{ $obat['nama_brng'] }}
                                                </div>
                                                <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                    {{ $obat['kode_brng'] }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-4">
                                                <div class="text-xs space-y-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                                    <div>{{ $obat['satuan'] }}</div>
                                                    <div>{{ $obat['jenis'] }}</div>
                                                    @if($obat['komposisi'] !== '-')
                                                        <div class="text-xs">{{ $obat['komposisi'] }}</div>
                                                    @endif
                                                    <div class="text-xs">{{ $obat['industri'] }}</div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                {{ $obat['formatted_stok'] }}
                                            </td>
                                            <td class="px-3 py-4 text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                {{ $obat['formatted_harga'] }}
                                            </td>
                                            <td class="px-3 py-4">
                                                <input type="number"
                                                       wire:model.live="obatTable.{{ $index }}.jumlah"
                                                       min="1"
                                                       step="0.01"
                                                       class="w-20 px-2 py-1 border rounded-md text-sm transition-colors"
                                                       x-bind:class="darkMode
                                                         ? 'border-gray-600 bg-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                                         : 'border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                                            </td>
                                            <td class="px-3 py-4" x-data="{
                                                showSuggestions: false,
                                                suggestions: [],
                                                searchTerm: @js($obat['aturan_pakai'] ?? ''),
                                                async getSuggestions() {
                                                    if (this.searchTerm.length >= 2) {
                                                        this.suggestions = await $wire.searchAturanPakai(this.searchTerm);
                                                        this.showSuggestions = this.suggestions.length > 0;
                                                    } else {
                                                        this.suggestions = [];
                                                        this.showSuggestions = false;
                                                    }
                                                },
                                                selectSuggestion(suggestion) {
                                                    this.searchTerm = suggestion;
                                                    $wire.set('obatTable.{{ $index }}.aturan_pakai', suggestion);
                                                    this.showSuggestions = false;
                                                }
                                            }" class="relative">
                                                <input type="text"
                                                       x-model="searchTerm"
                                                       @input="getSuggestions(); $wire.set('obatTable.{{ $index }}.aturan_pakai', searchTerm)"
                                                       @focus="if (searchTerm.length >= 2) getSuggestions()"
                                                       @blur="setTimeout(() => showSuggestions = false, 200)"
                                                       placeholder="3x1 sesudah makan"
                                                       class="w-full px-2 py-1 border rounded-md text-sm transition-colors"
                                                       x-bind:class="darkMode
                                                         ? 'border-gray-600 bg-gray-700 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                                                         : 'border-gray-300 bg-white text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500'">

                                                <!-- Suggestions dropdown -->
                                                <div x-show="showSuggestions"
                                                     class="absolute z-50 w-full mt-1 rounded-md shadow-lg max-h-40 overflow-y-auto"
                                                     x-bind:class="darkMode ? 'bg-gray-800 border border-gray-600' : 'bg-white border border-gray-200'"
                                                     style="display: none;">
                                                    <template x-for="suggestion in suggestions" :key="suggestion">
                                                        <div @click="selectSuggestion(suggestion)"
                                                             class="px-3 py-2 cursor-pointer text-sm transition-colors"
                                                             x-bind:class="darkMode
                                                               ? 'hover:bg-gray-700 text-gray-100'
                                                               : 'hover:bg-gray-100 text-gray-900'"
                                                             x-text="suggestion">
                                                        </div>
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 text-sm font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                Rp {{ number_format($obat['subtotal'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-4">
                                                <button wire:click="removeObatFromTable({{ $index }})"
                                                        class="transition-colors" x-bind:class="darkMode ? 'text-red-400 hover:text-red-300' : 'text-red-600 hover:text-red-800'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                                    <tr>
                                        <td colspan="6" class="px-3 py-3 text-right font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            Total Harga:
                                        </td>
                                        <td class="px-3 py-3 font-bold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            Rp {{ number_format($this->getTotalHargaResep(), 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex justify-between items-center pt-4 border-t" x-bind:class="darkMode ? 'border-gray-600' : 'border-gray-200'">
                    <div class="flex space-x-2">
                        @if($isEditing)
                            <button wire:click="batalEdit"
                                    class="px-4 py-2 text-sm font-medium border rounded-md transition-colors"
                                    x-bind:class="darkMode
                                        ? 'text-gray-300 bg-gray-700 border-gray-600 hover:bg-gray-600'
                                        : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'">
                                Batal Edit
                            </button>
                        @else
                            <button wire:click="buatResepBaru"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium border rounded-md transition-colors"
                                    x-bind:class="darkMode
                                        ? 'text-gray-300 bg-gray-700 border-gray-600 hover:bg-gray-600'
                                        : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Resep Baru
                            </button>
                        @endif
                    </div>

                    <button wire:click="simpanResep"
                            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white rounded-md transition-colors"
                            x-bind:class="darkMode
                                ? 'bg-blue-500 hover:bg-blue-600 disabled:opacity-50'
                                : 'bg-blue-600 hover:bg-blue-700 disabled:opacity-50'"
                            @disabled(empty($obatTable))>
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $isEditing ? 'Update Resep' : 'Simpan Resep' }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Existing Resep --}}
        @if(!empty($existingResep))
            <div class="rounded-lg shadow-sm" x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                <div class="px-6 py-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <h3 class="text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Resep Obat Terdahulu</h3>
                </div>
                <div class="divide-y" x-bind:class="darkMode ? 'divide-gray-600' : 'divide-gray-200'">
                    @foreach($existingResep as $resep)
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                        Resep #{{ $resep['no_resep'] }}
                                    </div>
                                    <div class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                        {{ $resep['formatted_tgl_peresepan'] }} |
                                        Dr. {{ $resep['dokter']['nm_dokter'] ?? '' }} |
                                        Status: {{ ucfirst($resep['status']) }}
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="lihatDetailResep('{{ $resep['no_resep'] }}')"
                                            class="text-sm transition-colors" x-bind:class="darkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-800'">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </button>
                                    <button wire:click="editResep('{{ $resep['no_resep'] }}')"
                                            class="text-sm transition-colors" x-bind:class="darkMode ? 'text-green-400 hover:text-green-300' : 'text-green-600 hover:text-green-800'">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="duplicateResep('{{ $resep['no_resep'] }}')"
                                            class="text-sm transition-colors" x-bind:class="darkMode ? 'text-purple-400 hover:text-purple-300' : 'text-purple-600 hover:text-purple-800'">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        Duplikasi
                                    </button>
                                    <button wire:click="hapusResep('{{ $resep['no_resep'] }}')"
                                            wire:confirm="Apakah Anda yakin ingin menghapus resep ini?"
                                            class="text-sm transition-colors" x-bind:class="darkMode ? 'text-red-400 hover:text-red-300' : 'text-red-600 hover:text-red-800'">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            {{-- Detail Resep (expandable) --}}
                            @if(!empty($detailResep) && collect($detailResep)->isNotEmpty())
                                <div class="mt-4 border-t pt-4" x-bind:class="darkMode ? 'border-gray-600' : 'border-gray-200'">
                                    <h4 class="font-medium mb-3" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Detail Obat:</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y" x-bind:class="darkMode ? 'divide-gray-600' : 'divide-gray-200'">
                                            <thead x-bind:class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Obat</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Info</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Jumlah</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Aturan Pakai</th>
                                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y" x-bind:class="darkMode ? 'bg-gray-800 divide-gray-600' : 'bg-white divide-gray-200'">
                                                @foreach($detailResep as $detail)
                                                    <tr>
                                                        <td class="px-3 py-2 text-sm">
                                                            <div class="font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $detail['nama_brng'] }}</div>
                                                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $detail['kode_brng'] }}</div>
                                                        </td>
                                                        <td class="px-3 py-2 text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                                            <div>{{ $detail['satuan'] }} | {{ $detail['jenis'] }}</div>
                                                            <div>{{ $detail['industri'] }}</div>
                                                            @if($detail['komposisi'] !== '-')
                                                                <div>{{ $detail['komposisi'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $detail['jumlah'] }}</td>
                                                        <td class="px-3 py-2 text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $detail['aturan_pakai'] }}</td>
                                                        <td class="px-3 py-2 text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $detail['formatted_harga'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Template Modal --}}
    @if($showTemplateModal)
    <div class="fixed inset-0 z-50 overflow-hidden">
        {{-- Background overlay --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeTemplateModal"></div>

        {{-- Modal panel - Fullscreen --}}
        <div class="relative w-full h-full flex flex-col" x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'">
            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div>
                    <h2 class="text-xl font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        📋 Pilih Template Resep
                    </h2>
                    <p class="text-sm mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                        Pilih template resep untuk mengisi form secara otomatis
                    </p>
                </div>
                <button type="button"
                        wire:click="closeTemplateModal"
                        class="p-2 rounded-md transition-colors"
                        x-bind:class="darkMode
                          ? 'text-gray-400 hover:text-gray-200 hover:bg-gray-700'
                          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($resepTemplates as $template)
                        <div class="border rounded-lg p-4 transition-colors hover:shadow-lg"
                             x-bind:class="darkMode
                               ? 'border-gray-600 bg-gray-700 hover:bg-gray-600'
                               : 'border-gray-200 bg-white hover:bg-gray-50'">
                            {{-- Template Header --}}
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                        {{ $template['nama_template'] }}
                                    </h3>
                                    @if($template['keterangan'])
                                        <p class="text-sm mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                            {{ $template['keterangan'] }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 ml-3">
                                    <span class="px-2 py-1 rounded text-xs"
                                          x-bind:class="darkMode
                                            ? (@if($template['is_public']) 'bg-green-800 text-green-200' @else 'bg-blue-800 text-blue-200' @endif)
                                            : (@if($template['is_public']) 'bg-green-100 text-green-800' @else 'bg-blue-100 text-blue-800' @endif)">
                                        {{ $template['is_public'] ? 'Public' : 'Private' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Template Details --}}
                            @if(isset($template['resep_template_detail']) && count($template['resep_template_detail']) > 0)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                                        Daftar Obat ({{ count($template['resep_template_detail']) }} item):
                                    </h4>
                                    <div class="space-y-2 max-h-48 overflow-y-auto" x-bind:class="darkMode ? 'scrollbar-dark' : 'scrollbar-light'">
                                        @foreach($template['resep_template_detail'] as $detail)
                                            <div class="p-2 rounded border text-sm" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-200 bg-gray-50'">
                                                <div class="font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                    {{ $detail['databarang']['nama_brng'] ?? 'Obat tidak ditemukan' }}
                                                </div>
                                                <div class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                                    Kode: {{ $detail['kode_brng'] }} |
                                                    Jumlah: {{ $detail['jumlah'] }} {{ $detail['databarang']['satuan_kecil']['satuan'] ?? '' }}
                                                </div>
                                                <div class="text-xs mt-1 font-medium" x-bind:class="darkMode ? 'text-blue-300' : 'text-blue-600'">
                                                    Aturan: {{ $detail['aturan_pakai'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mb-4 p-3 rounded border text-center text-sm" x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-gray-400' : 'border-gray-200 bg-gray-50 text-gray-500'">
                                    Belum ada detail obat
                                </div>
                            @endif

                            {{-- Template Actions --}}
                            <div class="flex gap-2">
                                <button type="button"
                                        wire:click="useTemplate({{ $template['id'] }})"
                                        class="flex-1 px-4 py-2 rounded-md font-medium transition-colors"
                                        x-bind:class="darkMode
                                          ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                          : 'bg-blue-500 hover:bg-blue-600 text-white'">
                                    Gunakan Template
                                </button>
                                @if(!$template['is_public'] || Auth::user()?->username === 'admin')
                                    <button type="button"
                                            wire:click="deleteTemplate({{ $template['id'] }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus template '{{ $template['nama_template'] }}'?"
                                            class="px-3 py-2 rounded-md transition-colors"
                                            x-bind:class="darkMode
                                              ? 'bg-red-600 hover:bg-red-700 text-white'
                                              : 'bg-red-500 hover:bg-red-600 text-white'"
                                            title="Hapus Template">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4" x-bind:class="darkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                Belum Ada Template
                            </h3>
                            <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                Belum ada template resep tersedia. Silakan hubungi administrator untuk menambahkan template.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Footer --}}
            <div class="border-t p-4" x-bind:class="darkMode ? 'border-gray-700 bg-gray-750' : 'border-gray-200 bg-gray-50'">
                <div class="flex justify-end">
                    <button type="button"
                            wire:click="closeTemplateModal"
                            class="px-6 py-2 rounded-md border font-medium transition-colors"
                            x-bind:class="darkMode
                              ? 'border-gray-600 bg-gray-700 text-gray-200 hover:bg-gray-600'
                              : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Create Template Modal --}}
    @if($showCreateTemplateModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            {{-- Background overlay --}}
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeCreateTemplateModal"></div>

            {{-- Modal panel --}}
            <div class="relative inline-block align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 x-bind:class="darkMode ? 'bg-gray-800' : 'bg-white'"
                 @click.stop>

                {{-- Header --}}
                <div class="px-6 pt-6 pb-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            💾 Simpan sebagai Template
                        </h3>
                        <button type="button"
                                wire:click="closeCreateTemplateModal"
                                class="p-1 rounded-md transition-colors"
                                x-bind:class="darkMode
                                  ? 'text-gray-400 hover:text-gray-200 hover:bg-gray-700'
                                  : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                        Simpan {{ count($obatTable) }} obat yang sudah ditambahkan sebagai template resep
                    </p>
                </div>

                {{-- Content --}}
                <div class="px-6 py-4 space-y-4">
                    {{-- Nama Template --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Nama Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="namaTemplate"
                               placeholder="Contoh: Resep Flu & Batuk"
                               class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                               x-bind:class="darkMode
                                 ? 'bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                                 : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500'">
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Keterangan (Opsional)
                        </label>
                        <textarea wire:model="keteranganTemplate"
                                  placeholder="Deskripsi singkat untuk template ini..."
                                  rows="3"
                                  class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                                  x-bind:class="darkMode
                                    ? 'bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500'
                                    : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500'"></textarea>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Kategori
                        </label>
                        <select wire:model="kategoriTemplate"
                                class="w-full px-3 py-2 border rounded-md text-sm transition-colors"
                                x-bind:class="darkMode
                                  ? 'bg-gray-700 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500'
                                  : 'bg-white border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500'">
                            <option value="">Pilih Kategori</option>
                            <option value="umum">Umum</option>
                            <option value="kardiovaskular">Kardiovaskular</option>
                            <option value="respirasi">Respirasi</option>
                            <option value="gastrointestinal">Gastrointestinal</option>
                            <option value="endokrin">Endokrin</option>
                            <option value="neurologi">Neurologi</option>
                            <option value="kulit">Kulit</option>
                            <option value="mata">Mata</option>
                            <option value="tht">THT</option>
                            <option value="pediatri">Pediatri</option>
                            <option value="ginekologi">Ginekologi</option>
                            <option value="antibiotik">Antibiotik</option>
                            <option value="analgesik">Analgesik</option>
                        </select>
                    </div>

                    {{-- Public Template Checkbox --}}
                    <div class="flex items-center">
                        <input type="checkbox"
                               wire:model="isPublicTemplate"
                               id="isPublicTemplate"
                               class="h-4 w-4 rounded transition-colors"
                               x-bind:class="darkMode
                                 ? 'bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500'
                                 : 'bg-white border-gray-300 text-blue-600 focus:ring-blue-500'">
                        <label for="isPublicTemplate" class="ml-2 block text-sm" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Template Public (dapat digunakan oleh semua user)
                        </label>
                    </div>

                    {{-- Preview Obat --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-700'">
                            Preview Obat ({{ count($obatTable) }} item)
                        </label>
                        <div class="max-h-40 overflow-y-auto border rounded-md" x-bind:class="darkMode ? 'border-gray-600' : 'border-gray-300'">
                            @foreach($obatTable as $index => $obat)
                                <div class="p-2 border-b last:border-b-0 text-xs" x-bind:class="darkMode ? 'border-gray-600' : 'border-gray-200'">
                                    <div class="font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                        {{ $obat['nama_brng'] }}
                                    </div>
                                    <div x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                                        Jumlah: {{ $obat['jumlah'] }} | Aturan: {{ $obat['aturan_pakai'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t flex justify-end gap-3" x-bind:class="darkMode ? 'border-gray-700 bg-gray-750' : 'border-gray-200 bg-gray-50'">
                    <button type="button"
                            wire:click="closeCreateTemplateModal"
                            class="px-4 py-2 rounded-md border font-medium transition-colors"
                            x-bind:class="darkMode
                              ? 'border-gray-600 bg-gray-700 text-gray-200 hover:bg-gray-600'
                              : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="saveTemplate"
                            class="px-4 py-2 rounded-md font-medium transition-colors"
                            x-bind:class="darkMode
                              ? 'bg-green-600 hover:bg-green-700 text-white'
                              : 'bg-green-500 hover:bg-green-600 text-white'">
                        Simpan Template
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>