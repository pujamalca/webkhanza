<div style="space-y: 24px;">
    <!-- SOAP Form Section -->
    <x-filament::section>
        <x-slot name="heading">
            <span style="font-size: 18px; font-weight: 600; color: var(--gray-900, #111827);">
                SOAP Perawatan 
                @if($editingId)
                    <small style="color: var(--warning-600, #ca8a04); font-weight: 500;">(Mode Edit)</small>
                @endif
            </span>
            <small style="color: var(--gray-500, #6b7280); margin-left: 8px;">(Subjective, Objective, Assessment, Plan, Intervention, Evaluation)</small>
        </x-slot>
        
        <form wire:submit="simpanPemeriksaan" style="space-y: 24px;">
            <!-- Date, Time and Petugas -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 8px; padding: 8px; background-color: var(--primary-50, #f0f9ff); border: 1px solid var(--gray-200, #e5e7eb); border-radius: 6px;">
                <div>
                    <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-700, #374151); margin-bottom: 2px;">Tanggal</label>
                    <input type="date" wire:model="tgl_perawatan" required 
                           style="width: 100%; padding: 3px 6px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; font-size: 12px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    @error('tgl_perawatan') <span style="color: var(--danger-600, #dc2626); font-size: 10px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-700, #374151); margin-bottom: 2px;">Jam</label>
                    <input type="time" wire:model="jam_rawat" required 
                           style="width: 100%; padding: 3px 6px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; font-size: 12px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    @error('jam_rawat') <span style="color: var(--danger-600, #dc2626); font-size: 10px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-700, #374151); margin-bottom: 2px;">
                        Petugas
                        @if(!$isAdmin)
                            <small style="color: var(--gray-500, #6b7280);">(Auto)</small>
                        @endif
                    </label>
                    @if($isAdmin)
                        <select wire:model="nip" required 
                                style="width: 100%; padding: 3px 6px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; font-size: 12px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                            <option value="">Pilih Petugas</option>
                            @foreach($pegawaiList as $nik => $nama)
                                <option value="{{ $nik }}">{{ $nama }} ({{ $nik }})</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" wire:model="nip" readonly 
                               style="width: 100%; padding: 3px 6px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; font-size: 12px; background-color: var(--gray-100, #f3f4f6); color: var(--gray-700, #374151);">
                    @endif
                    @error('nip') <span style="color: var(--danger-600, #dc2626); font-size: 10px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Vital Signs -->
            <div style="background-color: var(--gray-50, #f9fafb); padding: 8px; border: 1px solid var(--gray-200, #e5e7eb); border-radius: 6px;">
                <h3 style="font-size: 12px; font-weight: 600; color: var(--gray-700, #374151); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                    <span style="background-color: var(--red-600, #dc2626); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">TTV</span>
                    Tanda Vital
                </h3>
                
                <!-- Compact grid layout -->
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-bottom: 8px;">
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">Suhu</label>
                        <input type="number" step="0.1" wire:model="suhu_tubuh" placeholder="36.5"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">TD</label>
                        <input type="text" wire:model="tensi" placeholder="120/80"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">Nadi</label>
                        <input type="number" wire:model="nadi" placeholder="80"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">RR</label>
                        <input type="number" wire:model="respirasi" placeholder="20"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">SPO2</label>
                        <input type="number" min="0" max="100" wire:model="spo2" placeholder="98"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">TB</label>
                        <input type="number" wire:model="tinggi" placeholder="170"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 2fr 1fr; gap: 8px;">
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">BB</label>
                        <input type="number" step="0.1" wire:model="berat" placeholder="70"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">GCS</label>
                        <input type="text" wire:model="gcs" placeholder="E4V5M6"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">LP</label>
                        <input type="number" step="0.1" wire:model="lingkar_perut" placeholder="85"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">Kesadaran</label>
                        <select wire:model="kesadaran" style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                            <option value="">Pilih</option>
                            <option value="Compos Mentis">Compos Mentis</option>
                            <option value="Apatis">Apatis</option>
                            <option value="Somnolen">Somnolen</option>
                            <option value="Sopor">Sopor</option>
                            <option value="Koma">Koma</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 10px; font-weight: 500; color: var(--gray-600, #4b5563); margin-bottom: 2px;">Alergi</label>
                        <input type="text" maxlength="50" wire:model="alergi" placeholder="Tidak ada"
                               style="width: 100%; padding: 3px 6px; font-size: 12px; border: 1px solid var(--gray-300, #d1d5db); border-radius: 3px; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);">
                    </div>
                </div>
            </div>

            <!-- SOAPIE Grid - 3 Columns -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                <!-- Column 1: Subjective & Objective -->
                <div style="space-y: 16px;">
                    <!-- Subjective -->
                    <div style="background: var(--success-50, #f0fdf4); padding: 16px; border: 1px solid var(--success-200, #bbf7d0); border-radius: 8px; border-left: 4px solid var(--success-500, #22c55e);">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--success-700, #15803d); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="background-color: var(--success-600, #16a34a); color: white; padding: 4px 8px; border-radius: 4px; font-size: 14px;">S</span>
                            SUBJECTIVE (Keluhan Pasien)
                        </h3>
                        <textarea wire:model="keluhan" rows="8" 
                                  placeholder="Tuliskan keluhan utama pasien, riwayat penyakit sekarang, dan anamnesis..."
                                  style="width: 100%; padding: 12px; font-size: 14px; border: 1px solid var(--success-300, #86efac); border-radius: 8px; resize: none; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);"></textarea>
                        @error('keluhan') <span style="color: var(--danger-600, #dc2626); font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Objective -->
                    <div style="background: var(--primary-50, #eff6ff); padding: 16px; border: 1px solid var(--primary-200, #bfdbfe); border-radius: 8px; border-left: 4px solid var(--primary-500, #3b82f6);">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--primary-700, #1d4ed8); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="background-color: var(--primary-600, #2563eb); color: white; padding: 4px 8px; border-radius: 4px; font-size: 14px;">O</span>
                            OBJECTIVE (Pemeriksaan Fisik)
                        </h3>
                        <textarea wire:model="pemeriksaan" rows="8"
                                  placeholder="Hasil pemeriksaan fisik, temuan klinis, hasil laboratorium/radiologi..."
                                  style="width: 100%; padding: 12px; font-size: 14px; border: 1px solid var(--primary-300, #93c5fd); border-radius: 8px; resize: none; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);"></textarea>
                        @error('pemeriksaan') <span style="color: var(--danger-600, #dc2626); font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Column 2: Assessment & Plan -->
                <div style="space-y: 16px;">
                    <!-- Assessment -->
                    <div style="background: var(--warning-50, #fefce8); padding: 16px; border: 1px solid var(--warning-200, #fde68a); border-radius: 8px; border-left: 4px solid var(--warning-500, #f59e0b);">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--warning-700, #a16207); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="background-color: var(--warning-600, #ca8a04); color: white; padding: 4px 8px; border-radius: 4px; font-size: 14px;">A</span>
                            ASSESSMENT (Diagnosis)
                        </h3>
                        <textarea wire:model="penilaian" rows="8"
                                  placeholder="Diagnosis kerja, diagnosis banding, interpretasi hasil pemeriksaan..."
                                  style="width: 100%; padding: 12px; font-size: 14px; border: 1px solid var(--warning-300, #fcd34d); border-radius: 8px; resize: none; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);"></textarea>
                        @error('penilaian') <span style="color: var(--danger-600, #dc2626); font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Plan -->
                    <div style="background: var(--info-50, #f0fdfa); padding: 16px; border: 1px solid var(--info-200, #a5f3fc); border-radius: 8px; border-left: 4px solid var(--info-500, #06b6d4);">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--info-700, #0e7490); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="background-color: var(--info-600, #0891b2); color: white; padding: 4px 8px; border-radius: 4px; font-size: 14px;">P</span>
                            PLAN (Rencana Tindakan)
                        </h3>
                        <textarea wire:model="rtl" rows="8"
                                  placeholder="Rencana tindak lanjut, terapi, edukasi, kontrol ulang..."
                                  style="width: 100%; padding: 12px; font-size: 14px; border: 1px solid var(--info-300, #67e8f9); border-radius: 8px; resize: none; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);"></textarea>
                        @error('rtl') <span style="color: var(--danger-600, #dc2626); font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Column 3: Intervention & Evaluation -->
                <div style="space-y: 16px;">
                    <!-- Intervention -->
                    <div style="background: var(--danger-50, #fef2f2); padding: 16px; border: 1px solid var(--danger-200, #fecaca); border-radius: 8px; border-left: 4px solid var(--danger-500, #ef4444);">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--danger-700, #b91c1c); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="background-color: var(--danger-600, #dc2626); color: white; padding: 4px 8px; border-radius: 4px; font-size: 14px;">I</span>
                            INTERVENTION (Tindakan yang Dilakukan)
                        </h3>
                        <textarea wire:model="instruksi" rows="8"
                                  placeholder="Tindakan medis yang sudah dilakukan, prosedur, pemberian obat..."
                                  style="width: 100%; padding: 12px; font-size: 14px; border: 1px solid var(--danger-300, #fca5a5); border-radius: 8px; resize: none; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);"></textarea>
                        @error('instruksi') <span style="color: var(--danger-600, #dc2626); font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Evaluation -->
                    <div style="background: var(--slate-50, #f8fafc); padding: 16px; border: 1px solid var(--slate-200, #e2e8f0); border-radius: 8px; border-left: 4px solid var(--slate-500, #64748b);">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--slate-700, #334155); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <span style="background-color: var(--slate-600, #475569); color: white; padding: 4px 8px; border-radius: 4px; font-size: 14px;">E</span>
                            EVALUATION (Evaluasi & Hasil)
                        </h3>
                        <textarea wire:model="evaluasi" rows="8"
                                  placeholder="Evaluasi kondisi pasien, respons terhadap terapi, outcome..."
                                  style="width: 100%; padding: 12px; font-size: 14px; border: 1px solid var(--slate-300, #cbd5e1); border-radius: 8px; resize: none; background-color: var(--white, #ffffff); color: var(--gray-900, #111827);"></textarea>
                        @error('evaluasi') <span style="color: var(--danger-600, #dc2626); font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid var(--gray-200, #e5e7eb);">
                <x-filament::button type="button" color="gray" size="sm" wire:click="resetForm">
                    Reset Form
                </x-filament::button>
                <x-filament::button type="submit" size="sm">
                    {{ $editingId ? 'Update SOAP' : 'Simpan SOAP' }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <!-- History Section -->
    @if(count($riwayatPemeriksaan) > 0)
    <x-filament::section>
        <x-slot name="heading">
            Riwayat Pemeriksaan SOAP 
            <small style="color: var(--gray-500, #6b7280); font-weight: normal;">
                ({{ $totalRecords }} total, halaman {{ $currentPage }} dari {{ $totalPages }})
            </small>
        </x-slot>
        
        <div style="space-y: 16px;">
            @foreach($riwayatPemeriksaan as $pemeriksaan)
            <div style="border: 1px solid {{ $editingId === $pemeriksaan['tgl_perawatan_raw'] . '-' . $pemeriksaan['jam_rawat_raw'] ? 'var(--primary-400, #60a5fa)' : 'var(--gray-200, #e5e7eb)' }}; border-radius: 8px; padding: 16px; background-color: {{ $editingId === $pemeriksaan['tgl_perawatan_raw'] . '-' . $pemeriksaan['jam_rawat_raw'] ? 'var(--primary-50, #eff6ff)' : 'var(--white, #ffffff)' }}; transition: box-shadow 0.2s;">
                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--gray-100, #f3f4f6);">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <span style="font-weight: 600; color: var(--primary-600, #2563eb);">
                            {{ \Carbon\Carbon::parse($pemeriksaan['tgl_perawatan'])->format('d/m/Y') }}
                        </span>
                        <span style="font-size: 14px; color: var(--gray-500, #6b7280);">{{ $pemeriksaan['jam_rawat'] }}</span>
                        @if($pemeriksaan['petugas'])
                            <span style="font-size: 12px; background-color: var(--gray-100, #f3f4f6); color: var(--gray-700, #374151); padding: 4px 8px; border-radius: 4px;">{{ $pemeriksaan['petugas']['nama'] ?? $pemeriksaan['nip'] }}</span>
                        @endif
                    </div>
                    <x-filament::button type="button" color="primary" size="xs" 
                                        wire:click="editPemeriksaan('{{ $pemeriksaan['tgl_perawatan_raw'] }}', '{{ $pemeriksaan['jam_rawat_raw'] }}')"
                                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="editPemeriksaan">Edit</span>
                        <span wire:loading wire:target="editPemeriksaan">Loading...</span>
                    </x-filament::button>
                </div>

                <!-- Vital Signs (TTV) Rows -->
                @if($pemeriksaan['suhu_tubuh'] || $pemeriksaan['tensi'] || $pemeriksaan['nadi'] || $pemeriksaan['respirasi'] || $pemeriksaan['spo2'] || $pemeriksaan['tinggi'] || $pemeriksaan['berat'] || $pemeriksaan['gcs'] || $pemeriksaan['kesadaran'] || $pemeriksaan['lingkar_perut'])
                <div style="margin-bottom: 12px; padding: 8px; background-color: var(--gray-50, #f9fafb); border: 1px solid var(--gray-200, #e5e7eb); border-radius: 6px;">
                    <h4 style="font-weight: 600; color: var(--gray-700, #374151); margin-bottom: 8px; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                        <span style="background-color: var(--red-600, #dc2626); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">TTV</span>
                        Tanda Vital
                    </h4>
                    
                    <!-- Compact Vitals in Single Row -->
                    <div style="display: flex; flex-wrap: wrap; gap: 6px; font-size: 11px;">
                        @if($pemeriksaan['suhu_tubuh'])
                        <span style="padding: 4px 8px; background-color: var(--red-50, #fef2f2); color: var(--red-700, #b91c1c); border: 1px solid var(--red-200, #fecaca); border-radius: 4px; font-weight: 600;">
                            SUHU: {{ $pemeriksaan['suhu_tubuh'] }}°C
                        </span>
                        @endif
                        @if($pemeriksaan['tensi'])
                        <span style="padding: 4px 8px; background-color: var(--blue-50, #eff6ff); color: var(--blue-700, #1d4ed8); border: 1px solid var(--blue-200, #bfdbfe); border-radius: 4px; font-weight: 600;">
                            TD: {{ $pemeriksaan['tensi'] }}
                        </span>
                        @endif
                        @if($pemeriksaan['nadi'])
                        <span style="padding: 4px 8px; background-color: var(--green-50, #f0fdf4); color: var(--green-700, #15803d); border: 1px solid var(--green-200, #bbf7d0); border-radius: 4px; font-weight: 600;">
                            N: {{ $pemeriksaan['nadi'] }}/min
                        </span>
                        @endif
                        @if($pemeriksaan['respirasi'])
                        <span style="padding: 4px 8px; background-color: var(--purple-50, #faf5ff); color: var(--purple-700, #7c3aed); border: 1px solid var(--purple-200, #e9d5ff); border-radius: 4px; font-weight: 600;">
                            RR: {{ $pemeriksaan['respirasi'] }}/min
                        </span>
                        @endif
                        @if($pemeriksaan['spo2'])
                        <span style="padding: 4px 8px; background-color: var(--cyan-50, #ecfeff); color: var(--cyan-700, #0e7490); border: 1px solid var(--cyan-200, #a5f3fc); border-radius: 4px; font-weight: 600;">
                            SPO2: {{ $pemeriksaan['spo2'] }}%
                        </span>
                        @endif
                        @if($pemeriksaan['tinggi'])
                        <span style="padding: 4px 8px; background-color: var(--orange-50, #fff7ed); color: var(--orange-700, #c2410c); border: 1px solid var(--orange-200, #fed7aa); border-radius: 4px; font-weight: 600;">
                            TB: {{ $pemeriksaan['tinggi'] }}cm
                        </span>
                        @endif
                        @if($pemeriksaan['berat'])
                        <span style="padding: 4px 8px; background-color: var(--yellow-50, #fefce8); color: var(--yellow-700, #a16207); border: 1px solid var(--yellow-200, #fde047); border-radius: 4px; font-weight: 600;">
                            BB: {{ $pemeriksaan['berat'] }}kg
                        </span>
                        @endif
                        @if($pemeriksaan['gcs'])
                        <span style="padding: 4px 8px; background-color: var(--indigo-50, #eef2ff); color: var(--indigo-700, #4338ca); border: 1px solid var(--indigo-200, #c7d2fe); border-radius: 4px; font-weight: 600;">
                            GCS: {{ $pemeriksaan['gcs'] }}
                        </span>
                        @endif
                        @if($pemeriksaan['lingkar_perut'])
                        <span style="padding: 4px 8px; background-color: var(--pink-50, #fdf2f8); color: var(--pink-700, #be185d); border: 1px solid var(--pink-200, #fbcfe8); border-radius: 4px; font-weight: 600;">
                            LP: {{ $pemeriksaan['lingkar_perut'] }}cm
                        </span>
                        @endif
                        @if($pemeriksaan['kesadaran'])
                        <span style="padding: 4px 8px; background-color: var(--gray-100, #f3f4f6); color: var(--gray-800, #1f2937); border: 1px solid var(--gray-300, #d1d5db); border-radius: 4px; font-weight: 600;">
                            Kesadaran: {{ $pemeriksaan['kesadaran'] }}
                        </span>
                        @endif
                    </div>
                </div>
                @endif

                <!-- SOAP Content Grid -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; font-size: 14px;">
                    @if($pemeriksaan['keluhan'])
                    <div style="background-color: var(--success-50, #f0fdf4); padding: 12px; border-radius: 6px; border-left: 4px solid var(--success-400, #4ade80);">
                        <h4 style="font-weight: 600; color: var(--success-800, #166534); margin-bottom: 4px;">S - Subjective</h4>
                        <p style="color: var(--gray-700, #374151); font-size: 12px;">{{ Str::limit($pemeriksaan['keluhan'], 100) }}</p>
                    </div>
                    @endif

                    @if($pemeriksaan['pemeriksaan'])
                    <div style="background-color: var(--primary-50, #eff6ff); padding: 12px; border-radius: 6px; border-left: 4px solid var(--primary-400, #60a5fa);">
                        <h4 style="font-weight: 600; color: var(--primary-800, #1e40af); margin-bottom: 4px;">O - Objective</h4>
                        <p style="color: var(--gray-700, #374151); font-size: 12px;">{{ Str::limit($pemeriksaan['pemeriksaan'], 100) }}</p>
                    </div>
                    @endif

                    @if($pemeriksaan['penilaian'])
                    <div style="background-color: var(--warning-50, #fefce8); padding: 12px; border-radius: 6px; border-left: 4px solid var(--warning-400, #facc15);">
                        <h4 style="font-weight: 600; color: var(--warning-800, #92400e); margin-bottom: 4px;">A - Assessment</h4>
                        <p style="color: var(--gray-700, #374151); font-size: 12px;">{{ Str::limit($pemeriksaan['penilaian'], 100) }}</p>
                    </div>
                    @endif

                    @if($pemeriksaan['rtl'])
                    <div style="background-color: var(--info-50, #f0fdfa); padding: 12px; border-radius: 6px; border-left: 4px solid var(--info-400, #22d3ee);">
                        <h4 style="font-weight: 600; color: var(--info-800, #155e75); margin-bottom: 4px;">P - Plan</h4>
                        <p style="color: var(--gray-700, #374151); font-size: 12px;">{{ Str::limit($pemeriksaan['rtl'], 100) }}</p>
                    </div>
                    @endif

                    @if($pemeriksaan['instruksi'])
                    <div style="background-color: var(--danger-50, #fef2f2); padding: 12px; border-radius: 6px; border-left: 4px solid var(--danger-400, #f87171);">
                        <h4 style="font-weight: 600; color: var(--danger-800, #991b1b); margin-bottom: 4px;">I - Intervention</h4>
                        <p style="color: var(--gray-700, #374151); font-size: 12px;">{{ Str::limit($pemeriksaan['instruksi'], 100) }}</p>
                    </div>
                    @endif

                    @if($pemeriksaan['evaluasi'])
                    <div style="background-color: var(--slate-50, #f8fafc); padding: 12px; border-radius: 6px; border-left: 4px solid var(--slate-400, #94a3b8);">
                        <h4 style="font-weight: 600; color: var(--slate-800, #1e293b); margin-bottom: 4px;">E - Evaluation</h4>
                        <p style="color: var(--gray-700, #374151); font-size: 12px;">{{ Str::limit($pemeriksaan['evaluasi'], 100) }}</p>
                    </div>
                    @endif
                </div>

                @if($pemeriksaan['alergi'])
                <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid var(--gray-100, #f3f4f6);">
                    <span style="font-size: 12px; color: var(--danger-600, #dc2626); background-color: var(--danger-100, #fee2e2); padding: 4px 8px; border-radius: 4px;">⚠️ Alergi: {{ $pemeriksaan['alergi'] }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        
        <!-- Pagination Controls -->
        @if($totalPages > 1)
        <div style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-200, #e5e7eb);">
            <!-- Previous Button -->
            <x-filament::button type="button" color="gray" size="sm" 
                                wire:click="previousPage" 
                                wire:loading.attr="disabled"
                                :disabled="$currentPage <= 1">
                <span wire:loading.remove wire:target="previousPage">← Previous</span>
                <span wire:loading wire:target="previousPage">Loading...</span>
            </x-filament::button>
            
            <!-- Page Numbers -->
            @for($i = 1; $i <= $totalPages; $i++)
                @if($i == $currentPage)
                    <span style="padding: 6px 12px; background-color: var(--primary-600, #2563eb); color: white; border-radius: 6px; font-size: 14px; font-weight: 500;">
                        {{ $i }}
                    </span>
                @else
                    <x-filament::button type="button" color="gray" size="sm" 
                                        wire:click="goToPage({{ $i }})"
                                        style="min-width: 40px;">
                        {{ $i }}
                    </x-filament::button>
                @endif
            @endfor
            
            <!-- Next Button -->
            <x-filament::button type="button" color="gray" size="sm" 
                                wire:click="nextPage" 
                                wire:loading.attr="disabled"
                                :disabled="$currentPage >= $totalPages">
                <span wire:loading.remove wire:target="nextPage">Next →</span>
                <span wire:loading wire:target="nextPage">Loading...</span>
            </x-filament::button>
        </div>
        @endif
    </x-filament::section>
    @endif
</div>