<?php

namespace App\Filament\Clusters\UserRole\Resources\TrackerSQLs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class TrackerSQLForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Informasi SQL Tracker')
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->helperText('Tanggal eksekusi SQL'),
                        
                        Textarea::make('sqle')
                            ->label('SQL Command')
                            ->required()
                            ->rows(5)
                            ->helperText('Perintah SQL yang dieksekusi'),
                        
                        TextInput::make('usere')
                            ->label('User')
                            ->required()
                            ->maxLength(255)
                            ->helperText('User yang mengeksekusi SQL'),
                    ]),
            ]);
    }
}