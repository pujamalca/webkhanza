<?php

namespace App\Filament\Clusters\Website\Resources\BlogResource\Pages;

use App\Filament\Clusters\Website\Resources\BlogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlogs extends ListRecords
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
