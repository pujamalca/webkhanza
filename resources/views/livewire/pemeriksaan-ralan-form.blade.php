{{-- SOAPIE Form using Universal Livewire System --}}
<x-livewire-layout title="📝 Form Input SOAPIE - WebKhanza">

    <x-filament::section>
        <x-slot name="heading">
            <span class="text-primary-600 dark:text-primary-400">
                Form Pemeriksaan SOAPIE
            </span>
        </x-slot>

        <x-livewire-form wire:submit="simpanPemeriksaan">

            {{-- Test Section --}}
            <div class="bg-blue-100 border border-blue-300 p-4 rounded-lg">
                <h3 class="text-blue-800 font-bold">🧪 Test Tailwind CSS</h3>
                <p class="text-blue-600">Jika background ini biru, berarti Tailwind sudah ter-load dengan Vite!</p>
            </div>

            {{-- Date, Time, Petugas Section --}}
            <x-livewire-section title="📅 Informasi Dasar" class="mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-livewire-field
                        type="date"
                        label="Tanggal"
                        wire:model="tgl_perawatan"
                        required />

                    <x-livewire-field
                        type="time"
                        label="Jam"
                        wire:model="jam_rawat"
                        required />

                    @if($isAdmin && !empty($pegawaiList))
                        <x-livewire-field
                            type="select"
                            label="Petugas"
                            wire:model="nip"
                            placeholder="Pilih Petugas..."
                            :options="$pegawaiList"
                            required />
                    @else
                        <x-livewire-field
                            type="text"
                            label="Petugas"
                            wire:model="nip"
                            readonly
                            required />
                    @endif
                </div>
            </x-livewire-section>

            {{-- SOAPIE Assessment Section --}}
            <x-livewire-section title="📋 SOAPIE Assessment" subtitle="Subjective, Objective, Assessment, Plan, Intervention, Evaluation">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    {{-- Subjective --}}
                    <x-livewire-field
                        type="textarea"
                        wire:model="keluhan"
                        placeholder="Keluhan pasien..."
                        required>
                        <x-slot name="label">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white rounded-full text-xs font-bold mr-2">S</span>
                            Subjective
                        </x-slot>
                    </x-livewire-field>

                    {{-- Objective --}}
                    <x-livewire-field
                        type="textarea"
                        wire:model="pemeriksaan"
                        placeholder="Hasil pemeriksaan..."
                        required>
                        <x-slot name="label">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs font-bold mr-2">O</span>
                            Objective
                        </x-slot>
                    </x-livewire-field>

                    {{-- Assessment --}}
                    <x-livewire-field
                        type="textarea"
                        wire:model="penilaian"
                        placeholder="Diagnosis...">
                        <x-slot name="label">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white rounded-full text-xs font-bold mr-2">A</span>
                            Assessment
                        </x-slot>
                    </x-livewire-field>

                    {{-- Plan --}}
                    <x-livewire-field
                        type="textarea"
                        wire:model="rtl"
                        placeholder="Rencana pengobatan..."
                        required>
                        <x-slot name="label">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-500 text-white rounded-full text-xs font-bold mr-2">P</span>
                            Plan
                        </x-slot>
                    </x-livewire-field>

                    {{-- Intervention --}}
                    <x-livewire-field
                        type="textarea"
                        wire:model="instruksi"
                        placeholder="Instruksi/tindakan...">
                        <x-slot name="label">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-500 text-white rounded-full text-xs font-bold mr-2">I</span>
                            Intervention
                        </x-slot>
                    </x-livewire-field>

                    {{-- Evaluation --}}
                    <x-livewire-field
                        type="textarea"
                        wire:model="evaluasi"
                        placeholder="Evaluasi hasil...">
                        <x-slot name="label">
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-teal-500 text-white rounded-full text-xs font-bold mr-2">E</span>
                            Evaluation
                        </x-slot>
                    </x-livewire-field>

                </div>
            </x-livewire-section>

            {{-- Action Buttons --}}
            <div class="livewire-button-group">
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

        </x-livewire-form>
    </x-filament::section>

    {{-- Debug Section --}}
    <div class="mt-8 p-4 bg-yellow-100 border border-yellow-300 rounded-lg dark:bg-yellow-900 dark:border-yellow-600">
        <h4 class="font-bold text-yellow-800 dark:text-yellow-200 mb-2">🔍 Debug Information</h4>
        <ul class="text-sm space-y-1 text-yellow-700 dark:text-yellow-300">
            <li>✅ Jika background ini kuning, Universal Livewire System bekerja</li>
            <li>✅ Jika form responsive (kolom berubah di mobile), grid bekerja</li>
            <li>✅ Jika input fields berubah background saat dark mode, theme switching bekerja</li>
            <li class="font-semibold text-green-600 dark:text-green-300">
                🌙 Dark Mode Status: <span x-text="darkMode ? 'AKTIF' : 'TIDAK AKTIF'"></span>
            </li>
            <li class="text-xs opacity-75">
                📱 Vite Dev Server: <span class="font-mono">http://localhost:5175</span>
            </li>
            <li class="text-xs opacity-75">
                ⚡ Universal System: <span class="font-mono">@once Vite loading</span>
            </li>
        </ul>
    </div>

    {{-- History Section --}}
    @if(count($riwayatPemeriksaan ?? []) > 0)
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                📋 Riwayat Pemeriksaan ({{ count($riwayatPemeriksaan) }} records)
            </x-slot>

            <div class="space-y-3">
                @foreach($riwayatPemeriksaan as $item)
                    <div class="livewire-card">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex gap-4 items-center text-sm">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    📅 {{ \Carbon\Carbon::parse($item['tgl_perawatan'])->format('d/m/Y') }}
                                </span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    🕐 {{ substr($item['jam_rawat'], 0, 5) }}
                                </span>
                            </div>

                            @if($isAdmin || $item['nip'] === auth()->user()->pegawai->nik ?? auth()->user()->username)
                                <button type="button"
                                        wire:click="editPemeriksaan('{{ $item['tgl_perawatan_raw'] }}', '{{ $item['jam_rawat_raw'] }}')"
                                        class="px-3 py-1 bg-orange-100 text-orange-700 border border-orange-300 rounded-lg hover:bg-orange-200 text-sm">
                                    ✏️ Edit
                                </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div>
                                <strong class="text-green-600">S:</strong>
                                <p class="text-gray-600 dark:text-gray-400">{{ $item['keluhan'] }}</p>
                            </div>
                            <div>
                                <strong class="text-blue-600">O:</strong>
                                <p class="text-gray-600 dark:text-gray-400">{{ $item['pemeriksaan'] }}</p>
                            </div>
                            <div>
                                <strong class="text-orange-600">A:</strong>
                                <p class="text-gray-600 dark:text-gray-400">{{ $item['penilaian'] ?: '-' }}</p>
                            </div>
                            <div>
                                <strong class="text-purple-600">P:</strong>
                                <p class="text-gray-600 dark:text-gray-400">{{ $item['rtl'] }}</p>
                            </div>
                            <div>
                                <strong class="text-indigo-600">I:</strong>
                                <p class="text-gray-600 dark:text-gray-400">{{ $item['instruksi'] ?: '-' }}</p>
                            </div>
                            <div>
                                <strong class="text-teal-600">E:</strong>
                                <p class="text-gray-600 dark:text-gray-400">{{ $item['evaluasi'] ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

</x-livewire-layout>