<?php

namespace App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrackerSQLsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('sqle')
                    ->label('SQL Command')
                    ->limit(50)
                    ->tooltip(function ($state) {
                        return $state;
                    })
                    ->searchable(),
                    
                TextColumn::make('usere')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('custom_key')
                    ->label('Custom Key')
                    ->getStateUsing(fn ($record) => $record->tanggal . '|' . $record->sqle . '|' . $record->usere),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}