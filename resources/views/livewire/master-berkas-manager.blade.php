<div style="max-width: 100%;">
    <div style="max-height: 300px; overflow: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 12px;" class="bg-white dark:bg-gray-900">
            <thead>
                <tr style="padding: 4px 6px; font-weight: 600;" class="bg-gray-50 dark:bg-gray-800">
                    <th style="border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; font-weight: 600;" class="dark:border-gray-700 text-gray-900 dark:text-gray-100">Kode</th>
                    <th style="border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; font-weight: 600;" class="dark:border-gray-700 text-gray-900 dark:text-gray-100">Nama Berkas</th>
                    <th style="border: 1px solid #d1d5db; padding: 4px 6px; text-align: center; font-weight: 600; width: 60px;" class="dark:border-gray-700 text-gray-900 dark:text-gray-100">Status</th>
                    <th style="border: 1px solid #d1d5db; padding: 4px 6px; text-align: center; font-weight: 600; width: 50px;" class="dark:border-gray-700 text-gray-900 dark:text-gray-100">Hapus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($masterBerkas as $item)
                <tr style="border-bottom: 1px solid #e5e7eb;" class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700" onmouseover="this.style.backgroundColor=document.documentElement.classList.contains('dark') ? 'rgb(31, 41, 55)' : 'rgb(249, 250, 251)'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="border: 1px solid #d1d5db; padding: 4px 6px; font-size: 12px;" class="dark:border-gray-700 text-gray-900 dark:text-gray-100">{{ $item['kode'] }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 4px 6px; font-size: 12px;" class="dark:border-gray-700 text-gray-900 dark:text-gray-100" title="{{ $item['kategori'] }}">{{ $item['nama_berkas'] }}</td>
                    <td style="border: 1px solid #d1d5db; padding: 4px 6px; text-align: center; font-size: 12px;" class="dark:border-gray-700">
                        @if($item['in_use'])
                            <span style="font-size: 11px;" class="text-red-600 dark:text-red-400">🔒 {{ $item['usage_count'] }}</span>
                        @else
                            <span style="font-size: 11px;" class="text-green-600 dark:text-green-400">✅</span>
                        @endif
                    </td>
                    <td style="border: 1px solid #d1d5db; padding: 4px 6px; text-align: center;" class="dark:border-gray-700">
                        @if($item['in_use'])
                            <span style="font-size: 12px;" class="text-gray-400 dark:text-gray-500">-</span>
                        @else
                            <button 
                                wire:click="deleteMasterBerkas('{{ $item['kode'] }}')"
                                wire:confirm="Hapus {{ $item['kode'] }} - {{ $item['nama_berkas'] }}?"
                                style="padding: 2px 6px; background: #dc2626; color: white; font-size: 11px; border: none; border-radius: 3px; cursor: pointer;"
                                onmouseover="this.style.backgroundColor='#b91c1c'"
                                onmouseout="this.style.backgroundColor='#dc2626'"
                                title="Hapus {{ $item['nama_berkas'] }}"
                            >
                                ✕
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if(count($masterBerkas) === 0)
        <div style="text-align: center; padding: 20px; font-size: 12px;" class="text-gray-500 dark:text-gray-400">
            Belum ada data master berkas
        </div>
    @endif
    
    <div style="margin-top: 8px; padding: 4px 8px; border-radius: 4px; font-size: 11px;" class="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300">
        <strong>Info:</strong> 🔒 = digunakan, tidak bisa dihapus • ✅ = bisa dihapus • ✕ = hapus data
    </div>
</div>
