<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsent extends EditRecord
{
    protected static string $resource = AbsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat'),
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}