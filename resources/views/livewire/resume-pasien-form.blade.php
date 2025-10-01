<div x-data="{
    darkMode: false,
    showKeluhanUtamaModal: false,
    showAnamnesisModal: false,
    showSoapieModal: false,
    showDiagnosaModal: false,
    showProsedurModal: false,
    showLabModal: false,
    showRadiologiModal: false,
    selectedDiagnosaLevel: 'utama',
    selectedProsedurLevel: 'utama',
    init() {
        this.darkMode = document.documentElement.classList.contains('dark');
        this.$nextTick(() => {
            this.darkMode = document.documentElement.classList.contains('dark');
        });
        const observer = new MutationObserver(() => {
            setTimeout(() => {
                this.darkMode = document.documentElement.classList.contains('dark');
            }, 50);
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    }
}" x-init="init()">

    <div class="space-y-6">
        <form wire:submit="simpanResume" class="space-y-6">

            {{-- Anamnesis & Pemeriksaan Section --}}
            <div class="space-y-4 rounded-lg p-4"
                 x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                <h3 class="text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                    📋 Anamnesis & Pemeriksaan
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                Keluhan Utama <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="showKeluhanUtamaModal = true"
                                    class="px-3 py-1 rounded-lg text-xs font-medium transition-colors flex items-center gap-1"
                                    x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'">
                                💬 Pilih Keluhan Utama
                            </button>
                        </div>
                        <textarea wire:model="keluhan_utama" rows="3"
                                x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                                placeholder="Masukkan keluhan utama pasien..."></textarea>
                        @error('keluhan_utama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                Jalannya Penyakit/Anamnesis <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="showAnamnesisModal = true"
                                    class="px-3 py-1 rounded-lg text-xs font-medium transition-colors flex items-center gap-1"
                                    x-bind:class="darkMode ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-green-500 hover:bg-green-600 text-white'">
                                🔍 Pilih Anamnesis
                            </button>
                        </div>
                        <textarea wire:model="jalannya_penyakit" rows="3"
                                x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                                placeholder="Masukkan riwayat perjalanan penyakit..."></textarea>
                        @error('jalannya_penyakit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                Pemeriksaan Penunjang
                            </label>
                            <button type="button" @click="showRadiologiModal = true"
                                    class="px-3 py-1 rounded-lg text-xs font-medium transition-colors flex items-center gap-1"
                                    x-bind:class="darkMode ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'bg-purple-500 hover:bg-purple-600 text-white'">
                                🩻 Pilih dari Radiologi
                            </button>
                        </div>
                        <textarea wire:model="pemeriksaan_penunjang" rows="3"
                                x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                                placeholder="Hasil pemeriksaan penunjang..."></textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                Hasil Laboratorium
                            </label>
                            <button type="button" @click="showLabModal = true"
                                    class="px-3 py-1 rounded-lg text-xs font-medium transition-colors flex items-center gap-1"
                                    x-bind:class="darkMode ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-yellow-500 hover:bg-yellow-600 text-white'">
                                🧪 Pilih dari Lab
                            </button>
                        </div>
                        <textarea wire:model="hasil_laborat" rows="3"
                                x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                                placeholder="Hasil pemeriksaan laboratorium..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Diagnosa Section --}}
            <div class="space-y-4 rounded-lg p-4"
                 x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        🩺 Diagnosa (ICD-10)
                    </h3>
                    <button type="button" @click="showDiagnosaModal = true"
                            x-bind:class="darkMode ? 'bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm' : 'bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm'">
                        🩺 Pilih dari Diagnosa yang Ada
                    </button>
                </div>

                {{-- Diagnosa Utama --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Kode Diagnosa Utama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.live="kd_diagnosa_utama"
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                               placeholder="Cari kode ICD-10..." />
                        @error('kd_diagnosa_utama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Nama Diagnosa Utama
                        </label>
                        <input type="text" wire:model="diagnosa_utama" readonly
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-600 text-gray-300' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600'" />
                    </div>
                </div>

                {{-- Diagnosa Sekunder 1-4 --}}
                @foreach(['sekunder' => 'Sekunder 1', 'sekunder2' => 'Sekunder 2', 'sekunder3' => 'Sekunder 3', 'sekunder4' => 'Sekunder 4'] as $level => $label)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Kode Diagnosa {{ $label }}
                        </label>
                        <input type="text" wire:model.live="kd_diagnosa_{{ $level }}"
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                               placeholder="Cari kode ICD-10..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Nama Diagnosa {{ $label }}
                        </label>
                        <input type="text" wire:model="diagnosa_{{ $level }}" readonly
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-600 text-gray-300' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600'" />
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Prosedur Section --}}
            <div class="space-y-4 rounded-lg p-4"
                 x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        🔧 Prosedur/Tindakan (ICD-9)
                    </h3>
                    <button type="button" @click="showProsedurModal = true"
                            x-bind:class="darkMode ? 'bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm' : 'bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm'">
                        🔧 Pilih dari Prosedur yang Ada
                    </button>
                </div>

                {{-- Prosedur Utama --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Kode Prosedur Utama
                        </label>
                        <input type="text" wire:model.live="kd_prosedur_utama"
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                               placeholder="Cari kode ICD-9..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Nama Prosedur Utama
                        </label>
                        <input type="text" wire:model="prosedur_utama" readonly
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-600 text-gray-300' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600'" />
                    </div>
                </div>

                {{-- Prosedur Sekunder 1-3 --}}
                @foreach(['sekunder' => 'Sekunder 1', 'sekunder2' => 'Sekunder 2', 'sekunder3' => 'Sekunder 3'] as $level => $label)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Kode Prosedur {{ $label }}
                        </label>
                        <input type="text" wire:model.live="kd_prosedur_{{ $level }}"
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                               placeholder="Cari kode ICD-9..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Nama Prosedur {{ $label }}
                        </label>
                        <input type="text" wire:model="prosedur_{{ $level }}" readonly
                               x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-600 text-gray-300' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600'" />
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Kondisi Pulang & Obat Section --}}
            <div class="space-y-4 rounded-lg p-4"
                 x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                <h3 class="text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                    🏠 Kondisi Pulang & Obat
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Kondisi Pulang <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="kondisi_pulang"
                                x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'">
                            <option value="Hidup">Hidup</option>
                            <option value="Meninggal">Meninggal</option>
                        </select>
                        @error('kondisi_pulang') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Obat & Anjuran Pulang
                        </label>
                        <textarea wire:model="obat_pulang" rows="4"
                                x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900'"
                                placeholder="Masukkan obat dan anjuran untuk pasien pulang..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button type="submit"
                        x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out' : 'bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out'"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>💾 Simpan Resume</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>

        </form>
    </div>

    {{-- SOAPIE Modal --}}
    <div x-show="showSoapieModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[80vh] overflow-hidden"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

            {{-- Modal Header --}}
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        📋 Pilih dari Data SOAPIE
                    </h3>
                    <button @click="showSoapieModal = false"
                            class="p-2 rounded-lg transition-colors"
                            x-bind:class="darkMode ? 'text-gray-400 hover:text-gray-200 hover:bg-gray-700' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                @if($this->getSoapieOptions()->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->getSoapieOptions() as $soapie)
                        <div class="rounded-lg border p-4 transition-colors"
                             x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 hover:bg-gray-650' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'">

                            {{-- Header dengan tanggal --}}
                            <div class="flex justify-between items-center mb-3">
                                <div class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                    📅 {{ \Carbon\Carbon::parse($soapie->tgl_perawatan)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($soapie->jam_rawat)->format('H:i') }}
                                </div>
                            </div>

                            {{-- Subjek & Objek untuk Keluhan Utama --}}
                            @if(!empty($soapie->keluhan) || !empty($soapie->pemeriksaan))
                            <div class="mb-3 p-3 rounded-lg border" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-200 bg-white'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-blue-400' : 'text-blue-600'">
                                            💬 Subjek & Objek (untuk Keluhan Utama)
                                        </div>
                                        <div class="space-y-2 text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                            @if(!empty($soapie->keluhan))
                                            <div><span class="font-medium text-blue-500">Subjektif:</span> {{ $soapie->keluhan }}</div>
                                            @endif
                                            @if(!empty($soapie->pemeriksaan))
                                            <div><span class="font-medium text-green-500">Objektif:</span> {{ $soapie->pemeriksaan }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="pilihSubjekObjek('{{ addslashes($soapie->keluhan) }}', '{{ addslashes($soapie->pemeriksaan) }}')"
                                            @click="showSoapieModal = false"
                                            class="ml-3 px-3 py-1 rounded-md text-xs font-medium transition-colors"
                                            x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'">
                                        📋 Tambah ke Keluhan
                                    </button>
                                </div>
                            </div>
                            @endif

                            {{-- Anamnesis Section (hanya penilaian & RTL) --}}
                            @if(!empty($soapie->penilaian) || !empty($soapie->rtl))
                            <div class="mb-3 p-3 rounded-lg border" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-200 bg-white'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                            🔍 Anamnesis (untuk Jalannya Penyakit)
                                        </div>
                                        <div class="space-y-2 text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                            @if(!empty($soapie->penilaian))
                                            <div><span class="font-medium text-amber-500">Assessment:</span> {{ $soapie->penilaian }}</div>
                                            @endif
                                            @if(!empty($soapie->rtl))
                                            <div><span class="font-medium text-purple-500">Planning:</span> {{ $soapie->rtl }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="pilihAnamnesis('', '{{ addslashes($soapie->penilaian) }}', '{{ addslashes($soapie->rtl) }}')"
                                            @click="showSoapieModal = false"
                                            class="ml-3 px-3 py-1 rounded-md text-xs font-medium transition-colors"
                                            x-bind:class="darkMode ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-green-500 hover:bg-green-600 text-white'">
                                        🔍 Tambah ke Anamnesis
                                    </button>
                                </div>
                            </div>
                            @endif

                            {{-- Evaluasi Section --}}
                            @if(!empty($soapie->evaluasi))
                            <div class="p-3 rounded-lg border" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-200 bg-white'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-purple-400' : 'text-purple-600'">
                                            📊 Evaluasi
                                        </div>
                                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                            {{ $soapie->evaluasi }}
                                        </p>
                                    </div>
                                    <button wire:click="pilihKeluhan('{{ addslashes($soapie->evaluasi) }}')"
                                            @click="showSoapieModal = false"
                                            class="ml-3 px-3 py-1 rounded-md text-xs font-medium transition-colors"
                                            x-bind:class="darkMode ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'bg-purple-500 hover:bg-purple-600 text-white'">
                                        📊 Tambah ke Keluhan
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">📋</div>
                        <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Tidak Ada Data SOAPIE
                        </h3>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada pemeriksaan SOAPIE untuk pasien ini
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Diagnosa Modal --}}
    <div x-show="showDiagnosaModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[80vh] overflow-hidden"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

            {{-- Modal Header --}}
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            🩺 Pilih Diagnosa yang Sudah Ada
                        </h3>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                Untuk:
                            </label>
                            <select x-model="selectedDiagnosaLevel"
                                    class="px-3 py-1 rounded-md text-sm border transition-colors"
                                    x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-100' : 'bg-white border-gray-300 text-gray-900'">
                                <option value="utama">Diagnosa Utama</option>
                                <option value="sekunder">Diagnosa Sekunder 1</option>
                                <option value="sekunder2">Diagnosa Sekunder 2</option>
                                <option value="sekunder3">Diagnosa Sekunder 3</option>
                                <option value="sekunder4">Diagnosa Sekunder 4</option>
                            </select>
                        </div>
                    </div>
                    <button @click="showDiagnosaModal = false"
                            class="p-2 rounded-lg transition-colors"
                            x-bind:class="darkMode ? 'text-gray-400 hover:text-gray-200 hover:bg-gray-700' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                @if($this->getDiagnosaOptions()->count() > 0)
                    <div class="space-y-3">
                        @foreach($this->getDiagnosaOptions() as $diagnosa)
                        <div class="rounded-lg border p-4 transition-colors"
                             x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 hover:bg-gray-650' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-start space-x-3">
                                        <div class="px-2 py-1 rounded text-xs font-medium"
                                             x-bind:class="darkMode ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800'">
                                            {{ $diagnosa->kd_penyakit }}
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                {{ $diagnosa->nm_penyakit }}
                                            </h4>
                                            <p class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                Prioritas: {{ $diagnosa->prioritas }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <button x-on:click="$wire.pilihDiagnosa('{{ $diagnosa->kd_penyakit }}', '{{ addslashes($diagnosa->nm_penyakit) }}', selectedDiagnosaLevel); showDiagnosaModal = false"
                                        class="ml-3 px-3 py-1 rounded-md text-xs font-medium transition-colors"
                                        x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'">
                                    🩺 Pilih
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">🩺</div>
                        <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Tidak Ada Diagnosa
                        </h3>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada diagnosa yang dibuat untuk pasien ini
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Prosedur Modal --}}
    <div x-show="showProsedurModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[80vh] overflow-hidden"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

            {{-- Modal Header --}}
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold mb-2" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            🔧 Pilih Prosedur yang Sudah Ada
                        </h3>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                Untuk:
                            </label>
                            <select x-model="selectedProsedurLevel"
                                    class="px-3 py-1 rounded-md text-sm border transition-colors"
                                    x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-100' : 'bg-white border-gray-300 text-gray-900'">
                                <option value="utama">Prosedur Utama</option>
                                <option value="sekunder">Prosedur Sekunder 1</option>
                                <option value="sekunder2">Prosedur Sekunder 2</option>
                                <option value="sekunder3">Prosedur Sekunder 3</option>
                            </select>
                        </div>
                    </div>
                    <button @click="showProsedurModal = false"
                            class="p-2 rounded-lg transition-colors"
                            x-bind:class="darkMode ? 'text-gray-400 hover:text-gray-200 hover:bg-gray-700' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                @if($this->getProsedurExistingOptions()->count() > 0)
                    <div class="space-y-3">
                        @foreach($this->getProsedurExistingOptions() as $prosedur)
                        <div class="rounded-lg border p-4 transition-colors"
                             x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 hover:bg-gray-650' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-start space-x-3">
                                        <div class="px-2 py-1 rounded text-xs font-medium"
                                             x-bind:class="darkMode ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-800'">
                                            {{ $prosedur->kode }}
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                {{ $prosedur->deskripsi_panjang }}
                                            </h4>
                                            <p class="text-xs mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                Prioritas: {{ $prosedur->prioritas }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <button x-on:click="$wire.pilihProsedur('{{ $prosedur->kode }}', '{{ addslashes($prosedur->deskripsi_panjang) }}', selectedProsedurLevel); showProsedurModal = false"
                                        class="ml-3 px-3 py-1 rounded-md text-xs font-medium transition-colors"
                                        x-bind:class="darkMode ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'bg-purple-500 hover:bg-purple-600 text-white'">
                                    🔧 Pilih
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">🔧</div>
                        <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Tidak Ada Prosedur
                        </h3>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada prosedur yang dibuat untuk pasien ini
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Lab Modal --}}
    <div x-show="showLabModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[80vh] overflow-hidden"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

            {{-- Modal Header --}}
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        🧪 Pilih dari Hasil Laboratorium
                    </h3>
                    <button @click="showLabModal = false"
                            class="p-2 rounded-lg transition-colors"
                            x-bind:class="darkMode ? 'text-gray-400 hover:text-gray-200 hover:bg-gray-700' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                @if($this->getLabOptions()->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->getLabOptions() as $lab)
                        <div class="rounded-lg border p-4 transition-colors"
                             x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 hover:bg-gray-650' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'">

                            {{-- Header dengan tanggal --}}
                            <div class="flex justify-between items-center mb-3">
                                <div class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                    📅 {{ \Carbon\Carbon::parse($lab->tgl_periksa)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($lab->jam)->format('H:i') }}
                                </div>
                                @if(!empty($lab->nama_pemeriksaan))
                                <div class="px-2 py-1 rounded text-xs font-medium"
                                     x-bind:class="darkMode ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800'">
                                    {{ $lab->nama_pemeriksaan }}
                                </div>
                                @endif
                            </div>

                            {{-- Lab Results --}}
                            @if(!empty($lab->nilai) || !empty($lab->keterangan))
                            <div class="p-3 rounded-lg border" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-200 bg-white'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="space-y-2 text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                            @if(!empty($lab->nilai))
                                            <div>
                                                <span class="font-medium">Nilai:</span> {{ $lab->nilai }}
                                                @if(!empty($lab->nilai_rujukan))
                                                <span class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                    (Rujukan: {{ $lab->nilai_rujukan }})
                                                </span>
                                                @endif
                                            </div>
                                            @endif
                                            @if(!empty($lab->keterangan))
                                            <div><span class="font-medium">Keterangan:</span> {{ $lab->keterangan }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="pilihHasilLab('{{ addslashes(($lab->nama_pemeriksaan ? $lab->nama_pemeriksaan . ': ' : '') . $lab->nilai . ($lab->nilai_rujukan ? ' (Rujukan: ' . $lab->nilai_rujukan . ')' : '') . ($lab->keterangan ? '\nKeterangan: ' . $lab->keterangan : '')) }}')"
                                            @click="showLabModal = false"
                                            class="ml-3 px-3 py-1 rounded-md text-xs font-medium transition-colors"
                                            x-bind:class="darkMode ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-green-500 hover:bg-green-600 text-white'">
                                        🧪 Pilih
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">🧪</div>
                        <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Tidak Ada Hasil Lab
                        </h3>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada hasil laboratorium untuk pasien ini
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Radiologi Modal --}}
    <div x-show="showRadiologiModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[80vh] overflow-hidden"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

            {{-- Modal Header --}}
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        🩻 Pilih dari Hasil Radiologi
                    </h3>
                    <button @click="showRadiologiModal = false"
                            class="p-2 rounded-lg transition-colors"
                            x-bind:class="darkMode ? 'text-gray-400 hover:text-gray-200 hover:bg-gray-700' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                @if($this->getRadiologiOptions()->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->getRadiologiOptions() as $radiologi)
                        <div class="rounded-lg border p-4 transition-colors"
                             x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 hover:bg-gray-650' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'">

                            {{-- Header dengan tanggal --}}
                            <div class="flex justify-between items-center mb-3">
                                <div class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                    📅 {{ \Carbon\Carbon::parse($radiologi->tgl_permintaan)->format('d/m/Y') }}
                                </div>
                            </div>

                            {{-- Radiologi Results --}}
                            @if(!empty($radiologi->hasil))
                            <div class="p-3 rounded-lg border" x-bind:class="darkMode ? 'border-gray-600 bg-gray-800' : 'border-gray-200 bg-white'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-orange-400' : 'text-orange-600'">
                                            🩻 Hasil Radiologi
                                        </div>
                                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                            {{ $radiologi->hasil }}
                                        </p>
                                    </div>
                                    <button wire:click="pilihPemeriksaanPenunjang('{{ addslashes($radiologi->hasil) }}')"
                                            @click="showRadiologiModal = false"
                                            class="ml-3 px-3 py-1 rounded-md text-xs font-medium transition-colors"
                                            x-bind:class="darkMode ? 'bg-orange-600 hover:bg-orange-700 text-white' : 'bg-orange-500 hover:bg-orange-600 text-white'">
                                        🩻 Pilih
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">🩻</div>
                        <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Tidak Ada Hasil Radiologi
                        </h3>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada hasil radiologi untuk pasien ini
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Keluhan Utama --}}
    <div x-show="showKeluhanUtamaModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="rounded-lg shadow-xl max-w-7xl w-full mx-4 max-h-[85vh] overflow-hidden"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

            {{-- Modal Header --}}
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        💬 Pilih Keluhan Utama (Subjek & Objek)
                    </h3>
                    <button @click="showKeluhanUtamaModal = false"
                            class="p-2 rounded-lg transition-colors"
                            x-bind:class="darkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'">
                        ✕
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[65vh]">
                @if($this->getKeluhanUtamaOptions()->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->getKeluhanUtamaOptions() as $data)
                        <div class="rounded-lg border p-4"
                             x-bind:class="darkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-200 bg-gray-50'">

                            {{-- Header dengan tanggal --}}
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                    📅 {{ \Carbon\Carbon::parse($data->tgl_perawatan)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data->jam_rawat)->format('H:i') }}
                                </div>
                            </div>

                            {{-- Grid Dua Kolom --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                                {{-- Kolom Subjek --}}
                                <div class="space-y-3">
                                    <h4 class="text-sm font-semibold flex items-center gap-2" x-bind:class="darkMode ? 'text-blue-400' : 'text-blue-600'">
                                        💭 SUBJEKTIF (Keluhan)
                                    </h4>

                                    @if(!empty($data->keluhan))
                                    <div class="p-3 rounded-lg border transition-colors"
                                         x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 hover:bg-gray-750' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                        <label class="flex items-start space-x-3 cursor-pointer">
                                            <input type="checkbox"
                                                   wire:click="toggleSubjek('{{ $data->tgl_perawatan }}_{{ $data->jam_rawat }}', '{{ addslashes($data->keluhan) }}')"
                                                   {{ isset($selectedSubjek[$data->tgl_perawatan . '_' . $data->jam_rawat]) ? 'checked' : '' }}
                                                   class="mt-1 h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                                    {{ $data->keluhan }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                    @else
                                    <div class="text-center py-4" x-bind:class="darkMode ? 'text-gray-500' : 'text-gray-400'">
                                        <p class="text-sm">Tidak ada data subjektif</p>
                                    </div>
                                    @endif
                                </div>

                                {{-- Kolom Objek --}}
                                <div class="space-y-3">
                                    <h4 class="text-sm font-semibold flex items-center gap-2" x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">
                                        🔍 OBJEKTIF (Pemeriksaan)
                                    </h4>

                                    @if(!empty($data->pemeriksaan))
                                    <div class="p-3 rounded-lg border transition-colors"
                                         x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 hover:bg-gray-750' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                        <label class="flex items-start space-x-3 cursor-pointer">
                                            <input type="checkbox"
                                                   wire:click="toggleObjek('{{ $data->tgl_perawatan }}_{{ $data->jam_rawat }}', '{{ addslashes($data->pemeriksaan) }}')"
                                                   {{ isset($selectedObjek[$data->tgl_perawatan . '_' . $data->jam_rawat]) ? 'checked' : '' }}
                                                   class="mt-1 h-4 w-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                                    {{ $data->pemeriksaan }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                    @else
                                    <div class="text-center py-4" x-bind:class="darkMode ? 'text-gray-500' : 'text-gray-400'">
                                        <p class="text-sm">Tidak ada data objektif</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">💬</div>
                        <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Tidak Ada Data Keluhan
                        </h3>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada data keluhan untuk pasien ini
                        </p>
                    </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 border-t flex justify-between items-center" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                    Pilih data subjek dan/atau objek yang ingin di tambahkan
                </div>
                <div class="flex space-x-3">
                    <button @click="showKeluhanUtamaModal = false"
                            class="px-4 py-2 rounded-lg font-medium transition-colors"
                            x-bind:class="darkMode ? 'bg-gray-600 hover:bg-gray-700 text-white' : 'bg-gray-500 hover:bg-gray-600 text-white'">
                        Batal
                    </button>
                    <button wire:click="applyKeluhanUtama"
                            @click="showKeluhanUtamaModal = false"
                            class="px-4 py-2 rounded-lg font-medium transition-colors"
                            x-bind:class="darkMode ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-blue-500 hover:bg-blue-600 text-white'">
                        💬 Tambah kan ke Keluhan Utama
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Anamnesis --}}
    <div x-show="showAnamnesisModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="rounded-lg shadow-xl max-w-7xl w-full mx-4 max-h-[85vh] overflow-hidden"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

            {{-- Modal Header --}}
            <div class="p-4 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        🔍 Pilih Anamnesis (Assessment & Planning)
                    </h3>
                    <button @click="showAnamnesisModal = false"
                            class="p-2 rounded-lg transition-colors"
                            x-bind:class="darkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'">
                        ✕
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-4 overflow-y-auto max-h-[65vh]">
                @if($this->getAnamnesisOptions()->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->getAnamnesisOptions() as $data)
                        <div class="rounded-lg border p-4"
                             x-bind:class="darkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-200 bg-gray-50'">

                            {{-- Header dengan tanggal --}}
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                    📅 {{ \Carbon\Carbon::parse($data->tgl_perawatan)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($data->jam_rawat)->format('H:i') }}
                                </div>
                            </div>

                            {{-- Grid Dua Kolom --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                                {{-- Kolom Assessment --}}
                                <div class="space-y-3">
                                    <h4 class="text-sm font-semibold flex items-center gap-2" x-bind:class="darkMode ? 'text-amber-400' : 'text-amber-600'">
                                        📊 ASSESSMENT (Penilaian)
                                    </h4>

                                    @if(!empty($data->penilaian))
                                    <div class="p-3 rounded-lg border transition-colors"
                                         x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 hover:bg-gray-750' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                        <label class="flex items-start space-x-3 cursor-pointer">
                                            <input type="checkbox"
                                                   wire:click="toggleAssessment('{{ $data->tgl_perawatan }}_{{ $data->jam_rawat }}', '{{ addslashes($data->penilaian) }}')"
                                                   {{ isset($selectedAssessment[$data->tgl_perawatan . '_' . $data->jam_rawat]) ? 'checked' : '' }}
                                                   class="mt-1 h-4 w-4 text-amber-600 rounded border-gray-300 focus:ring-amber-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                                    {{ $data->penilaian }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                    @else
                                    <div class="text-center py-4" x-bind:class="darkMode ? 'text-gray-500' : 'text-gray-400'">
                                        <p class="text-sm">Tidak ada data assessment</p>
                                    </div>
                                    @endif
                                </div>

                                {{-- Kolom Planning --}}
                                <div class="space-y-3">
                                    <h4 class="text-sm font-semibold flex items-center gap-2" x-bind:class="darkMode ? 'text-purple-400' : 'text-purple-600'">
                                        📋 PLANNING (RTL)
                                    </h4>

                                    @if(!empty($data->rtl))
                                    <div class="p-3 rounded-lg border transition-colors"
                                         x-bind:class="darkMode ? 'border-gray-600 bg-gray-800 hover:bg-gray-750' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                        <label class="flex items-start space-x-3 cursor-pointer">
                                            <input type="checkbox"
                                                   wire:click="togglePlanning('{{ $data->tgl_perawatan }}_{{ $data->jam_rawat }}', '{{ addslashes($data->rtl) }}')"
                                                   {{ isset($selectedPlanning[$data->tgl_perawatan . '_' . $data->jam_rawat]) ? 'checked' : '' }}
                                                   class="mt-1 h-4 w-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                                    {{ $data->rtl }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                    @else
                                    <div class="text-center py-4" x-bind:class="darkMode ? 'text-gray-500' : 'text-gray-400'">
                                        <p class="text-sm">Tidak ada data planning</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-6xl mb-4">🔍</div>
                        <h3 class="text-lg font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                            Tidak Ada Data Anamnesis
                        </h3>
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            Belum ada data anamnesis untuk pasien ini
                        </p>
                    </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 border-t flex justify-between items-center" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <div class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                    Pilih data assessment dan/atau planning yang ingin di tambahkan
                </div>
                <div class="flex space-x-3">
                    <button @click="showAnamnesisModal = false"
                            class="px-4 py-2 rounded-lg font-medium transition-colors"
                            x-bind:class="darkMode ? 'bg-gray-600 hover:bg-gray-700 text-white' : 'bg-gray-500 hover:bg-gray-600 text-white'">
                        Batal
                    </button>
                    <button wire:click="applyAnamnesis"
                            @click="showAnamnesisModal = false"
                            class="px-4 py-2 rounded-lg font-medium transition-colors"
                            x-bind:class="darkMode ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-green-500 hover:bg-green-600 text-white'">
                        🔍 Tambah kan ke Anamnesis
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>