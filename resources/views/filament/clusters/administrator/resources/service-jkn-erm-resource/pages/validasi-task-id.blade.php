<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    {{-- Filter Form --}}
    <div class="rounded-lg shadow p-6 mb-6"
         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
        <div class="mb-4">
            <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Filter Tanggal</h3>
            <p class="text-sm mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">Pilih rentang tanggal untuk validasi Task ID 3-7</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Dari Tanggal
                </label>
                <input
                    type="date"
                    wire:model="dariTanggal"
                    x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"
                />
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Sampai Tanggal
                </label>
                <input
                    type="date"
                    wire:model="sampaiTanggal"
                    x-bind:class="darkMode ? 'w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-100 focus:ring-blue-500 focus:border-blue-500' : 'w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500'"
                />
            </div>

            <div class="flex items-end">
                <x-filament::button
                    wire:click="validateTaskIds"
                    class="w-full"
                    icon="heroicon-o-magnifying-glass"
                >
                    Validasi Sekarang
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- Update Database Button --}}
    @if($hasValidated && count($this->validationResults) > 0)
    <div class="mb-6 rounded-lg shadow p-6"
         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        Update Database
                    </h3>
                    @if($dataExported)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400">
                            ✓ Sudah Diupdate
                        </span>
                    @endif
                </div>
                <p class="text-sm mb-4" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                    {{ $this->pagination['total'] }} data siap diupdate ke database. Desktop app akan mengambil data terbaru dari database untuk dikirim ke BPJS.
                </p>
                <div class="flex gap-3">
                    <x-filament::button
                        wire:click="updateTaskIds"
                        wire:confirm="Apakah Anda yakin ingin mengupdate {{ $this->pagination['total'] }} data ke database? Proses ini akan mengubah waktu di tabel pemeriksaan_ralan, resep_obat, dan mutasi_berkas."
                        color="success"
                        icon="heroicon-o-check-circle"
                        size="lg"
                    >
                        Update {{ $this->pagination['total'] }} Data ke Database
                    </x-filament::button>
                    <x-filament::button
                        wire:click="$set('hasValidated', false)"
                        color="gray"
                        icon="heroicon-o-arrow-path"
                        size="lg"
                        outlined
                    >
                        Reset & Validasi Ulang
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Statistics Cards --}}
    @if($hasValidated)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="rounded-lg shadow p-6 text-center"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
            <div class="text-4xl font-bold" x-bind:class="darkMode ? 'text-white' : 'text-warning-600'">
                {{ $summary['totalFixed'] }}
            </div>
            <div class="text-sm mt-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-600'">
                Data Perlu Diperbaiki
            </div>
        </div>

        <div class="rounded-lg shadow p-6 text-center"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
            <div class="text-4xl font-bold" x-bind:class="darkMode ? 'text-white' : 'text-success-600'">
                {{ $summary['totalOk'] }}
            </div>
            <div class="text-sm mt-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-600'">
                Data Sudah Benar
            </div>
        </div>

        <div class="rounded-lg shadow p-6 text-center"
             x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
            <div class="text-4xl font-bold" x-bind:class="darkMode ? 'text-white' : 'text-primary-600'">
                {{ $summary['totalProcessed'] }}
            </div>
            <div class="text-sm mt-2" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-600'">
                Total Data Diproses
            </div>
        </div>
    </div>
    @endif

    {{-- Detail Validasi --}}
    @if($hasValidated && count($this->validationResults) > 0)
    <div class="rounded-lg shadow p-6 mb-6"
         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Detail Data yang Perlu Diperbaiki</h3>
                <p class="text-sm mt-1" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                    Menampilkan {{ $this->pagination['from'] }} - {{ $this->pagination['to'] }} dari {{ $this->pagination['total'] }} data
                    ({{ count($this->paginatedValidationResults) }} items on this page)
                </p>
            </div>
        </div>

        <div class="space-y-6">
            @foreach($this->paginatedValidationResults as $index => $result)
            <div class="rounded-lg p-6 space-y-4 @if($result['has_issues']) border-2 @else border @endif"
                 x-bind:class="darkMode ?
                    '{{ $result['has_issues'] ? 'bg-warning-900/10 border-warning-600' : 'bg-success-900/10 border-success-600' }}' :
                    '{{ $result['has_issues'] ? 'bg-warning-50 border-warning-500' : 'bg-success-50 border-success-500' }}'">
                {{-- Header --}}
                <div class="flex items-start justify-between pb-4"
                     x-bind:class="darkMode ? 'border-b border-gray-600' : 'border-b border-gray-200'">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="text-lg font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                {{ $this->pagination['from'] + $index }}. {{ $result['pasien'] }}
                            </div>
                            @if($result['has_issues'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-200">
                                    ⚠ Perlu Diperbaiki
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-200">
                                    ✓ Sudah Benar
                                </span>
                            @endif
                        </div>
                        <div class="text-sm mt-1 space-x-4" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                            <span>No. Rawat: <strong class="font-mono" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $result['no_rawat'] }}</strong></span>
                            <span>No. RM: <strong class="font-mono" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">{{ $result['no_rkm_medis'] }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Issues - hanya tampil jika ada masalah --}}
                @if($result['has_issues'])
                <div class="border-l-4 border-warning-600 p-4 rounded"
                     x-bind:class="darkMode ? 'bg-warning-900/20' : 'bg-warning-50'">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5" x-bind:class="darkMode ? 'text-warning-200' : 'text-warning-600'" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-warning-800'">
                                Masalah yang Ditemukan
                            </h3>
                            <div class="mt-2 text-sm" x-bind:class="darkMode ? 'text-gray-200' : 'text-warning-700'">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($result['issues'] as $issue)
                                    <li>{{ $issue }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Timeline Table --}}
                <div>
                    <h4 class="text-sm font-medium mb-3" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                        Timeline Task ID (Setelah Perbaikan)
                    </h4>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y"
                               x-bind:class="darkMode ? 'divide-gray-700' : 'divide-gray-200'">
                            <thead x-bind:class="darkMode ? 'bg-gray-800' : 'bg-gray-100'">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                        Task
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                        Deskripsi
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                        Waktu Asli
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                        Waktu Perbaikan
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody x-bind:class="darkMode ? 'bg-gray-900 divide-y divide-gray-700' : 'bg-white divide-y divide-gray-200'">
                                @foreach([3, 4, 5, 6, 7] as $taskNum)
                                    @if(isset($result['tasks'][$taskNum]))
                                    @php
                                        $hasOriginal = isset($result['originalTasks'][$taskNum]) && $result['originalTasks'][$taskNum];
                                        $isChanged = $hasOriginal && $result['tasks'][$taskNum]->ne($result['originalTasks'][$taskNum]);
                                        $isAutoFilled = !$hasOriginal;

                                        $taskName = match($taskNum) {
                                            3 => 'Registrasi (Jam Reg)',
                                            4 => 'Mulai Pelayanan',
                                            5 => 'Selesai Pelayanan',
                                            6 => 'Resep Dibuat',
                                            7 => 'Obat Diserahkan',
                                            default => 'Task ' . $taskNum
                                        };
                                    @endphp
                                    <tr x-bind:class="darkMode ? 'hover:bg-gray-800/50' : 'hover:bg-gray-50'">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                @if($isChanged)
                                                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-warning-500 text-white text-xs font-bold">
                                                        {{ $taskNum }}
                                                    </span>
                                                @elseif($isAutoFilled)
                                                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-info-500 text-white text-xs font-bold">
                                                        {{ $taskNum }}
                                                    </span>
                                                @else
                                                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-success-500 text-white text-xs font-bold">
                                                        {{ $taskNum }}
                                                    </span>
                                                @endif
                                                <span class="text-sm font-medium" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Task {{ $taskNum }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                            {{ $taskName }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-600'">
                                            @if($hasOriginal)
                                                <span class="font-mono">{{ $result['originalTasks'][$taskNum]->format('H:i:s') }}</span>
                                            @else
                                                <span class="italic" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-400'">Kosong</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <span class="font-mono font-semibold" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">
                                                {{ $result['tasks'][$taskNum]->format('H:i:s') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($isChanged)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                      x-bind:class="darkMode ? 'bg-warning-900/30 text-warning-200' : 'bg-warning-100 text-warning-800'">
                                                    Diperbaiki
                                                </span>
                                            @elseif($isAutoFilled)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                      x-bind:class="darkMode ? 'bg-info-900/30 text-info-200' : 'bg-info-100 text-info-800'">
                                                    Auto +5 mnt
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                      x-bind:class="darkMode ? 'bg-success-900/30 text-success-200' : 'bg-success-100 text-success-800'">
                                                    OK
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Info Validasi SEP Mobile JKN (jika ada) --}}
                    @if(isset($result['tasks']['3_sep']) && $result['tasks']['3_sep'])
                    <div class="mt-3 px-4 py-2 rounded-lg border"
                         x-bind:class="darkMode ? 'bg-blue-900/20 border-blue-700' : 'bg-blue-50 border-blue-300'">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" x-bind:class="darkMode ? 'text-blue-400' : 'text-blue-600'" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span x-bind:class="darkMode ? 'text-blue-300' : 'text-blue-700'">
                                <strong>Validasi SEP Mobile JKN:</strong> {{ $result['tasks']['3_sep']->format('H:i:s') }}
                                <span class="text-xs opacity-75">(tidak diupdate, hanya info)</span>
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($this->pagination['totalPages'] > 1)
        <div class="mt-6 flex items-center justify-between border-t pt-4"
             x-bind:class="darkMode ? 'border-gray-700' : 'border-gray-200'">
            <div class="flex items-center gap-2">
                @if($this->pagination['currentPage'] <= 1)
                    <button
                        disabled
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-not-allowed"
                        x-bind:class="darkMode ? 'bg-gray-700 text-gray-500' : 'bg-gray-100 text-gray-400'"
                    >
                        ← Sebelumnya
                    </button>
                @else
                    <button
                        wire:click="previousPage"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        x-bind:class="darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
                    >
                        ← Sebelumnya
                    </button>
                @endif

                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= $this->pagination['totalPages']; $i++)
                        @if(
                            $i == 1 ||
                            $i == $this->pagination['totalPages'] ||
                            ($i >= $this->pagination['currentPage'] - 2 && $i <= $this->pagination['currentPage'] + 2)
                        )
                            @if($i == $this->pagination['currentPage'])
                                <button
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-colors min-w-[40px] bg-primary-600 text-white"
                                >
                                    {{ $i }}
                                </button>
                            @else
                                <button
                                    wire:click="goToPage({{ $i }})"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-colors min-w-[40px]"
                                    x-bind:class="darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
                                >
                                    {{ $i }}
                                </button>
                            @endif
                        @elseif(
                            $i == $this->pagination['currentPage'] - 3 ||
                            $i == $this->pagination['currentPage'] + 3
                        )
                            <span class="px-2" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-500'">...</span>
                        @endif
                    @endfor
                </div>

                @if($this->pagination['currentPage'] >= $this->pagination['totalPages'])
                    <button
                        disabled
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-not-allowed"
                        x-bind:class="darkMode ? 'bg-gray-700 text-gray-500' : 'bg-gray-100 text-gray-400'"
                    >
                        Selanjutnya →
                    </button>
                @else
                    <button
                        wire:click="nextPage"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        x-bind:class="darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'"
                    >
                        Selanjutnya →
                    </button>
                @endif
            </div>

            <div class="text-sm" x-bind:class="darkMode ? 'text-gray-400' : 'text-gray-600'">
                Halaman {{ $this->pagination['currentPage'] }} dari {{ $this->pagination['totalPages'] }}
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Info Footer --}}
    @if($hasValidated)
    <div class="rounded-lg shadow p-6"
         x-bind:class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
        <h3 class="text-lg font-semibold mb-4" x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Keterangan</h3>

        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-warning-500 text-white text-xs font-bold flex-shrink-0">
                    !
                </span>
                <div class="text-sm" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-600'">
                    <strong x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Diperbaiki:</strong> Task dengan waktu yang diperbaiki karena urutan tidak benar
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-info-500 text-white text-xs font-bold flex-shrink-0">
                    +
                </span>
                <div class="text-sm" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-600'">
                    <strong x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">Auto +5 menit:</strong> Task yang kosong dan diisi otomatis (+5 menit dari task sebelumnya)
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-success-500 text-white text-xs font-bold flex-shrink-0">
                    ✓
                </span>
                <div class="text-sm" x-bind:class="darkMode ? 'text-gray-200' : 'text-gray-600'">
                    <strong x-bind:class="darkMode ? 'text-gray-100' : 'text-gray-900'">OK:</strong> Task dengan waktu yang sudah benar
                </div>
            </div>

            <div class="mt-4 p-4 border rounded-lg"
                 x-bind:class="darkMode ? 'bg-info-900/20 border-info-700' : 'bg-info-50 border-info-200'">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5" x-bind:class="darkMode ? 'text-info-200' : 'text-info-600'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm" x-bind:class="darkMode ? 'text-gray-100' : 'text-info-800'">
                            <strong>Catatan:</strong> Validasi ini menampilkan hasil perbaikan waktu Task ID.
                            Setelah klik tombol Update, waktu akan <strong>diubah di database</strong> (pemeriksaan_ralan, resep_obat, mutasi_berkas).
                            Desktop app akan mengambil data terbaru dari database untuk dikirim ke BPJS API.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    </div>
</x-filament-panels::page>
