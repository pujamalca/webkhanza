<div x-data="{
    darkMode: document.documentElement.classList.contains('dark')
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
            <div class="text-right">
                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium"
                      x-bind:class="{{$existingCatatan ? 'true' : 'false'}}
                        ? (darkMode ? 'bg-green-800 text-green-300' : 'bg-green-100 text-green-700')
                        : (darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600')">
                    <svg class="w-2.5 h-2.5" viewBox="0 0 20 20" fill="currentColor">
                        @if($existingCatatan)
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        @else
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        @endif
                    </svg>
                    {{ $existingCatatan ? 'Ada Catatan' : 'Belum Ada' }}
                </span>
            </div>
        </div>

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
</div>