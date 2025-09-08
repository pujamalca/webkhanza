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
use Filament\Schemas\Components\Livewire;
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
                                Livewire::make('pemeriksaan-ralan-form', [
                                    'noRawat' => $this->record->no_rawat
                                ])
                                ->columnSpanFull()
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