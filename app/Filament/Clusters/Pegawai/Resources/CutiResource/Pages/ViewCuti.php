<?php

namespace App\Filament\Clusters\Pegawai\Resources\CutiResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\CutiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class ViewCuti extends ViewRecord
{
    protected static string $resource = CutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => 
                    auth()->user()->can('approve_cuti') && 
                    $this->record->status === 'pending'
                )
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->approve(auth()->id());
                    $this->refreshFormData(['status', 'approved_by', 'approved_at']);
                }),
                
            Actions\Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => 
                    auth()->user()->can('approve_cuti') && 
                    $this->record->status === 'pending'
                )
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->reject(auth()->id());
                    $this->refreshFormData(['status', 'approved_by', 'approved_at']);
                }),
                
            Actions\EditAction::make()
                ->label('Edit'),
        ];
    }
    
    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pengajuan Cuti')
                    ->schema([
                        TextEntry::make('employee.name')
                            ->label('Pegawai')
                            ->visible(fn() => auth()->user()->can('view_all_cuti')),
                            
                        TextEntry::make('start_date')
                            ->label('Tanggal Mulai')
                            ->date('d/m/Y'),
                            
                        TextEntry::make('end_date')
                            ->label('Tanggal Selesai')
                            ->date('d/m/Y'),
                            
                        TextEntry::make('total_days')
                            ->label('Total Hari')
                            ->badge()
                            ->color('primary')
                            ->suffix(' hari'),
                            
                        TextEntry::make('leave_type_label')
                            ->label('Jenis Cuti')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Cuti Tahunan' => 'success',
                                'Cuti Sakit' => 'danger',
                                'Cuti Darurat' => 'warning',
                                'Cuti Melahirkan' => 'info',
                                'Cuti Menikah' => 'secondary',
                                default => 'secondary',
                            }),
                            
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Menunggu Persetujuan',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default => $state,
                            }),
                    ])
                    ->columns(3),
                    
                Section::make('Alasan Cuti')
                    ->schema([
                        TextEntry::make('reason')
                            ->label('Alasan')
                            ->prose(),
                    ]),
                    
                Section::make('Persetujuan')
                    ->schema([
                        TextEntry::make('approver.name')
                            ->label('Disetujui Oleh')
                            ->placeholder('Belum diproses'),
                            
                        TextEntry::make('approved_at')
                            ->label('Waktu Persetujuan')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Belum diproses'),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record->approved_by || $record->approved_at),
                    
                Section::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Diajukan Pada')
                            ->dateTime('d/m/Y H:i'),
                            
                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}