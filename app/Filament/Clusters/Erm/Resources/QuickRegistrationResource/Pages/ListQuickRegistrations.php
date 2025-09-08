<?php

namespace App\Filament\Clusters\Erm\Resources\QuickRegistrationResource\Pages;

use App\Filament\Clusters\Erm\Resources\QuickRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuickRegistrations extends ListRecords
{
    protected static string $resource = QuickRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrasi Cepat Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Registrasi Cepat';
    }
}