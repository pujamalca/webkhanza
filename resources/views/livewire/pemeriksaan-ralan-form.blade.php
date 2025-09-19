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
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <h3 class="text-base sm:text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">🩺 Tanda-Tanda Vital (TTV)</h3>
                            <x-filament::button
                                type="button"
                                color="info"
                                size="sm"
                                wire:click="fillTTVFromPrevious"
                                icon="heroicon-m-arrow-path">
                                📋 Isi dari Data Sebelumnya
                            </x-filament::button>
                        </div>
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
                                <select wire:model="kesadaran"
                                        x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'">
                                    <option value="">Pilih Kesadaran...</option>
                                    <option value="Compos Mentis">Compos Mentis</option>
                                    <option value="Somnolence">Somnolence</option>
                                    <option value="Sopor">Sopor</option>
                                    <option value="Coma">Coma</option>
                                    <option value="Alert">Alert</option>
                                    <option value="Confusion">Confusion</option>
                                    <option value="Voice">Voice</option>
                                    <option value="Pain">Pain</option>
                                    <option value="Unresponsive">Unresponsive</option>
                                    <option value="Apatis">Apatis</option>
                                    <option value="Delirium">Delirium</option>
                                </select>
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
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <div>
                                <h3 class="text-base sm:text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">📋 SOAPIE Assessment</h3>
                                <p class="text-xs sm:text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">Subjective, Objective, Assessment, Plan, Intervention, Evaluation</p>
                            </div>
                            <div>
                                <x-filament::button
                                    type="button"
                                    color="info"
                                    size="sm"
                                    wire:click="openTemplateModal"
                                    icon="heroicon-m-document-text">
                                    📝 Template
                                </x-filament::button>
                            </div>
                        </div>
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
                    <div class="space-y-3 rounded-lg p-3 sm:p-4"
                         x-bind:class="darkMode ? 'bg-gray-700 border border-gray-600' : 'bg-gray-50 border border-gray-200'">

                        {{-- Save to Template Checkbox --}}
                        @if(!$editingId)
                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    id="saveToTemplate"
                                    wire:model="saveToTemplate"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    x-bind:class="darkMode ? 'bg-gray-600 border-gray-500' : 'bg-white border-gray-300'">
                                <label for="saveToTemplate" class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                                    💾 Simpan juga sebagai template
                                </label>
                            </div>
                        @endif

                        {{-- Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
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
                    </div>

                </form>
            </div>

            {{-- History Section --}}
            @if(count($riwayatPemeriksaan ?? []) > 0)
                <div class="mt-4 sm:mt-6 rounded-lg shadow p-3 sm:p-6"
                     x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 sm:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">📋 Riwayat Pemeriksaan Pasien</h3>
                        <div class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                            Menampilkan {{ count($riwayatPemeriksaan) }} dari {{ $totalHistory }} record
                            @if($totalHistory > $historyPerPage)
                                - Halaman {{ $historyPage }} dari {{ ceil($totalHistory / $historyPerPage) }}
                            @endif
                        </div>
                    </div>

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
                                        <span class="font-medium px-2 py-1 rounded text-xs" x-bind:class="darkMode ? 'bg-blue-900 text-blue-200 border border-blue-700' : 'bg-blue-100 text-blue-800 border border-blue-200'">
                                            📋 {{ $item['no_rawat'] }}
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

                    {{-- History Pagination Controls --}}
                    @if($totalHistory > $historyPerPage)
                        <div class="flex justify-center items-center gap-2 mt-6">
                            {{-- Previous Button --}}
                            <button
                                wire:click="previousHistoryPage"
                                {{ $historyPage <= 1 ? 'disabled' : '' }}
                                class="px-3 py-2 rounded-md border text-sm font-medium transition-colors {{ $historyPage <= 1 ? 'cursor-not-allowed' : '' }}"
                                x-bind:class="darkMode ?
                                    '{{ $historyPage <= 1 ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-700 border-gray-600 text-gray-200 hover:bg-gray-600' }}' :
                                    '{{ $historyPage <= 1 ? 'bg-gray-100 border-gray-300 text-gray-400' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}'">
                                ← Sebelumnya
                            </button>

                            {{-- Page Numbers --}}
                            @php
                                $maxHistoryPage = ceil($totalHistory / $historyPerPage);
                                $startHistoryPage = max(1, $historyPage - 2);
                                $endHistoryPage = min($maxHistoryPage, $historyPage + 2);
                            @endphp

                            @for($i = $startHistoryPage; $i <= $endHistoryPage; $i++)
                                <button
                                    wire:click="goToHistoryPage({{ $i }})"
                                    class="px-3 py-2 rounded-md border text-sm font-medium transition-colors"
                                    x-bind:class="darkMode ?
                                        '{{ $i == $historyPage ? 'bg-blue-600 border-blue-600 text-white' : 'bg-gray-700 border-gray-600 text-gray-200 hover:bg-gray-600' }}' :
                                        '{{ $i == $historyPage ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}'">
                                    {{ $i }}
                                </button>
                            @endfor

                            {{-- Next Button --}}
                            <button
                                wire:click="nextHistoryPage"
                                {{ $historyPage >= $maxHistoryPage ? 'disabled' : '' }}
                                class="px-3 py-2 rounded-md border text-sm font-medium transition-colors {{ $historyPage >= $maxHistoryPage ? 'cursor-not-allowed' : '' }}"
                                x-bind:class="darkMode ?
                                    '{{ $historyPage >= ceil($totalHistory / $historyPerPage) ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-700 border-gray-600 text-gray-200 hover:bg-gray-600' }}' :
                                    '{{ $historyPage >= ceil($totalHistory / $historyPerPage) ? 'bg-gray-100 border-gray-300 text-gray-400' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}'">
                                Selanjutnya →
                            </button>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- Template Modal --}}
    @if($showTemplateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="max-w-4xl w-full max-h-[90vh] overflow-y-auto rounded-lg shadow-xl"
                 x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">

                {{-- Modal Header --}}
                <div class="p-4 sm:p-6 border-b" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                            📝 Pilih Template SOAPIE
                        </h3>
                        <button
                            wire:click="closeTemplateModal"
                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                            x-bind:class="darkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-4 sm:p-6">
                    {{-- Create New Template Button --}}
                    <div class="mb-4 flex justify-end">
                        @if(!$showCreateTemplate)
                            <x-filament::button
                                type="button"
                                color="success"
                                size="sm"
                                wire:click="showCreateTemplateForm">
                                ➕ Buat Template Baru
                            </x-filament::button>
                        @endif
                    </div>

                    {{-- Create Template Form with SOAPIE Inputs --}}
                    @if($showCreateTemplate)
                        <div class="border rounded-lg p-4 mb-6"
                             x-bind:class="darkMode ? 'border-green-600 bg-green-900/20' : 'border-green-200 bg-green-50'">
                            <h4 class="font-medium mb-4" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                                ➕ Buat Template Baru
                            </h4>

                            {{-- Template Info --}}
                            <div class="space-y-3 mb-4">
                                <div>
                                    <input type="text" wire:model="newTemplateName" placeholder="📝 Nama template (contoh: Hipertensi Kontrol)"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                           x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'">
                                    @error('newTemplateName')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex gap-3">
                                    <input type="text" wire:model="newTemplateCategory" placeholder="🏷️ Kategori (contoh: Kardiovaskular, Endokrin, dll)"
                                           class="flex-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                           x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'">

                                    @if($isAdmin)
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" wire:model="newTemplateIsPublic" class="rounded border-gray-300 text-green-600">
                                            <span x-bind:class="darkMode ? 'text-white' : 'text-gray-700'">🌐 Public</span>
                                        </label>
                                    @endif
                                </div>
                            </div>

                            {{-- SOAPIE Input Fields --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- Subjective --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-white' : 'text-gray-700'">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white rounded-full text-xs font-bold mr-2">S</span>
                                        Subjective
                                    </label>
                                    <textarea wire:model="newTemplateSubjective" rows="3" placeholder="Keluhan pasien..."
                                              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'"></textarea>
                                </div>

                                {{-- Objective --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-white' : 'text-gray-700'">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs font-bold mr-2">O</span>
                                        Objective
                                    </label>
                                    <textarea wire:model="newTemplateObjective" rows="3" placeholder="Hasil pemeriksaan..."
                                              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'"></textarea>
                                </div>

                                {{-- Assessment --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-white' : 'text-gray-700'">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white rounded-full text-xs font-bold mr-2">A</span>
                                        Assessment
                                    </label>
                                    <textarea wire:model="newTemplateAssessment" rows="3" placeholder="Diagnosis..."
                                              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'"></textarea>
                                </div>

                                {{-- Plan --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-white' : 'text-gray-700'">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-500 text-white rounded-full text-xs font-bold mr-2">P</span>
                                        Plan
                                    </label>
                                    <textarea wire:model="newTemplatePlan" rows="3" placeholder="Rencana pengobatan..."
                                              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'"></textarea>
                                </div>

                                {{-- Intervention --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-white' : 'text-gray-700'">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-500 text-white rounded-full text-xs font-bold mr-2">I</span>
                                        Intervention
                                    </label>
                                    <textarea wire:model="newTemplateIntervention" rows="3" placeholder="Instruksi/tindakan..."
                                              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'"></textarea>
                                </div>

                                {{-- Evaluation --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1" x-bind:class="darkMode ? 'text-white' : 'text-gray-700'">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-teal-500 text-white rounded-full text-xs font-bold mr-2">E</span>
                                        Evaluation
                                    </label>
                                    <textarea wire:model="newTemplateEvaluation" rows="3" placeholder="Evaluasi hasil..."
                                              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              x-bind:class="darkMode ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400' : 'border-gray-300 bg-white text-gray-900'"></textarea>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex gap-3 mt-4">
                                <x-filament::button type="button" color="success" size="sm" wire:click="saveNewTemplate" class="flex-1">
                                    💾 Simpan Template
                                </x-filament::button>
                                <x-filament::button type="button" color="gray" size="sm" wire:click="hideCreateTemplateForm">
                                    ❌ Batal
                                </x-filament::button>
                            </div>
                        </div>
                    @endif

                    @if(count($soapieTemplates) > 0)
                        {{-- Pagination Info --}}
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                Menampilkan {{ count($soapieTemplates) }} dari {{ $totalTemplates }} template
                            </p>
                            <div class="text-sm" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                Halaman {{ $templatePage }} dari {{ ceil($totalTemplates / $templatePerPage) }}
                            </div>
                        </div>

                        <div class="grid gap-4">
                            @foreach($soapieTemplates as $template)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                                     x-bind:class="darkMode ? 'border-gray-600 hover:border-gray-500 bg-gray-700' : 'border-gray-200 hover:border-gray-300 bg-gray-50'"
                                     wire:click="applyTemplate({{ $template['id'] }})">

                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-medium" x-bind:class="darkMode ? 'text-white' : 'text-gray-900'">
                                                {{ $template['nama_template'] }}
                                            </h4>
                                            @if($template['kategori'])
                                                <span class="inline-block px-2 py-1 text-xs rounded-full mt-1"
                                                      x-bind:class="darkMode ? 'bg-blue-900 text-blue-200' : 'bg-blue-100 text-blue-800'">
                                                    {{ $template['kategori'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-xs" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                                @if($template['is_public'])
                                                    🌐 Public
                                                @else
                                                    👤 Private
                                                @endif
                                            </div>
                                            @if(($template['nip'] === (auth()->user()->pegawai->nik ?? auth()->user()->username ?? '-')) || $isAdmin)
                                                <button
                                                    wire:click.stop="deleteTemplate({{ $template['id'] }})"
                                                    onclick="confirm('Yakin hapus template {{ $template['nama_template'] }}?') || event.stopImmediatePropagation()"
                                                    class="text-red-500 hover:text-red-700 text-xs p-1">
                                                    🗑️
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if($template['keterangan'])
                                        <p class="text-sm mb-3" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                                            {{ $template['keterangan'] }}
                                        </p>
                                    @endif

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                                        @if($template['subjective'])
                                            <div>
                                                <strong x-bind:class="darkMode ? 'text-green-400' : 'text-green-600'">S:</strong>
                                                <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'" class="truncate">{{ Str::limit($template['subjective'], 50) }}</p>
                                            </div>
                                        @endif
                                        @if($template['objective'])
                                            <div>
                                                <strong x-bind:class="darkMode ? 'text-blue-400' : 'text-blue-600'">O:</strong>
                                                <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'" class="truncate">{{ Str::limit($template['objective'], 50) }}</p>
                                            </div>
                                        @endif
                                        @if($template['assessment'])
                                            <div>
                                                <strong x-bind:class="darkMode ? 'text-orange-400' : 'text-orange-600'">A:</strong>
                                                <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'" class="truncate">{{ Str::limit($template['assessment'], 50) }}</p>
                                            </div>
                                        @endif
                                        @if($template['plan'])
                                            <div>
                                                <strong x-bind:class="darkMode ? 'text-purple-400' : 'text-purple-600'">P:</strong>
                                                <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'" class="truncate">{{ Str::limit($template['plan'], 50) }}</p>
                                            </div>
                                        @endif
                                        @if($template['intervention'])
                                            <div>
                                                <strong x-bind:class="darkMode ? 'text-indigo-400' : 'text-indigo-600'">I:</strong>
                                                <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'" class="truncate">{{ Str::limit($template['intervention'], 50) }}</p>
                                            </div>
                                        @endif
                                        @if($template['evaluation'])
                                            <div>
                                                <strong x-bind:class="darkMode ? 'text-teal-400' : 'text-teal-600'">E:</strong>
                                                <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'" class="truncate">{{ Str::limit($template['evaluation'], 50) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination Controls --}}
                        @if($totalTemplates > $templatePerPage)
                            <div class="flex justify-center items-center gap-2 mt-6">
                                {{-- Previous Button --}}
                                <button
                                    wire:click="previousTemplatePage"
                                    {{ $templatePage <= 1 ? 'disabled' : '' }}
                                    class="px-3 py-2 rounded-md border text-sm font-medium transition-colors {{ $templatePage <= 1 ? 'cursor-not-allowed' : '' }}"
                                    x-bind:class="darkMode ?
                                        '{{ $templatePage <= 1 ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-700 border-gray-600 text-gray-200 hover:bg-gray-600' }}' :
                                        '{{ $templatePage <= 1 ? 'bg-gray-100 border-gray-300 text-gray-400' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}'">
                                    ← Sebelumnya
                                </button>

                                {{-- Page Numbers --}}
                                @php
                                    $maxPage = ceil($totalTemplates / $templatePerPage);
                                    $startPage = max(1, $templatePage - 2);
                                    $endPage = min($maxPage, $templatePage + 2);
                                @endphp

                                @for($i = $startPage; $i <= $endPage; $i++)
                                    <button
                                        wire:click="goToTemplatePage({{ $i }})"
                                        class="px-3 py-2 rounded-md border text-sm font-medium transition-colors"
                                        x-bind:class="darkMode ?
                                            '{{ $i == $templatePage ? 'bg-blue-600 border-blue-600 text-white' : 'bg-gray-700 border-gray-600 text-gray-200 hover:bg-gray-600' }}' :
                                            '{{ $i == $templatePage ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}'">
                                        {{ $i }}
                                    </button>
                                @endfor

                                {{-- Next Button --}}
                                <button
                                    wire:click="nextTemplatePage"
                                    {{ $templatePage >= $maxPage ? 'disabled' : '' }}
                                    class="px-3 py-2 rounded-md border text-sm font-medium transition-colors {{ $templatePage >= $maxPage ? 'cursor-not-allowed' : '' }}"
                                    x-bind:class="darkMode ?
                                        '{{ $templatePage >= ceil($totalTemplates / $templatePerPage) ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-700 border-gray-600 text-gray-200 hover:bg-gray-600' }}' :
                                        '{{ $templatePage >= ceil($totalTemplates / $templatePerPage) ? 'bg-gray-100 border-gray-300 text-gray-400' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}'">
                                    Selanjutnya →
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p x-bind:class="darkMode ? 'text-white' : 'text-gray-600'">
                                Belum ada template yang tersedia.
                            </p>
                            <p class="text-sm mt-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-500'">
                                Template akan muncul setelah Anda atau admin membuat template SOAPIE.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 sm:p-6 border-t" x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                    <div class="flex justify-end">
                        <x-filament::button
                            type="button"
                            color="gray"
                            wire:click="closeTemplateModal">
                            Tutup
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>