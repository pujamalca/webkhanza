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
                                Section::make('Data Pemeriksaan')
                                    ->schema([
                                        SchemaActions::make([
                                            Action::make('add_examination')
                                                ->label('Tambah/Edit Pemeriksaan')
                                                ->icon('heroicon-o-plus')
                                                ->color('primary')
                                                ->visible(fn () => auth()->check())
                                                ->form([
                                                    DatePicker::make('tgl_perawatan')
                                                        ->label('Tanggal Pemeriksaan')
                                                        ->default(now())
                                                        ->required()
                                                        ->columnSpan(1),
                                                    
                                                    TimePicker::make('jam_rawat')
                                                        ->label('Jam Pemeriksaan')
                                                        ->default(now()->format('H:i'))
                                                        ->required()
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('suhu_tubuh')
                                                        ->label('Suhu Tubuh (°C)')
                                                        ->numeric()
                                                        ->step(0.1)
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('tensi')
                                                        ->label('Tensi (mmHg)')
                                                        ->placeholder('120/80')
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('nadi')
                                                        ->label('Nadi (x/menit)')
                                                        ->numeric()
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('respirasi')
                                                        ->label('Respirasi (x/menit)')
                                                        ->numeric()
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('tinggi')
                                                        ->label('Tinggi Badan (cm)')
                                                        ->numeric()
                                                        ->step(0.1)
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('berat')
                                                        ->label('Berat Badan (kg)')
                                                        ->numeric()
                                                        ->step(0.1)
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('spo2')
                                                        ->label('SpO2 (%)')
                                                        ->numeric()
                                                        ->step(0.1)
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('gcs')
                                                        ->label('GCS')
                                                        ->placeholder('E4V5M6')
                                                        ->columnSpan(1),
                                                    
                                                    Select::make('kesadaran')
                                                        ->label('Kesadaran')
                                                        ->options([
                                                            'Compos Mentis' => 'Compos Mentis',
                                                            'Somnolence' => 'Somnolence', 
                                                            'Sopor' => 'Sopor',
                                                            'Coma' => 'Coma',
                                                        ])
                                                        ->columnSpan(1),
                                                    
                                                    TextInput::make('lingkar_perut')
                                                        ->label('Lingkar Perut (cm)')
                                                        ->numeric()
                                                        ->step(0.1)
                                                        ->columnSpan(1),
                                                    
                                                    Textarea::make('keluhan')
                                                        ->label('Keluhan')
                                                        ->rows(3)
                                                        ->columnSpanFull(),
                                                    
                                                    Textarea::make('pemeriksaan')
                                                        ->label('Pemeriksaan Fisik')
                                                        ->rows(3)
                                                        ->columnSpanFull(),
                                                    
                                                    TextInput::make('alergi')
                                                        ->label('Alergi')
                                                        ->columnSpanFull(),
                                                    
                                                    Textarea::make('penilaian')
                                                        ->label('Penilaian')
                                                        ->rows(3)
                                                        ->columnSpanFull(),
                                                    
                                                    Textarea::make('rtl')
                                                        ->label('RTL (Rencana Tindak Lanjut)')
                                                        ->rows(3)
                                                        ->columnSpanFull(),
                                                    
                                                    Textarea::make('instruksi')
                                                        ->label('Instruksi')
                                                        ->rows(2)
                                                        ->columnSpanFull(),
                                                    
                                                    Textarea::make('evaluasi')
                                                        ->label('Evaluasi')
                                                        ->rows(2)
                                                        ->columnSpanFull(),
                                                ])
                                                ->fillForm(function () {
                                                    $examination = $this->record->pemeriksaanRalan;
                                                    return $examination ? $examination->toArray() : [];
                                                })
                                                ->action(function (array $data) {
                                                    $data['no_rawat'] = $this->record->no_rawat;
                                                    $data['nip'] = auth()->user()->nip ?? auth()->id();
                                                    
                                                    PemeriksaanRalan::updateOrCreate(
                                                        ['no_rawat' => $this->record->no_rawat],
                                                        $data
                                                    );
                                                    
                                                    Notification::make()
                                                        ->title('Pemeriksaan berhasil disimpan')
                                                        ->success()
                                                        ->send();
                                                        
                                                    $this->refreshFormData(['pemeriksaanRalan']);
                                                })
                                        ])
                                        ->alignEnd(),
                                            
                                        // Display existing data
                                        TextEntry::make('pemeriksaanRalan.tgl_perawatan')
                                            ->label('Tanggal Pemeriksaan')
                                            ->date('d/m/Y')
                                            ->placeholder('Belum ada pemeriksaan'),
                                        
                                        TextEntry::make('pemeriksaanRalan.jam_rawat')
                                            ->label('Jam Pemeriksaan')
                                            ->time('H:i')
                                            ->placeholder('-'),
                                        
                                        Fieldset::make('Tanda Vital')
                                            ->schema([
                                                TextEntry::make('pemeriksaanRalan.suhu_tubuh')
                                                    ->label('Suhu Tubuh')
                                                    ->suffix('°C')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.tensi')
                                                    ->label('Tensi')
                                                    ->suffix('mmHg')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.nadi')
                                                    ->label('Nadi')
                                                    ->suffix('x/menit')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.respirasi')
                                                    ->label('Respirasi')
                                                    ->suffix('x/menit')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.tinggi')
                                                    ->label('Tinggi Badan')
                                                    ->suffix('cm')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.berat')
                                                    ->label('Berat Badan')
                                                    ->suffix('kg')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.spo2')
                                                    ->label('SpO2')
                                                    ->suffix('%')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.gcs')
                                                    ->label('GCS')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.kesadaran')
                                                    ->label('Kesadaran')
                                                    ->placeholder('-'),
                                                
                                                TextEntry::make('pemeriksaanRalan.lingkar_perut')
                                                    ->label('Lingkar Perut')
                                                    ->suffix('cm')
                                                    ->placeholder('-'),
                                            ])
                                            ->columns(3),
                                        
                                        TextEntry::make('pemeriksaanRalan.keluhan')
                                            ->label('Keluhan')
                                            ->columnSpanFull()
                                            ->placeholder('Belum ada keluhan tercatat'),
                                        
                                        TextEntry::make('pemeriksaanRalan.pemeriksaan')
                                            ->label('Pemeriksaan Fisik')
                                            ->columnSpanFull()
                                            ->placeholder('Belum ada pemeriksaan fisik tercatat'),
                                        
                                        TextEntry::make('pemeriksaanRalan.alergi')
                                            ->label('Alergi')
                                            ->placeholder('Tidak ada alergi tercatat'),
                                        
                                        TextEntry::make('pemeriksaanRalan.penilaian')
                                            ->label('Penilaian')
                                            ->columnSpanFull()
                                            ->placeholder('Belum ada penilaian tercatat'),
                                        
                                        TextEntry::make('pemeriksaanRalan.rtl')
                                            ->label('RTL (Rencana Tindak Lanjut)')
                                            ->columnSpanFull()
                                            ->placeholder('Belum ada RTL tercatat'),
                                        
                                        TextEntry::make('pemeriksaanRalan.instruksi')
                                            ->label('Instruksi')
                                            ->columnSpanFull()
                                            ->placeholder('Belum ada instruksi tercatat'),
                                        
                                        TextEntry::make('pemeriksaanRalan.evaluasi')
                                            ->label('Evaluasi')
                                            ->columnSpanFull()
                                            ->placeholder('Belum ada evaluasi tercatat'),
                                        
                                        TextEntry::make('pemeriksaanRalan.petugas.nama')
                                            ->label('Petugas')
                                            ->placeholder('Unknown'),
                                    ])
                                    ->columns(2),
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