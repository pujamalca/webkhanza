<?php

namespace App\Filament\Clusters\SDM\Resources\DepartemenResource\Pages;

use App\Filament\Clusters\SDM\Resources\DepartemenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDepartemen extends EditRecord
{
    protected static string $resource = DepartemenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}