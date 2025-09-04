<?php

namespace App\Filament\Clusters\Website\Resources\BlogTagResource\Pages;

use App\Filament\Clusters\Website\Resources\BlogTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogTag extends EditRecord
{
    protected static string $resource = BlogTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}