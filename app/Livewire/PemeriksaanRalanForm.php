<?php

namespace App\Livewire;

use App\Models\PemeriksaanRalan;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

class PemeriksaanRalanForm extends Component implements HasForms
{
    use InteractsWithForms;
    public $no_rawat;
    public ?array $data = [];
    
    public function mount($noRawat)
    {
        $this->no_rawat = $noRawat;
        $this->data = [
            'datetime_pemeriksaan' => now()->format('Y-m-d\TH:i'),
        ];
    }
    
    protected function getFormSchema(): array
    {
        return [
            // Baris 1: Tanggal & Jam Pemeriksaan
            DateTimePicker::make('datetime_pemeriksaan')
                ->label('Tanggal & Jam Pemeriksaan')
                ->required()
                ->seconds(false)
                ->displayFormat('d/m/Y H:i')
                ->columnSpanFull(),

            // Baris 2: Tanda Vital Dasar (5 fields)
            TextInput::make('suhu_tubuh')
                ->label('Suhu Tubuh (°C)')
                ->numeric()
                ->step(0.1)
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            TextInput::make('tensi')
                ->label('Tensi (mmHg)')
                ->placeholder('120/80')
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            TextInput::make('nadi')
                ->label('Nadi (x/menit)')
                ->numeric()
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            TextInput::make('respirasi')
                ->label('Respirasi (x/menit)')
                ->numeric()
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            TextInput::make('spo2')
                ->label('SpO2 (%)')
                ->numeric()
                ->step(0.1)
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),

            // Baris 3: Antropometri & Status (5 fields)
            TextInput::make('tinggi')
                ->label('Tinggi (cm)')
                ->numeric()
                ->step(0.1)
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            TextInput::make('berat')
                ->label('Berat (kg)')
                ->numeric()
                ->step(0.1)
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            TextInput::make('lingkar_perut')
                ->label('Lingkar Perut (cm)')
                ->numeric()
                ->step(0.1)
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            TextInput::make('gcs')
                ->label('GCS')
                ->placeholder('E4V5M6')
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),
            Select::make('kesadaran')
                ->label('Kesadaran')
                ->options([
                    'Compos Mentis' => 'Compos Mentis',
                    'Somnolence' => 'Somnolence',
                    'Sopor' => 'Sopor',
                    'Coma' => 'Coma',
                ])
                ->placeholder('Pilih Kesadaran')
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                ]),

            // Baris 4: Keluhan & Pemeriksaan & Alergi (3 fields)
            Textarea::make('keluhan')
                ->label('Keluhan')
                ->rows(3)
                ->columnSpan([
                    'default' => 5,
                    'sm' => 2,
                    'md' => 4,
                ]),
            Textarea::make('pemeriksaan')
                ->label('Pemeriksaan Fisik')
                ->rows(3)
                ->columnSpan([
                    'default' => 5,
                    'sm' => 2,
                    'md' => 4,
                ]),
            TextInput::make('alergi')
                ->label('Alergi')
                ->columnSpan([
                    'default' => 5,
                    'sm' => 1,
                    'md' => 2,
                ]),

            // Baris 5: Detail Lanjutan (4 fields)
            Textarea::make('penilaian')
                ->label('Penilaian')
                ->rows(2)
                ->columnSpan([
                    'default' => 5,
                    'sm' => 5,
                    'md' => 5,
                ]),
            Textarea::make('rtl')
                ->label('RTL (Rencana Tindak Lanjut)')
                ->rows(2)
                ->columnSpan([
                    'default' => 5,
                    'sm' => 5,
                    'md' => 5,
                ]),
            Textarea::make('instruksi')
                ->label('Instruksi')
                ->rows(2)
                ->columnSpan([
                    'default' => 5,
                    'sm' => 5,
                    'md' => 5,
                ]),
            Textarea::make('evaluasi')
                ->label('Evaluasi')
                ->rows(2)
                ->columnSpan([
                    'default' => 5,
                    'sm' => 5,
                    'md' => 5,
                ]),
        ];
    }

    protected function getFormColumns(): int | string | array
    {
        return [
            'default' => 5,
            'sm' => 5,
            'md' => 10,
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }
    
    public function getPemeriksaanListProperty()
    {
        return PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }
    
    public function editPemeriksaan($tgl_perawatan, $jam_rawat)
    {
        $pemeriksaan = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->where('tgl_perawatan', $tgl_perawatan)
            ->where('jam_rawat', $jam_rawat)
            ->first();
            
        if ($pemeriksaan) {
            $this->data = [
                'datetime_pemeriksaan' => $pemeriksaan->tgl_perawatan->format('Y-m-d') . 'T' . substr($pemeriksaan->jam_rawat, 0, 5),
                'suhu_tubuh' => $pemeriksaan->suhu_tubuh,
                'tensi' => $pemeriksaan->tensi,
                'nadi' => $pemeriksaan->nadi,
                'respirasi' => $pemeriksaan->respirasi,
                'tinggi' => $pemeriksaan->tinggi,
                'berat' => $pemeriksaan->berat,
                'spo2' => $pemeriksaan->spo2,
                'gcs' => $pemeriksaan->gcs,
                'kesadaran' => $pemeriksaan->kesadaran,
                'lingkar_perut' => $pemeriksaan->lingkar_perut,
                'keluhan' => $pemeriksaan->keluhan,
                'pemeriksaan' => $pemeriksaan->pemeriksaan,
                'alergi' => $pemeriksaan->alergi,
                'penilaian' => $pemeriksaan->penilaian,
                'rtl' => $pemeriksaan->rtl,
                'instruksi' => $pemeriksaan->instruksi,
                'evaluasi' => $pemeriksaan->evaluasi,
            ];
        }
    }
    
    public function hapusPemeriksaan($tgl_perawatan, $jam_rawat)
    {
        PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->where('tgl_perawatan', $tgl_perawatan)
            ->where('jam_rawat', $jam_rawat)
            ->delete();
            
        Notification::make()
            ->title('Pemeriksaan berhasil dihapus')
            ->success()
            ->send();
    }
    
    public function resetForm()
    {
        $this->data = [
            'datetime_pemeriksaan' => now()->format('Y-m-d\TH:i'),
        ];
    }
    
    public function simpanPemeriksaan()
    {
        $this->validate();
        
        $data = $this->data;
        
        // Parse datetime untuk memisahkan tanggal dan jam
        $datetime = \Carbon\Carbon::parse($data['datetime_pemeriksaan']);
        $tgl_perawatan = $datetime->format('Y-m-d');
        $jam_rawat = $datetime->format('H:i:s');
        
        // Check if record exists (for edit/update)
        $existing = PemeriksaanRalan::where('no_rawat', $this->no_rawat)
            ->where('tgl_perawatan', $tgl_perawatan)
            ->where('jam_rawat', $jam_rawat)
            ->first();
            
        if ($existing) {
            // Update existing record
            $existing->update([
                'suhu_tubuh' => $data['suhu_tubuh'] ?? '',
                'tensi' => $data['tensi'] ?? '',
                'nadi' => $data['nadi'] ?? '',
                'respirasi' => $data['respirasi'] ?? '',
                'tinggi' => $data['tinggi'] ?? '',
                'berat' => $data['berat'] ?? '',
                'spo2' => $data['spo2'] ?? '',
                'gcs' => $data['gcs'] ?? '',
                'kesadaran' => $data['kesadaran'] ?? '',
                'lingkar_perut' => $data['lingkar_perut'] ?? '',
                'keluhan' => $data['keluhan'] ?? '',
                'pemeriksaan' => $data['pemeriksaan'] ?? '',
                'alergi' => $data['alergi'] ?? '',
                'penilaian' => $data['penilaian'] ?? '',
                'rtl' => $data['rtl'] ?? '',
                'instruksi' => $data['instruksi'] ?? '',
                'evaluasi' => $data['evaluasi'] ?? '',
            ]);
            $message = 'Pemeriksaan berhasil diperbarui';
        } else {
            // Create new record
            PemeriksaanRalan::create([
                'no_rawat' => $this->no_rawat,
                'tgl_perawatan' => $tgl_perawatan,
                'jam_rawat' => $jam_rawat,
                'nip' => auth()->user()->pegawai->nik ?? '-',
                'suhu_tubuh' => $data['suhu_tubuh'] ?? '',
                'tensi' => $data['tensi'] ?? '',
                'nadi' => $data['nadi'] ?? '',
                'respirasi' => $data['respirasi'] ?? '',
                'tinggi' => $data['tinggi'] ?? '',
                'berat' => $data['berat'] ?? '',
                'spo2' => $data['spo2'] ?? '',
                'gcs' => $data['gcs'] ?? '',
                'kesadaran' => $data['kesadaran'] ?? '',
                'lingkar_perut' => $data['lingkar_perut'] ?? '',
                'keluhan' => $data['keluhan'] ?? '',
                'pemeriksaan' => $data['pemeriksaan'] ?? '',
                'alergi' => $data['alergi'] ?? '',
                'penilaian' => $data['penilaian'] ?? '',
                'rtl' => $data['rtl'] ?? '',
                'instruksi' => $data['instruksi'] ?? '',
                'evaluasi' => $data['evaluasi'] ?? '',
            ]);
            $message = 'Pemeriksaan berhasil disimpan';
        }
        
        // Reset form
        $this->resetForm();
        
        Notification::make()
            ->title($message)
            ->success()
            ->send();
    }
    
    public function render()
    {
        return view('livewire.pemeriksaan-ralan-form');
    }
}