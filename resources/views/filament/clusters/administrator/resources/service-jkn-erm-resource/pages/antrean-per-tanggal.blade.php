<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <x-filament::card>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model="tanggal_awal"
                            label="Tanggal Awal"
                        />
                    </x-filament::input.wrapper>
                    <x-filament::input.label class="mt-1">
                        Tanggal Awal
                    </x-filament::input.label>
                </div>

                <div>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model="tanggal_akhir"
                            label="Tanggal Akhir"
                        />
                    </x-filament::input.wrapper>
                    <x-filament::input.label class="mt-1">
                        Tanggal Akhir
                    </x-filament::input.label>
                </div>

                <div class="flex items-end">
                    <x-filament::button
                        wire:click="fetchAntreanData"
                        class="w-full"
                    >
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2"/>
                        Tampilkan Data
                    </x-filament::button>
                </div>
            </div>
        </x-filament::card>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <x-filament::card class="text-center">
                <div class="text-2xl font-bold text-primary-600">
                    {{ $statistics['total'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Total Antrean
                </div>
            </x-filament::card>

            <x-filament::card class="text-center">
                <div class="text-2xl font-bold text-success-600">
                    {{ $statistics['jkn'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    JKN
                </div>
            </x-filament::card>

            <x-filament::card class="text-center">
                <div class="text-2xl font-bold text-warning-600">
                    {{ $statistics['non_jkn'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Non JKN
                </div>
            </x-filament::card>

            <x-filament::card class="text-center">
                <div class="text-2xl font-bold text-info-600">
                    {{ $statistics['checkin'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Checkin
                </div>
            </x-filament::card>

            <x-filament::card class="text-center">
                <div class="text-2xl font-bold text-primary-600">
                    {{ $statistics['dilayani'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Dilayani
                </div>
            </x-filament::card>

            <x-filament::card class="text-center">
                <div class="text-2xl font-bold text-success-600">
                    {{ $statistics['selesai'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Selesai
                </div>
            </x-filament::card>

            <x-filament::card class="text-center">
                <div class="text-2xl font-bold text-danger-600">
                    {{ $statistics['batal'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Batal
                </div>
            </x-filament::card>
        </div>

        {{-- Table --}}
        <x-filament::card>
            @if(count($antreanData) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Booking</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Poliklinik</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dokter</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Antrian</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Pasien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Task ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. RM</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($antreanData as $data)
                                <tr>
                                    <td class="px-4 py-3 text-sm">{{ $data['kodebooking'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ isset($data['tanggalperiksa']) ? \Carbon\Carbon::parse($data['tanggalperiksa'])->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $data['namapoli'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $data['namadokter'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $data['nomorantrean'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $jenisPasien = $data['jenispasien'] ?? '';
                                            $badgeClass = match($jenisPasien) {
                                                'JKN' => 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300',
                                                'NON JKN' => 'bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300',
                                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                            {{ $jenisPasien }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $status = $data['status'] ?? '';
                                            $statusClass = match($status) {
                                                'Checkin' => 'bg-info-100 text-info-700 dark:bg-info-900 dark:text-info-300',
                                                'Dipanggil' => 'bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300',
                                                'Dilayani' => 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300',
                                                'Selesai' => 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300',
                                                'Batal' => 'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300',
                                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $data['taskid'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $data['norm'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada data</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan pilih tanggal dan klik "Tampilkan Data"</p>
                </div>
            @endif
        </x-filament::card>
    </div>
</x-filament-panels::page>
