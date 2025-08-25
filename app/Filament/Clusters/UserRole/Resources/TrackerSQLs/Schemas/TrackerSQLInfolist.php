<?php

namespace App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class TrackerSQLInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Informasi SQL Tracker')
                    ->schema([
                        TextEntry::make('tanggal')
                            ->label('Tanggal')
                            ->date('d/m/Y H:i'),
                        
                        TextEntry::make('sqle')
                            ->label('SQL Command')
                            ->columnSpanFull(),
                        
                        TextEntry::make('usere')
                            ->label('User'),
                        
                        TextEntry::make('custom_key')
                            ->label('Custom Key')
                            ->getStateUsing(fn ($record) => $record->custom_key),
                    ]),
            ]);
    }
}