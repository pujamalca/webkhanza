<?php

namespace App\Livewire;

use App\Models\PemeriksaanRalan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class PemeriksaanRalanForm extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public string $noRawat;
    public ?array $data = [];

    // Mount komponen dengan default values
    public function mount(string $noRawat): void
    {
        $this->noRawat = $noRawat;
        
        // Set default values: tanggal hari ini dan jam sekarang
        $this->data = [
            'tgl_perawatan' => now()->format('Y-m-d'),
            'jam_rawat' => now()->format('H:i'),
        ];
    }
    
    // Definisi form schema dengan field-field pemeriksaan
    protected function getFormSchema(): array
    {
        return [
                Grid::make(2)
                    ->schema([
                        DatePicker::make('tgl_perawatan')
                            ->label('Tanggal Perawatan')
                            ->required()
                            ->default(now()),

                        TimePicker::make('jam_rawat')
                            ->label('Jam Rawat')
                            ->required()
                            ->seconds(false)
                            ->default(now()->format('H:i')),
                    ]),

                Grid::make(4)
                    ->schema([
                        TextInput::make('suhu_tubuh')
                            ->label('Suhu Tubuh (°C)')
                            ->numeric()
                            ->step(0.1),

                        TextInput::make('tensi')
                            ->label('Tensi (mmHg)'),

                        TextInput::make('nadi')
                            ->label('Nadi (x/mnt)')
                            ->numeric(),

                        TextInput::make('respirasi')
                            ->label('RR (x/mnt)')
                            ->numeric(),
                    ]),

                Grid::make(4)
                    ->schema([
                        TextInput::make('spo2')
                            ->label('SpO2 (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),

                        TextInput::make('tinggi')
                            ->label('TB (cm)')
                            ->numeric(),

                        TextInput::make('berat')
                            ->label('BB (kg)')
                            ->numeric()
                            ->step(0.1),

                        TextInput::make('gcs')
                            ->label('GCS'),
                    ]),

                Select::make('kesadaran')
                    ->label('Kesadaran')
                    ->options([
                        'Compos Mentis' => 'Compos Mentis',
                        'Apatis' => 'Apatis',
                        'Somnolen' => 'Somnolen',
                        'Sopor' => 'Sopor',
                        'Koma' => 'Koma',
                    ])
                    ->searchable(),

                Textarea::make('keluhan')
                    ->label('Keluhan')
                    ->columnSpanFull()
                    ->rows(3),

                Textarea::make('pemeriksaan')
                    ->label('Pemeriksaan')
                    ->columnSpanFull()
                    ->rows(3),

                Textarea::make('penilaian')
                    ->label('Penilaian')
                    ->columnSpanFull()
                    ->rows(3),
            ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }
    
    // Action untuk menyimpan data pemeriksaan baru
    public function simpanPemeriksaan(): void
    {
        $this->validate();

        $data = $this->data;
        $data['no_rawat'] = $this->noRawat;
        $data['nip'] = auth()->user()->pegawai->nik ?? '-';

        PemeriksaanRalan::create($data);

        // Reset form ke default values
        $this->data = [
            'tgl_perawatan' => now()->format('Y-m-d'),
            'jam_rawat' => now()->format('H:i'),
        ];

        // Reset pagination dan kirim notifikasi sukses
        $this->resetTable();
        
        Notification::make()
            ->title('Pemeriksaan berhasil disimpan')
            ->success()
            ->send();
    }

    // Method untuk reset form
    public function resetForm(): void
    {
        $this->data = [
            'tgl_perawatan' => now()->format('Y-m-d'),
            'jam_rawat' => now()->format('H:i'),
        ];
    }
    
    // Property untuk menyimpan data pemeriksaan
    public function getPemeriksaanListProperty()
    {
        return PemeriksaanRalan::where('no_rawat', $this->noRawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }
    
    public function render()
    {
        return view('livewire.pemeriksaan-ralan-form');
    }
}