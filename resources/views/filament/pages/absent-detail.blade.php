<div class="space-y-6">
    <!-- Informasi Pegawai -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Absensi</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @if(auth()->user()->can('view_all_absent'))
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Pegawai</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->employee->name ?? 'N/A' }}</p>
            </div>
            @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $record->date ? $record->date->format('d/m/Y') : 'N/A' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Masuk</label>
                <p class="mt-1">
                    @if($record->check_in)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ date('H:i', strtotime($record->check_in)) }}
                        </span>
                    @else
                        <span class="text-gray-400">Belum absen masuk</span>
                    @endif
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Pulang</label>
                <p class="mt-1">
                    @if($record->check_out)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ date('H:i', strtotime($record->check_out)) }}
                        </span>
                    @else
                        <span class="text-gray-400">Belum absen pulang</span>
                    @endif
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Jam Kerja</label>
                <p class="mt-1">
                    @if($record->total_working_hours)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $record->total_working_hours }}
                        </span>
                    @else
                        <span class="text-gray-400">Belum selesai</span>
                    @endif
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                <p class="mt-1">
                    @php
                        $statusColors = [
                            'hadir' => 'bg-green-100 text-green-800',
                            'terlambat' => 'bg-yellow-100 text-yellow-800', 
                            'izin' => 'bg-blue-100 text-blue-800',
                            'tidak_hadir' => 'bg-red-100 text-red-800'
                        ];
                        $statusLabels = [
                            'hadir' => 'Hadir',
                            'tidak_hadir' => 'Tidak Hadir',
                            'terlambat' => 'Terlambat',
                            'izin' => 'Izin'
                        ];
                        $statusClass = $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800';
                        $statusLabel = $statusLabels[$record->status] ?? ucfirst($record->status);
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Foto Absensi -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Foto Absensi</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Masuk</label>
                @if($record->check_in_photo)
                    <img src="{{ Storage::disk('public')->url($record->check_in_photo) }}" 
                         alt="Foto Masuk" 
                         class="w-48 h-48 object-cover rounded-lg border border-gray-200">
                @else
                    <div class="w-48 h-48 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Tidak ada foto</span>
                    </div>
                @endif
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Foto Pulang</label>
                @if($record->check_out_photo)
                    <img src="{{ Storage::disk('public')->url($record->check_out_photo) }}" 
                         alt="Foto Pulang" 
                         class="w-48 h-48 object-cover rounded-lg border border-gray-200">
                @else
                    <div class="w-48 h-48 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Tidak ada foto</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Catatan -->
    @if($record->notes)
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Catatan</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->notes }}</p>
    </div>
    @endif
</div>