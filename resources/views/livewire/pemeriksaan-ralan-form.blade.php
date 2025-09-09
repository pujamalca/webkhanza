<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Form Input -->
    <div style="background: #18181b; padding: 24px; margin-bottom: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.3);">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: white;">Input Pemeriksaan Baru</h3>
        
        <form wire:submit="simpan">
            <!-- Tanggal dan Jam -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Tanggal Pemeriksaan</label>
                    <input type="date" wire:model="tgl_perawatan" required style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Jam Pemeriksaan</label>
                    <input type="time" wire:model="jam_rawat" required style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
            </div>

            <!-- Tanda Vital -->
            <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: white;">Tanda Vital</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Suhu Tubuh (°C)</label>
                    <input type="number" step="0.1" wire:model="suhu_tubuh" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Tensi (mmHg)</label>
                    <input type="text" wire:model="tensi" placeholder="120/80" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Nadi (x/menit)</label>
                    <input type="number" wire:model="nadi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Respirasi (x/menit)</label>
                    <input type="number" wire:model="respirasi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Tinggi Badan (cm)</label>
                    <input type="number" step="0.1" wire:model="tinggi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Berat Badan (kg)</label>
                    <input type="number" step="0.1" wire:model="berat" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">SpO2 (%)</label>
                    <input type="number" step="0.1" wire:model="spo2" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">GCS</label>
                    <input type="text" wire:model="gcs" placeholder="E4V5M6" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Kesadaran</label>
                    <select wire:model="kesadaran" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                        <option value="">Pilih Kesadaran</option>
                        <option value="Compos Mentis">Compos Mentis</option>
                        <option value="Somnolence">Somnolence</option>
                        <option value="Sopor">Sopor</option>
                        <option value="Coma">Coma</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Lingkar Perut (cm)</label>
                    <input type="number" step="0.1" wire:model="lingkar_perut" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
            </div>

            <!-- Pemeriksaan Detail -->
            <div style="display: grid; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Keluhan</label>
                    <textarea wire:model="keluhan" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Pemeriksaan Fisik</label>
                    <textarea wire:model="pemeriksaan" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Alergi</label>
                    <input type="text" wire:model="alergi" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Penilaian</label>
                    <textarea wire:model="penilaian" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">RTL (Rencana Tindak Lanjut)</label>
                    <textarea wire:model="rtl" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Instruksi</label>
                    <textarea wire:model="instruksi" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: white; margin-bottom: 8px;">Evaluasi</label>
                    <textarea wire:model="evaluasi" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div style="text-align: right;">
                <button type="submit" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    Simpan Pemeriksaan
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat -->
    <div style="background: #18181b; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.3);">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: white;">Riwayat Pemeriksaan</h3>
        
        @php
            $pemeriksaanData = $this->pemeriksaan ?? collect();
        @endphp
        
        @if($pemeriksaanData->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #27272a;">
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Tanggal</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Jam</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Suhu</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Tensi</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Nadi</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Keluhan</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Petugas</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #d1d5db; text-transform: uppercase; border-bottom: 1px solid #404040;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pemeriksaanData as $item)
                            <tr style="border-bottom: 1px solid #404040;">
                                <td style="padding: 12px; font-size: 14px; color: white;">{{ $item->tgl_perawatan ? $item->tgl_perawatan->format('d/m/Y') : '-' }}</td>
                                <td style="padding: 12px; font-size: 14px; color: white;">{{ $item->jam_rawat ?? '-' }}</td>
                                <td style="padding: 12px; font-size: 14px; color: white;">{{ $item->suhu_tubuh ? $item->suhu_tubuh . '°C' : '-' }}</td>
                                <td style="padding: 12px; font-size: 14px; color: white;">{{ $item->tensi ? $item->tensi . ' mmHg' : '-' }}</td>
                                <td style="padding: 12px; font-size: 14px; color: white;">{{ $item->nadi ? $item->nadi . ' x/mnt' : '-' }}</td>
                                <td style="padding: 12px; font-size: 14px; color: white; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->keluhan }}">{{ Str::limit($item->keluhan ?? '-', 50) }}</td>
                                <td style="padding: 12px; font-size: 14px; color: white;">{{ $item->petugas->nama ?? 'Unknown' }}</td>
                                <td style="padding: 12px;">
                                    <button 
                                        wire:click="hapus('{{ $item->tgl_perawatan->format('Y-m-d') }}', '{{ $item->jam_rawat }}')"
                                        wire:confirm="Yakin ingin menghapus data pemeriksaan ini?"
                                        style="background: #dc2626; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;"
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
            <div style="text-align: center; padding: 48px 0;">
                <div style="color: #d1d5db; font-size: 16px; font-weight: 500;">Belum ada data pemeriksaan</div>
                <div style="color: #9ca3af; font-size: 14px; margin-top: 8px;">Silakan input pemeriksaan baru menggunakan form di atas</div>
            </div>
        @endif
    </div>
</div>