<div x-data="{
    darkMode: false,
    init() {
        // Immediate detection
        this.darkMode = document.documentElement.classList.contains('dark');

        // Force update after DOM ready
        this.$nextTick(() => {
            this.darkMode = document.documentElement.classList.contains('dark');
        });

        // Watch for theme changes with debounce
        const observer = new MutationObserver(() => {
            setTimeout(() => {
                this.darkMode = document.documentElement.classList.contains('dark');
            }, 50);
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        // Additional check every 500ms for first 3 seconds
        let checks = 0;
        const interval = setInterval(() => {
            this.darkMode = document.documentElement.classList.contains('dark');
            checks++;
            if (checks >= 6) clearInterval(interval);
        }, 500);
    }
}" x-init="init()">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <div class="min-h-screen py-4 sm:py-8" x-bind:class="darkMode ? 'bg-gray-900' : 'bg-gray-50'">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">📝 Form Input SOAPIE - WebKhanza</h1>

            <div class="rounded-lg shadow p-3 sm:p-6"
                 x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                <form wire:submit="simpanPemeriksaan" class="space-y-6">


                    {{-- Basic Info Section --}}
                    <div class="space-y-4 rounded-lg p-3 sm:p-4"
                         x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                        <h3 class="text-base sm:text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">📅 Informasi Dasar</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="tgl_perawatan" required
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Jam <span class="text-red-500">*</span></label>
                                <input type="time" wire:model="jam_rawat" required
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Petugas <span class="text-red-500">*</span></label>
                                @if($isAdmin && !empty($pegawaiList))
                                    <select wire:model="nip" required
                                            x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'">
                                        <option value="">Pilih Petugas...</option>
                                        @foreach($pegawaiList as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" wire:model="nip" readonly required
                                           x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-600 text-gray-300' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600'" />
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TTV Section --}}
                    <div class="space-y-4 rounded-lg p-3 sm:p-4"
                         x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                        <h3 class="text-base sm:text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">🩺 Tanda-Tanda Vital (TTV)</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Suhu Tubuh (°C)</label>
                                <input type="number" step="0.1" wire:model="suhu_tubuh" placeholder="36.5"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Tensi (mmHg)</label>
                                <input type="text" wire:model="tensi" placeholder="120/80"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Nadi (x/menit) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="nadi" required min="30" max="160" placeholder="80"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Respirasi (x/menit) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="respirasi" required min="5" max="70" placeholder="20"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">SpO2 (%)</label>
                                <input type="number" wire:model="spo2" min="0" max="100" placeholder="98"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Tinggi (cm) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="tinggi" required min="30" max="250" placeholder="170"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Berat (kg) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="berat" required min="2" max="300" placeholder="65"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">GCS</label>
                                <input type="text" wire:model="gcs" placeholder="E4V5M6"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Kesadaran</label>
                                <input type="text" wire:model="kesadaran" placeholder="Compos Mentis"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Alergi</label>
                                <input type="text" wire:model="alergi" placeholder="Tidak ada"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">Lingkar Perut (cm)</label>
                                <input type="number" wire:model="lingkar_perut" placeholder="80"
                                       x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'" />
                            </div>
                        </div>
                    </div>

                    {{-- SOAPIE Section --}}
                    <div class="space-y-4 rounded-lg p-3 sm:p-4"
                         x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                        <h3 class="text-base sm:text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">📋 SOAPIE Assessment</h3>
                        <p class="text-xs sm:text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">Subjective, Objective, Assessment, Plan, Intervention, Evaluation</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">

                            {{-- Subjective --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white rounded-full text-xs font-bold mr-2">S</span>
                                    Subjective <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="keluhan" rows="4" placeholder="Keluhan pasien..." required
                                          x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"></textarea>
                            </div>

                            {{-- Objective --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs font-bold mr-2">O</span>
                                    Objective <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="pemeriksaan" rows="4" placeholder="Hasil pemeriksaan..." required
                                          x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"></textarea>
                            </div>

                            {{-- Assessment --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white rounded-full text-xs font-bold mr-2">A</span>
                                    Assessment
                                </label>
                                <textarea wire:model="penilaian" rows="4" placeholder="Diagnosis..."
                                          x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"></textarea>
                            </div>

                            {{-- Plan --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-500 text-white rounded-full text-xs font-bold mr-2">P</span>
                                    Plan <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="rtl" rows="4" placeholder="Rencana pengobatan..." required
                                          x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"></textarea>
                            </div>

                            {{-- Intervention --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-500 text-white rounded-full text-xs font-bold mr-2">I</span>
                                    Intervention
                                </label>
                                <textarea wire:model="instruksi" rows="4" placeholder="Instruksi/tindakan..."
                                          x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"></textarea>
                            </div>

                            {{-- Evaluation --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-teal-500 text-white rounded-full text-xs font-bold mr-2">E</span>
                                    Evaluation
                                </label>
                                <textarea wire:model="evaluasi" rows="4" placeholder="Evaluasi hasil..."
                                          x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"></textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 rounded-lg p-3 sm:p-4"
                         x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">
                        <x-filament::button type="button" color="gray" size="sm" wire:click="resetForm">
                            🔄 Reset Form
                        </x-filament::button>

                        @if($editingId)
                            <x-filament::button type="submit" size="sm" color="warning">
                                ✏️ Update SOAP
                            </x-filament::button>
                        @else
                            <x-filament::button type="submit" size="sm">
                                💾 Simpan SOAP
                            </x-filament::button>
                        @endif
                    </div>

                </form>
            </div>

            {{-- History Section --}}
            @if(count($riwayatPemeriksaan ?? []) > 0)
                <div class="mt-4 sm:mt-6 rounded-lg shadow p-3 sm:p-6"
                     x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <h3 class="text-base sm:text-lg font-medium mb-3 sm:mb-4" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">📋 Riwayat Pemeriksaan ({{ count($riwayatPemeriksaan) }} records)</h3>

                    <div class="space-y-3 sm:space-y-4">
                        @foreach($riwayatPemeriksaan as $item)
                            <div class="rounded-lg p-3 sm:p-4"
                                 x-bind:class="darkMode ? 'bg-gray-600 border border-gray-500' : 'bg-gray-100 border border-gray-300'">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 sm:mb-4 gap-2">
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 items-start sm:items-center text-xs sm:text-sm">
                                        <span class="font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            📅 {{ \Carbon\Carbon::parse($item['tgl_perawatan'])->format('d/m/Y') }}
                                        </span>
                                        <span class="font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            🕐 {{ substr($item['jam_rawat'], 0, 5) }}
                                        </span>
                                    </div>

                                    @if($isAdmin || $item['nip'] === auth()->user()->pegawai->nik ?? auth()->user()->username)
                                        <button type="button"
                                                wire:click="editPemeriksaan('{{ $item['tgl_perawatan_raw'] }}', '{{ $item['jam_rawat_raw'] }}')"
                                                class="px-2 sm:px-3 py-1 bg-orange-100 text-orange-700 border border-orange-300 rounded-lg hover:bg-orange-200 text-xs sm:text-sm">
                                            ✏️ Edit
                                        </button>
                                    @endif
                                </div>

                                {{-- TTV Section --}}
                                <div class="mb-4">
                                    <h4 class="font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">🩺 Tanda-Tanda Vital</h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 text-xs">
                                        @if($item['suhu_tubuh'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">Suhu:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['suhu_tubuh'] }}°C</span>
                                            </div>
                                        @endif
                                        @if($item['tensi'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">Tensi:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['tensi'] }}</span>
                                            </div>
                                        @endif
                                        @if($item['nadi'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">Nadi:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['nadi'] }}/min</span>
                                            </div>
                                        @endif
                                        @if($item['respirasi'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">Resp:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['respirasi'] }}/min</span>
                                            </div>
                                        @endif
                                        @if($item['spo2'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">SpO2:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['spo2'] }}%</span>
                                            </div>
                                        @endif
                                        @if($item['tinggi'] && $item['berat'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">TB/BB:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['tinggi'] }}/{{ $item['berat'] }}</span>
                                            </div>
                                        @endif
                                        @if($item['gcs'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">GCS:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['gcs'] }}</span>
                                            </div>
                                        @endif
                                        @if($item['kesadaran'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">Kesadaran:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['kesadaran'] }}</span>
                                            </div>
                                        @endif
                                        @if($item['alergi'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">Alergi:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['alergi'] }}</span>
                                            </div>
                                        @endif
                                        @if($item['lingkar_perut'])
                                            <div>
                                                <span class="font-medium" x-bind:class="darkMode ? 'text-red-400' : 'text-red-600'">LP:</span>
                                                <span x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['lingkar_perut'] }}cm</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- SOAPIE Section --}}
                                <div>
                                    <h4 class="font-medium mb-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-800'">📋 SOAPIE Assessment</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                                        <div>
                                            <strong x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">S:</strong>
                                            <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['keluhan'] }}</p>
                                        </div>
                                        <div>
                                            <strong x-bind:class="darkMode ? 'text-blue-400' : 'text-blue-600'">O:</strong>
                                            <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['pemeriksaan'] }}</p>
                                        </div>
                                        <div>
                                            <strong x-bind:class="darkMode ? 'text-orange-400' : 'text-orange-600'">A:</strong>
                                            <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['penilaian'] ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <strong x-bind:class="darkMode ? 'text-purple-400' : 'text-purple-600'">P:</strong>
                                            <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['rtl'] }}</p>
                                        </div>
                                        <div>
                                            <strong x-bind:class="darkMode ? 'text-indigo-400' : 'text-indigo-600'">I:</strong>
                                            <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['instruksi'] ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <strong x-bind:class="darkMode ? 'text-teal-400' : 'text-teal-600'">E:</strong>
                                            <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">{{ $item['evaluasi'] ?: '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>