<?php

namespace App\Filament\Clusters\Erm\Resources\RawatJalanResource\Pages;

use App\Filament\Clusters\Erm\Resources\RawatJalanResource;
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
                            ->schema([
                                Livewire::make(\App\Livewire\PemeriksaanRalanForm::class, 
                                    fn () => ['noRawat' => $this->record->no_rawat]
                                )->key('pemeriksaan-ralan-' . $this->record->no_rawat),
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
                            ->icon('heroicon-o-document-text')
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
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                TextEntry::make('stts')
                                    ->label('Status Labor')
                                    ->placeholder('Belum ada pemeriksaan labor tercatat'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}