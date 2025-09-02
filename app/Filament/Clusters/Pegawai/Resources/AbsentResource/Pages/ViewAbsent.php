<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Components\ImageEntry;
use Filament\Schemas\Components\Section;

class ViewAbsent extends ViewRecord
{
    protected static string $resource = AbsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit'),
        ];
    }
    
    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Absensi')
                    ->schema([
                        TextEntry::make('employee.name')
                            ->label('Pegawai')
                            ->visible(fn() => auth()->user()->can('view_all_absent')),
                            
                        TextEntry::make('date')
                            ->label('Tanggal')
                            ->date('d/m/Y'),
                            
                        TextEntry::make('check_in')
                            ->label('Waktu Masuk')
                            ->time('H:i')
                            ->badge()
                            ->color('success'),
                            
                        TextEntry::make('check_out')
                            ->label('Waktu Pulang')
                            ->time('H:i')
                            ->badge()
                            ->color('info'),
                            
                        TextEntry::make('total_working_hours')
                            ->label('Total Jam Kerja')
                            ->badge()
                            ->color('primary'),
                            
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'hadir' => 'success',
                                'terlambat' => 'warning',
                                'izin' => 'info',
                                'tidak_hadir' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'hadir' => 'Hadir',
                                'tidak_hadir' => 'Tidak Hadir',
                                'terlambat' => 'Terlambat',
                                'izin' => 'Izin',
                                default => $state,
                            }),
                    ])
                    ->columns(3),
                    
                Section::make('Foto Absensi')
                    ->schema([
                        ImageEntry::make('check_in_photo')
                            ->label('Foto Masuk')
                            ->height(200)
                            ->width(200),
                            
                        ImageEntry::make('check_out_photo')
                            ->label('Foto Pulang')
                            ->height(200)
                            ->width(200),
                    ])
                    ->columns(2),
                    
                Section::make('Catatan')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tidak ada catatan'),
                    ])
                    ->visible(fn($record) => $record->notes),
            ]);
    }
}