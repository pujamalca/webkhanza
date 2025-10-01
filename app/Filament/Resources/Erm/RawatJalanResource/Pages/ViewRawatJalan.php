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
                    ])
                    ->columnSpanFull(),
            ]);
    }
}