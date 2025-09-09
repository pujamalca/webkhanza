<?php

namespace App\Filament\Clusters\Erm\Resources\RawatJalanResource\Pages;

use App\Filament\Clusters\Erm\Resources\RawatJalanResource;
use App\Models\DiagnosaPasien;
use App\Models\PemeriksaanRalan;
use App\Models\ResepObat;
use App\Models\TindakanRalan;
use App\Models\CatatanPasien;
use App\Models\PermintaanLab;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;

class ViewRawatJalan extends ViewRecord
{
    protected static string $resource = RawatJalanResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pasien')
                    ->schema([
                        Fieldset::make('Data Registrasi')
                            ->schema([
                                TextEntry::make('no_rawat')
                                    ->label('No. Rawat')
                                    ->copyable()
                                    ->icon('heroicon-o-clipboard'),
                                
                                TextEntry::make('no_reg')
                                    ->label('No. Registrasi'),
                                
                                TextEntry::make('tgl_registrasi')
                                    ->label('Tanggal Registrasi')
                                    ->date('d/m/Y'),
                                
                                TextEntry::make('jam_reg')
                                    ->label('Jam Registrasi')
                                    ->time('H:i'),
                            ])
                            ->columns(2),
                        
                        Fieldset::make('Data Pasien')
                            ->schema([
                                TextEntry::make('pasien.no_rkm_medis')
                                    ->label('No. RM')
                                    ->copyable(),
                                
                                TextEntry::make('pasien.nm_pasien')
                                    ->label('Nama Pasien')
                                    ->weight('bold'),
                                
                                TextEntry::make('pasien.jk')
                                    ->label('Jenis Kelamin')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'L' => 'blue',
                                        'P' => 'pink',
                                        default => 'gray',
                                    }),
                                
                                TextEntry::make('poliklinik.nm_poli')
                                    ->label('Poliklinik')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('dokter.nm_dokter')
                                    ->label('Dokter'),
                                
                                TextEntry::make('penjab.png_jawab')
                                    ->label('Cara Bayar')
                                    ->badge()
                                    ->color('success'),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
                
                Tabs::make('Detail Rawat Jalan')
                    ->tabs([
                        Tab::make('Pemeriksaan Ralan')
                            ->icon('heroicon-o-heart')
                            ->schema([
                                Section::make('Input Pemeriksaan Baru')
                                    ->schema([
                                        Group::make([
                                            DatePicker::make('tgl_perawatan')
                                                ->label('Tanggal Pemeriksaan')
                                                ->default(now())
                                                ->required(),
                                            TimePicker::make('jam_rawat')
                                                ->label('Jam Pemeriksaan')
                                                ->default(now()->format('H:i'))
                                                ->required(),
                                        ])->columns(2),
                                        
                                        Fieldset::make('Tanda Vital')
                                            ->schema([
                                                Group::make([
                                                    TextInput::make('suhu_tubuh')
                                                        ->label('Suhu Tubuh (°C)')
                                                        ->numeric()
                                                        ->step(0.1),
                                                    TextInput::make('tensi')
                                                        ->label('Tensi (mmHg)')
                                                        ->placeholder('120/80'),
                                                    TextInput::make('nadi')
                                                        ->label('Nadi (x/menit)')
                                                        ->numeric(),
                                                    TextInput::make('respirasi')
                                                        ->label('Respirasi (x/menit)')
                                                        ->numeric(),
                                                ])->columns(4),
                                                
                                                Group::make([
                                                    TextInput::make('tinggi')
                                                        ->label('Tinggi Badan (cm)')
                                                        ->numeric()
                                                        ->step(0.1),
                                                    TextInput::make('berat')
                                                        ->label('Berat Badan (kg)')
                                                        ->numeric()
                                                        ->step(0.1),
                                                    TextInput::make('spo2')
                                                        ->label('SpO2 (%)')
                                                        ->numeric()
                                                        ->step(0.1),
                                                    TextInput::make('gcs')
                                                        ->label('GCS')
                                                        ->placeholder('E4V5M6'),
                                                ])->columns(4),
                                                
                                                Group::make([
                                                    Select::make('kesadaran')
                                                        ->label('Kesadaran')
                                                        ->options([
                                                            'Compos Mentis' => 'Compos Mentis',
                                                            'Somnolence' => 'Somnolence',
                                                            'Sopor' => 'Sopor',
                                                            'Coma' => 'Coma',
                                                        ]),
                                                    TextInput::make('lingkar_perut')
                                                        ->label('Lingkar Perut (cm)')
                                                        ->numeric()
                                                        ->step(0.1),
                                                ])->columns(2),
                                            ]),
                                        
                                        Group::make([
                                            Textarea::make('keluhan')
                                                ->label('Keluhan')
                                                ->rows(3),
                                            Textarea::make('pemeriksaan')
                                                ->label('Pemeriksaan Fisik')
                                                ->rows(3),
                                        ])->columns(2),
                                        
                                        TextInput::make('alergi')
                                            ->label('Alergi'),
                                        
                                        Group::make([
                                            Textarea::make('penilaian')
                                                ->label('Penilaian')
                                                ->rows(3),
                                            Textarea::make('rtl')
                                                ->label('RTL (Rencana Tindak Lanjut)')
                                                ->rows(3),
                                        ])->columns(2),
                                        
                                        Group::make([
                                            Textarea::make('instruksi')
                                                ->label('Instruksi')
                                                ->rows(2),
                                            Textarea::make('evaluasi')
                                                ->label('Evaluasi')
                                                ->rows(2),
                                        ])->columns(2),
                                    ])
                                    ->headerActions([
                                        Action::make('simpan_pemeriksaan')
                                            ->label('Simpan Pemeriksaan')
                                            ->icon('heroicon-o-plus')
                                            ->action(function (array $data) {
                                                $data['no_rawat'] = $this->record->no_rawat;
                                                $data['nip'] = '-';
                                                
                                                \App\Models\PemeriksaanRalan::create($data);
                                                
                                                Notification::make()
                                                    ->title('Pemeriksaan berhasil disimpan')
                                                    ->success()
                                                    ->send();
                                                    
                                                return redirect()->back();
                                            })
                                            ->form([
                                                Group::make([
                                                    DatePicker::make('tgl_perawatan')
                                                        ->label('Tanggal Pemeriksaan')
                                                        ->default(now())
                                                        ->required(),
                                                    TimePicker::make('jam_rawat')
                                                        ->label('Jam Pemeriksaan')
                                                        ->default(now()->format('H:i'))
                                                        ->required(),
                                                ])->columns(2),
                                                
                                                Fieldset::make('Tanda Vital')
                                                    ->schema([
                                                        Group::make([
                                                            TextInput::make('suhu_tubuh')
                                                                ->label('Suhu Tubuh (°C)')
                                                                ->numeric()
                                                                ->step(0.1),
                                                            TextInput::make('tensi')
                                                                ->label('Tensi (mmHg)')
                                                                ->placeholder('120/80'),
                                                            TextInput::make('nadi')
                                                                ->label('Nadi (x/menit)')
                                                                ->numeric(),
                                                            TextInput::make('respirasi')
                                                                ->label('Respirasi (x/menit)')
                                                                ->numeric(),
                                                        ])->columns(4),
                                                        
                                                        Group::make([
                                                            TextInput::make('tinggi')
                                                                ->label('Tinggi Badan (cm)')
                                                                ->numeric()
                                                                ->step(0.1),
                                                            TextInput::make('berat')
                                                                ->label('Berat Badan (kg)')
                                                                ->numeric()
                                                                ->step(0.1),
                                                            TextInput::make('spo2')
                                                                ->label('SpO2 (%)')
                                                                ->numeric()
                                                                ->step(0.1),
                                                            TextInput::make('gcs')
                                                                ->label('GCS')
                                                                ->placeholder('E4V5M6'),
                                                        ])->columns(4),
                                                        
                                                        Group::make([
                                                            Select::make('kesadaran')
                                                                ->label('Kesadaran')
                                                                ->options([
                                                                    'Compos Mentis' => 'Compos Mentis',
                                                                    'Somnolence' => 'Somnolence',
                                                                    'Sopor' => 'Sopor',
                                                                    'Coma' => 'Coma',
                                                                ]),
                                                            TextInput::make('lingkar_perut')
                                                                ->label('Lingkar Perut (cm)')
                                                                ->numeric()
                                                                ->step(0.1),
                                                        ])->columns(2),
                                                    ]),
                                                
                                                Group::make([
                                                    Textarea::make('keluhan')
                                                        ->label('Keluhan')
                                                        ->rows(3),
                                                    Textarea::make('pemeriksaan')
                                                        ->label('Pemeriksaan Fisik')
                                                        ->rows(3),
                                                ])->columns(2),
                                                
                                                TextInput::make('alergi')
                                                    ->label('Alergi'),
                                                
                                                Group::make([
                                                    Textarea::make('penilaian')
                                                        ->label('Penilaian')
                                                        ->rows(3),
                                                    Textarea::make('rtl')
                                                        ->label('RTL (Rencana Tindak Lanjut)')
                                                        ->rows(3),
                                                ])->columns(2),
                                                
                                                Group::make([
                                                    Textarea::make('instruksi')
                                                        ->label('Instruksi')
                                                        ->rows(2),
                                                    Textarea::make('evaluasi')
                                                        ->label('Evaluasi')
                                                        ->rows(2),
                                                ])->columns(2),
                                            ])
                                            ->modalHeading('Input Pemeriksaan Baru')
                                            ->modalWidth('7xl'),
                                    ]),
                                    
                                Section::make('Riwayat Pemeriksaan')
                                    ->schema([
                                        TextEntry::make('pemeriksaan_history')
                                            ->label('')
                                            ->formatStateUsing(function () {
                                                $pemeriksaan = $this->record->pemeriksaanRalan()
                                                    ->with('petugas')
                                                    ->orderBy('tgl_perawatan', 'desc')
                                                    ->orderBy('jam_rawat', 'desc')
                                                    ->get();
                                                
                                                if ($pemeriksaan->isEmpty()) {
                                                    return 'Belum ada data pemeriksaan';
                                                }
                                                
                                                $html = '<div class="overflow-x-auto"><table class="w-full border-collapse border border-gray-300">';
                                                $html .= '<thead><tr class="bg-gray-100">';
                                                $html .= '<th class="border border-gray-300 px-3 py-2 text-left">Tanggal</th>';
                                                $html .= '<th class="border border-gray-300 px-3 py-2 text-left">Jam</th>';
                                                $html .= '<th class="border border-gray-300 px-3 py-2 text-left">Suhu</th>';
                                                $html .= '<th class="border border-gray-300 px-3 py-2 text-left">Tensi</th>';
                                                $html .= '<th class="border border-gray-300 px-3 py-2 text-left">Keluhan</th>';
                                                $html .= '<th class="border border-gray-300 px-3 py-2 text-left">Petugas</th>';
                                                $html .= '</tr></thead><tbody>';
                                                
                                                foreach ($pemeriksaan as $item) {
                                                    $html .= '<tr>';
                                                    $html .= '<td class="border border-gray-300 px-3 py-2">' . ($item->tgl_perawatan ? $item->tgl_perawatan->format('d/m/Y') : '-') . '</td>';
                                                    $html .= '<td class="border border-gray-300 px-3 py-2">' . ($item->jam_rawat ?? '-') . '</td>';
                                                    $html .= '<td class="border border-gray-300 px-3 py-2">' . ($item->suhu_tubuh ? $item->suhu_tubuh . '°C' : '-') . '</td>';
                                                    $html .= '<td class="border border-gray-300 px-3 py-2">' . ($item->tensi ? $item->tensi . ' mmHg' : '-') . '</td>';
                                                    $html .= '<td class="border border-gray-300 px-3 py-2">' . \Str::limit($item->keluhan ?? '-', 50) . '</td>';
                                                    $html .= '<td class="border border-gray-300 px-3 py-2">' . ($item->petugas->nama ?? 'Unknown') . '</td>';
                                                    $html .= '</tr>';
                                                }
                                                
                                                $html .= '</tbody></table></div>';
                                                return $html;
                                            })
                                            ->html(),
                                    ]),
                            ]),
                        
                        Tab::make('Input Tindakan')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                TextEntry::make('stts')
                                    ->label('Status Tindakan')
                                    ->placeholder('Belum ada tindakan tercatat'),
                            ]),
                        
                        Tab::make('Diagnosa')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('stts')
                                    ->label('Status Diagnosa')
                                    ->placeholder('Belum ada diagnosa tercatat'),
                            ]),
                        
                        Tab::make('Catatan Pasien')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                TextEntry::make('stts')
                                    ->label('Status Catatan')
                                    ->placeholder('Belum ada catatan tercatat'),
                            ]),
                        
                        Tab::make('Resep Obat')
                            ->icon('heroicon-o-beaker')
                            ->schema([
                                TextEntry::make('stts')
                                    ->label('Status Resep')
                                    ->placeholder('Belum ada resep tercatat'),
                            ]),
                        
                        Tab::make('Pemeriksaan Labor')
                            ->icon('heroicon-o-beaker')
                            ->schema([
                                TextEntry::make('stts')
                                    ->label('Status Lab')
                                    ->placeholder('Belum ada pemeriksaan lab tercatat'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}