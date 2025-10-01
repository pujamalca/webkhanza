<?php

namespace App\Filament\Resources\Erm\RawatJalanResource\Pages;

use App\Filament\Resources\Erm\RawatJalanResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;

class ViewRawatJalan extends ViewRecord
{
    protected static string $resource = RawatJalanResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                // Section Informasi Dasar - Ditampilkan di atas semua tab (Lebar Penuh)
                Section::make('📅 Informasi Dasar Pasien')
                    ->description('Informasi dasar rawat jalan dan data pasien')
                    ->schema([
                        Group::make([
                            TextEntry::make('no_rawat')
                                ->label('No. Rawat')
                                ->badge()
                                ->color('primary'),
                            TextEntry::make('no_rkm_medis')
                                ->label('No. RM')
                                ->badge()
                                ->color('success'),
                            TextEntry::make('pasien.nm_pasien')
                                ->label('Nama Pasien')
                                ->weight('bold'),
                            TextEntry::make('tgl_registrasi')
                                ->label('Tanggal Registrasi')
                                ->date('d/m/Y'),
                            TextEntry::make('jam_reg')
                                ->label('Jam Registrasi')
                                ->time('H:i'),
                            TextEntry::make('dokter.nm_dokter')
                                ->label('Dokter')
                                ->default('Tidak ada data'),
                        ])->columns(6),

                        Group::make([
                            TextEntry::make('poliklinik.nm_poli')
                                ->label('Poliklinik')
                                ->badge()
                                ->color('info'),
                            TextEntry::make('penjab.png_jawab')
                                ->label('Cara Bayar')
                                ->badge()
                                ->color('warning'),
                            TextEntry::make('status_lanjut')
                                ->label('Status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'Ralan' => 'success',
                                    'Ranap' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('pasien.jk')
                                ->label('Jenis Kelamin')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'L' => 'blue',
                                    'P' => 'pink',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan',
                                    default => $state,
                                }),
                            TextEntry::make('pasien.tgl_lahir')
                                ->label('Tanggal Lahir')
                                ->date('d/m/Y'),
                            TextEntry::make('pasien.alamat')
                                ->label('Alamat')
                                ->limit(50),
                        ])->columns(6),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->columnSpanFull(),

                Tabs::make('Detail Rawat Jalan')
                    ->tabs([
                        Tab::make('Pemeriksaan Ralan')
                            ->icon('heroicon-o-heart')
                            ->visible(fn () => auth()->user()->hasPermissionTo('rawat_jalan_pemeriksaan_access'))
                            ->schema([
                                Livewire::make(\App\Livewire\PemeriksaanRalanForm::class,
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('pemeriksaan-ralan-' . $this->record->no_rawat),
                            ]),
                        
                        Tab::make('Input Tindakan')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->visible(fn () => auth()->user()->hasPermissionTo('rawat_jalan_input_tindakan_access'))
                            ->schema([
                                Livewire::make(\App\Livewire\InputTindakanForm::class,
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('input-tindakan-' . $this->record->no_rawat),
                            ]),
                        
                        Tab::make('Diagnosa')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->visible(fn () => auth()->user()->hasPermissionTo('rawat_jalan_diagnosa_access'))
                            ->schema([
                                Livewire::make(\App\Livewire\DiagnosaForm::class,
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('diagnosa-' . $this->record->no_rawat),
                            ]),
                        
                        Tab::make('Catatan Pasien')
                            ->icon('heroicon-o-document-text')
                            ->visible(fn () => auth()->user()->hasPermissionTo('rawat_jalan_catatan_access'))
                            ->schema([
                                Livewire::make(\App\Livewire\CatatanPasienForm::class,
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('catatan-pasien-' . $this->record->no_rawat),
                            ]),
                        
                        Tab::make('Resep Obat')
                            ->icon('heroicon-o-beaker')
                            ->visible(fn () => auth()->user()->hasPermissionTo('rawat_jalan_resep_access'))
                            ->schema([
                                Livewire::make(\App\Livewire\ResepObatForm::class,
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('resep-obat-' . $this->record->no_rawat),
                            ]),
                        
                        Tab::make('Permintaan Labor')
                            ->icon('heroicon-o-beaker')
                            ->visible(fn () => auth()->user()->hasPermissionTo('rawat_jalan_labor_access'))
                            ->schema([
                                Livewire::make(\App\Livewire\PermintaanLaboratorium::class,
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('permintaan-laboratorium-' . $this->record->no_rawat),
                            ]),

                        Tab::make('Resume Pasien')
                            ->icon('heroicon-o-document-chart-bar')
                            ->visible(fn () => auth()->user()->hasPermissionTo('rawat_jalan_resume_access'))
                            ->schema([
                                Livewire::make(\App\Livewire\ResumePasienForm::class,
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('resume-pasien-' . $this->record->no_rawat),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}