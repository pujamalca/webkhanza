<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;

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
                Section::make('Detail Absensi')
                    ->schema([
                        View::make('filament.pages.absent-detail')
                            ->viewData([
                                'record' => $this->record
                            ])
                    ])
            ]);
    }
}