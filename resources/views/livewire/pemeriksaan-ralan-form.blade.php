<div>
    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 32px;">
        <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 16px; color: #111827;">Input Pemeriksaan Baru</h3>
        
        <form wire:submit="simpan" style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Tanggal dan Jam -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Tanggal Pemeriksaan</label>
                    <input type="date" wire:model="tgl_perawatan" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Jam Pemeriksaan</label>
                    <input type="time" wire:model="jam_rawat" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;" required>
                </div>
            </div>
            
            <!-- Tanda Vital -->
            <div>
                <h4 style="font-size: 16px; font-weight: 500; color: #111827; margin-bottom: 12px;">Tanda Vital</h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Suhu Tubuh (°C)</label>
                        <input type="number" step="0.1" wire:model="suhu_tubuh" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Tensi (mmHg)</label>
                        <input type="text" wire:model="tensi" placeholder="120/80" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Nadi (x/menit)</label>
                        <input type="number" wire:model="nadi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Respirasi (x/menit)</label>
                        <input type="number" wire:model="respirasi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Tinggi Badan (cm)</label>
                        <input type="number" step="0.1" wire:model="tinggi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Berat Badan (kg)</label>
                        <input type="number" step="0.1" wire:model="berat" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">SpO2 (%)</label>
                        <input type="number" step="0.1" wire:model="spo2" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">GCS</label>
                        <input type="text" wire:model="gcs" placeholder="E4V5M6" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Kesadaran</label>
                        <select wire:model="kesadaran" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                            <option value="">Pilih Kesadaran</option>
                            <option value="Compos Mentis">Compos Mentis</option>
                            <option value="Somnolence">Somnolence</option>
                            <option value="Sopor">Sopor</option>
                            <option value="Coma">Coma</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Lingkar Perut (cm)</label>
                        <input type="number" step="0.1" wire:model="lingkar_perut" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>
                </div>
            </div>
            
            <!-- Keluhan dan Pemeriksaan -->
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Keluhan</label>
                    <textarea wire:model="keluhan" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Pemeriksaan Fisik</label>
                    <textarea wire:model="pemeriksaan" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Alergi</label>
                    <input type="text" wire:model="alergi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>
                
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Penilaian</label>
                    <textarea wire:model="penilaian" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">RTL (Rencana Tindak Lanjut)</label>
                    <textarea wire:model="rtl" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Instruksi</label>
                    <textarea wire:model="instruksi" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Evaluasi</label>
                    <textarea wire:model="evaluasi" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" style="background-color: #2563eb; color: white; font-weight: 500; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                    Simpan Pemeriksaan
                </button>
            </div>
        </form>
    </div>
    
    <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 16px; color: #111827;">Riwayat Pemeriksaan</h3>
        
        @if($this->pemeriksaan && $this->pemeriksaan->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f9fafb;">
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Tanggal</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Jam</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Suhu</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Tensi</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Nadi</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Keluhan</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Petugas</th>
                            <th style="padding: 12px 24px; text-align: left; font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="background: white;">
                        @foreach($this->pemeriksaan as $item)
                            <tr style="border-bottom: 1px solid #e5e7eb;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; color: #111827;">
                                    {{ $item->tgl_perawatan ? $item->tgl_perawatan->format('d/m/Y') : '-' }}
                                </td>
                                <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; color: #111827;">
                                    {{ $item->jam_rawat ?? '-' }}
                                </td>
                                <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; color: #111827;">
                                    {{ $item->suhu_tubuh ? $item->suhu_tubuh . '°C' : '-' }}
                                </td>
                                <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; color: #111827;">
                                    {{ $item->tensi ? $item->tensi . ' mmHg' : '-' }}
                                </td>
                                <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; color: #111827;">
                                    {{ $item->nadi ? $item->nadi . ' x/mnt' : '-' }}
                                </td>
                                <td style="padding: 16px 24px; font-size: 14px; color: #111827; max-width: 300px; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->keluhan }}">
                                    {{ Str::limit($item->keluhan ?? '-', 50) }}
                                </td>
                                <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; color: #111827;">
                                    {{ $item->petugas->nama ?? 'Unknown' }}
                                </td>
                                <td style="padding: 16px 24px; white-space: nowrap; font-size: 14px; color: #111827;">
                                    <button 
                                        wire:click="hapus('{{ $item->tgl_perawatan->format('Y-m-d') }}', '{{ $item->jam_rawat }}')"
                                        wire:confirm="Yakin ingin menghapus data pemeriksaan ini?"
                                        style="color: #dc2626; font-weight: 500; border: none; background: none; cursor: pointer; padding: 4px 8px; border-radius: 4px;"
                                        onmouseover="this.style.color='#991b1b'; this.style.backgroundColor='#fef2f2';" 
                                        onmouseout="this.style.color='#dc2626'; this.style.backgroundColor='transparent';"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 32px 0;">
                <div style="color: #6b7280; font-size: 18px;">Belum ada data pemeriksaan</div>
                <div style="color: #9ca3af; font-size: 14px; margin-top: 8px;">Silakan input pemeriksaan baru menggunakan form di atas</div>
            </div>
        @endif
    </div>
</div>