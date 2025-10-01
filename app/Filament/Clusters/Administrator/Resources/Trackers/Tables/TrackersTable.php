<?php

namespace App\Filament\Clusters\Administrator\Resources\Trackers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrackersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('tgl_login')
                    ->label('Tanggal Login')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('jam_login')
                    ->label('Jam Login')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('custom_key')
                    ->label('Custom Key')
                    ->getStateUsing(fn ($record) => $record->nip . '|' . $record->tgl_login . '|' . $record->jam_login),
            ]);
    }
}
