<?php

namespace App\Filament\Clusters\Website\Resources\WebsiteIdentityResource\Pages;

use App\Filament\Clusters\Website\Resources\WebsiteIdentityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteIdentity extends EditRecord
{
    protected static string $resource = WebsiteIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}