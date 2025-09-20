<div x-data="{
    darkMode: false,
    init() {
        this.darkMode = document.documentElement.classList.contains('dark');
        const observer = new MutationObserver(() => {
            setTimeout(() => {
                this.darkMode = document.documentElement.classList.contains('dark');
            }, 50);
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    }
}" x-init="init()">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <div class="min-h-screen py-4" x-bind:class="darkMode ? 'bg-gray-900' : 'bg-gray-50'">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Header Info --}}
            <div class="rounded-lg shadow p-4 mb-6"
                 x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                <h2 class="text-lg font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                    🩺 Input Tindakan - {{ $noRawat }}
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left Panel: Add Tindakan --}}
                <div class="space-y-6">

                    {{-- Form Input --}}
                    <div class="rounded-lg shadow p-4"
                         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <h3 class="text-md font-semibold mb-4" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            ➕ Tambah Tindakan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    Jenis Tindakan <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="selectedJenisTindakan" required
                                        x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'">
                                    <option value="dr">👨‍⚕️ Dokter</option>
                                    <option value="pr">👩‍⚕️ Petugas</option>
                                    <option value="drpr">🤝 Dokter + Petugas</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    Tanggal Perawatan <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="tglPerawatan" required
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            @if(in_array($selectedJenisTindakan, ['dr', 'drpr']))
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    Dokter <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="kdDokter" required
                                        x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'">
                                    <option value="">Pilih Dokter...</option>
                                    @foreach($dokterList as $kd => $nama)
                                        <option value="{{ $kd }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if(in_array($selectedJenisTindakan, ['pr', 'drpr']))
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    Petugas <span class="text-red-500">*</span>
                                </label>
                                @if($isAdmin)
                                    <select wire:model="nipPetugas" required
                                            x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'">
                                        <option value="">Pilih Petugas...</option>
                                        @foreach($petugasList as $nip => $nama)
                                            <option value="{{ $nip }}">{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" value="{{ auth()->user()->pegawai->nama ?? auth()->user()->name ?? 'User' }}" readonly
                                           x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-600 text-gray-300 cursor-not-allowed' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600 cursor-not-allowed'">
                                    <input type="hidden" wire:model="nipPetugas">
                                @endif
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    Jam Rawat <span class="text-red-500">*</span>
                                </label>
                                <input type="time" wire:model="jamRawat" required
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'" />
                            </div>
                        </div>

                        @if(count($selectedTindakan) > 0)
                        <div class="mb-4 p-3 rounded-md" x-bind:class="darkMode ? 'bg-blue-900/30 border border-blue-700' : 'bg-blue-50 border border-blue-200'">
                            <p class="text-sm font-medium mb-3" x-bind:class="darkMode ? 'text-blue-300' : 'text-blue-800'">
                                {{ count($selectedTindakan) }} tindakan dipilih
                            </p>
                            <div class="flex gap-2">
                                <button type="button" wire:click="addSelectedTindakan"
                                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    💾 Simpan Tindakan Terpilih
                                </button>
                                <button type="button" wire:click="resetSelectedTindakan"
                                        class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                    🔄 Reset
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Search and List Tindakan --}}
                    <div class="rounded-lg shadow"
                         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                            <h3 class="text-md font-semibold mb-3" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                🔍 Cari Tindakan
                                @if($selectedJenisTindakan === 'dr')
                                    <span class="text-blue-500">(👨‍⚕️ Dokter)</span>
                                @elseif($selectedJenisTindakan === 'pr')
                                    <span class="text-green-500">(👩‍⚕️ Petugas)</span>
                                @else
                                    <span class="text-purple-500">(🤝 Kolaborasi)</span>
                                @endif
                            </h3>
                            <input type="text" wire:model.live="search" placeholder="Cari berdasarkan kode atau nama tindakan..."
                                   x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 placeholder-gray-400' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-500'" />
                            <p class="text-xs mt-2" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                <span class="text-blue-600">⭐</span> = Yang sering Anda gunakan |
                                <span class="text-gray-600">📊</span> = Yang sering digunakan umumnya
                            </p>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse($jenisPerawatan as $tindakan)
                            <div class="p-3 border-b cursor-pointer hover:bg-opacity-50"
                                 x-bind:class="darkMode ? 'border-gray-700 hover:bg-gray-700' : 'border-gray-100 hover:bg-gray-50'"
                                 wire:click="toggleTindakan('{{ $tindakan->kd_jenis_prw }}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            @if(in_array($tindakan->kd_jenis_prw, $selectedTindakan))
                                                <span class="text-green-500">✅</span>
                                            @else
                                                <span class="text-gray-400">⬜</span>
                                            @endif
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                    {{ $tindakan->kd_jenis_prw }}
                                                </span>
                                                @if($tindakan->user_usage > 0)
                                                    <span class="px-1.5 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                        ⭐ {{ $tindakan->user_usage }}x
                                                    </span>
                                                @elseif($tindakan->total_usage > 0)
                                                    <span class="px-1.5 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">
                                                        📊 {{ $tindakan->total_usage }}x
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-sm mt-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                            {{ $tindakan->nm_perawatan }}
                                        </p>
                                        <div class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                            @if($selectedJenisTindakan === 'dr')
                                                <span class="text-blue-500">👨‍⚕️ Dokter:</span> Rp {{ number_format($tindakan->tarif_tindakandr, 0, ',', '.') }}
                                                @if($tindakan->material > 0 || $tindakan->bhp > 0)
                                                    <br><span>Material: Rp {{ number_format($tindakan->material, 0, ',', '.') }}, BHP: Rp {{ number_format($tindakan->bhp, 0, ',', '.') }}</span>
                                                @endif
                                            @elseif($selectedJenisTindakan === 'pr')
                                                <span class="text-green-500">👩‍⚕️ Petugas:</span> Rp {{ number_format($tindakan->tarif_tindakanpr, 0, ',', '.') }}
                                                @if($tindakan->material > 0 || $tindakan->bhp > 0)
                                                    <br><span>Material: Rp {{ number_format($tindakan->material, 0, ',', '.') }}, BHP: Rp {{ number_format($tindakan->bhp, 0, ',', '.') }}</span>
                                                @endif
                                            @else
                                                <span class="text-purple-500">🤝 Kolaborasi:</span>
                                                <br><span class="text-blue-500">Dokter:</span> Rp {{ number_format($tindakan->tarif_tindakandr, 0, ',', '.') }}
                                                <br><span class="text-green-500">Petugas:</span> Rp {{ number_format($tindakan->tarif_tindakanpr, 0, ',', '.') }}
                                                @if($tindakan->material > 0 || $tindakan->bhp > 0)
                                                    <br><span>Material: Rp {{ number_format($tindakan->material, 0, ',', '.') }}, BHP: Rp {{ number_format($tindakan->bhp, 0, ',', '.') }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right text-sm">
                                        @if($selectedJenisTindakan === 'dr')
                                            <span class="font-semibold text-lg" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                                Rp {{ number_format($tindakan->total_byrdr, 0, ',', '.') }}
                                            </span>
                                            <div class="text-xs" x-bind:class="darkMode ? 'text-blue-400' : 'text-blue-600'">
                                                👨‍⚕️ Total Dokter
                                            </div>
                                        @elseif($selectedJenisTindakan === 'pr')
                                            <span class="font-semibold text-lg" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                                Rp {{ number_format($tindakan->total_byrpr, 0, ',', '.') }}
                                            </span>
                                            <div class="text-xs" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                                👩‍⚕️ Total Petugas
                                            </div>
                                        @else
                                            <span class="font-semibold text-lg" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                                Rp {{ number_format($tindakan->total_byrdrpr, 0, ',', '.') }}
                                            </span>
                                            <div class="text-xs" x-bind:class="darkMode ? 'text-purple-400' : 'text-purple-600'">
                                                🤝 Total Kolaborasi
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="p-6 text-center" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                @if($selectedJenisTindakan === 'dr')
                                    <p>👨‍⚕️ Tidak ada tindakan dokter ditemukan</p>
                                    <p class="text-xs mt-1">Hanya menampilkan tindakan dengan tarif dokter > 0</p>
                                @elseif($selectedJenisTindakan === 'pr')
                                    <p>👩‍⚕️ Tidak ada tindakan petugas ditemukan</p>
                                    <p class="text-xs mt-1">Hanya menampilkan tindakan dengan tarif petugas > 0</p>
                                @else
                                    <p>🤝 Tidak ada tindakan kolaborasi ditemukan</p>
                                    <p class="text-xs mt-1">Hanya menampilkan tindakan dengan tarif dokter & petugas > 0</p>
                                @endif
                            </div>
                            @endforelse
                        </div>

                        @if($jenisPerawatan->hasPages())
                        <div class="p-4 border-t" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                            {{ $jenisPerawatan->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Right Panel: Existing Tindakan --}}
                <div class="space-y-6">
                    {{-- Header with Reset All Button --}}
                    @if(count($existingTindakanDr) > 0 || count($existingTindakanPr) > 0 || count($existingTindakanDrPr) > 0)
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            📋 Tindakan Tersimpan
                        </h2>
                        <button
                            wire:click="resetAllTindakan"
                            wire:confirm="Apakah Anda yakin ingin menghapus SEMUA tindakan yang sudah disimpan? Tindakan ini tidak dapat dibatalkan!"
                            class="px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2"
                            x-bind:class="darkMode ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-red-500 hover:bg-red-600 text-white'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Semua
                        </button>
                    </div>
                    @endif

                    {{-- Tindakan Dokter --}}
                    @if(count($existingTindakanDr) > 0)
                    <div class="rounded-lg shadow"
                         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                            <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                👨‍⚕️ Tindakan Dokter ({{ count($existingTindakanDr) }})
                            </h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @foreach($existingTindakanDr as $tindakan)
                            <div class="p-3 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-medium text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            {{ $tindakan['jenis_perawatan']['nm_perawatan'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                            {{ $tindakan['dokter']['nm_dokter'] ?? 'N/A' }} |
                                            {{ \Carbon\Carbon::parse($tindakan['tgl_perawatan'])->format('d/m/Y') }} {{ $tindakan['jam_rawat'] }}
                                        </p>
                                        <p class="text-xs font-semibold" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                            Rp {{ number_format($tindakan['biaya_rawat'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <button type="button"
                                            wire:click="deleteTindakan('dr', {{ json_encode([
                                                'no_rawat' => $tindakan['no_rawat'],
                                                'kd_jenis_prw' => $tindakan['kd_jenis_prw'],
                                                'kd_dokter' => $tindakan['kd_dokter'],
                                                'tgl_perawatan' => $tindakan['tgl_perawatan'],
                                                'jam_rawat' => $tindakan['jam_rawat']
                                            ]) }})"
                                            onclick="confirm('Yakin hapus tindakan ini?') || event.stopImmediatePropagation()"
                                            class="text-red-500 hover:text-red-700 text-xs p-1">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Tindakan Petugas --}}
                    @if(count($existingTindakanPr) > 0)
                    <div class="rounded-lg shadow"
                         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                            <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                👩‍⚕️ Tindakan Petugas ({{ count($existingTindakanPr) }})
                            </h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @foreach($existingTindakanPr as $tindakan)
                            <div class="p-3 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-medium text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            {{ $tindakan['jenis_perawatan']['nm_perawatan'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                            {{ $tindakan['petugas']['nama'] ?? 'N/A' }} |
                                            {{ \Carbon\Carbon::parse($tindakan['tgl_perawatan'])->format('d/m/Y') }} {{ $tindakan['jam_rawat'] }}
                                        </p>
                                        <p class="text-xs font-semibold" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                            Rp {{ number_format($tindakan['biaya_rawat'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <button type="button"
                                            wire:click="deleteTindakan('pr', {{ json_encode([
                                                'no_rawat' => $tindakan['no_rawat'],
                                                'kd_jenis_prw' => $tindakan['kd_jenis_prw'],
                                                'nip' => $tindakan['nip'],
                                                'tgl_perawatan' => $tindakan['tgl_perawatan'],
                                                'jam_rawat' => $tindakan['jam_rawat']
                                            ]) }})"
                                            onclick="confirm('Yakin hapus tindakan ini?') || event.stopImmediatePropagation()"
                                            class="text-red-500 hover:text-red-700 text-xs p-1">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Tindakan Dokter + Petugas --}}
                    @if(count($existingTindakanDrPr) > 0)
                    <div class="rounded-lg shadow"
                         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                            <h3 class="text-md font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                🤝 Tindakan Kolaborasi ({{ count($existingTindakanDrPr) }})
                            </h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @foreach($existingTindakanDrPr as $tindakan)
                            <div class="p-3 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-medium text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            {{ $tindakan['jenis_perawatan']['nm_perawatan'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                            Dr: {{ $tindakan['dokter']['nm_dokter'] ?? 'N/A' }} |
                                            Petugas: {{ $tindakan['petugas']['nama'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                            {{ \Carbon\Carbon::parse($tindakan['tgl_perawatan'])->format('d/m/Y') }} {{ $tindakan['jam_rawat'] }}
                                        </p>
                                        <p class="text-xs font-semibold" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                            Rp {{ number_format($tindakan['biaya_rawat'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <button type="button"
                                            wire:click="deleteTindakan('drpr', {{ json_encode([
                                                'no_rawat' => $tindakan['no_rawat'],
                                                'kd_jenis_prw' => $tindakan['kd_jenis_prw'],
                                                'kd_dokter' => $tindakan['kd_dokter'],
                                                'nip' => $tindakan['nip'],
                                                'tgl_perawatan' => $tindakan['tgl_perawatan'],
                                                'jam_rawat' => $tindakan['jam_rawat']
                                            ]) }})"
                                            onclick="confirm('Yakin hapus tindakan ini?') || event.stopImmediatePropagation()"
                                            class="text-red-500 hover:text-red-700 text-xs p-1">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Empty State --}}
                    @if(count($existingTindakanDr) === 0 && count($existingTindakanPr) === 0 && count($existingTindakanDrPr) === 0)
                    <div class="rounded-lg shadow p-6 text-center"
                         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                        <p x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada tindakan yang diinput
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>