<?php

namespace App\Filament\Resources\Trackers\Tables;

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
                //
            TextColumn::make('custom_key')
                ->label('Custom Key')
                ->getStateUsing(fn ($record) => $record->custom_key)
                ->sortable(function ($query, $direction) {
                    $query->orderBy('nip', $direction)
                        ->orderBy('tgl_login', $direction)
                        ->orderBy('jam_login', $direction);
                }),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
