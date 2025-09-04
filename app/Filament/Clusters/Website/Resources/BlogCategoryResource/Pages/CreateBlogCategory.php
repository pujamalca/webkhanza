<?php

namespace App\Filament\Clusters\Website\Resources\BlogCategoryResource\Pages;

use App\Filament\Clusters\Website\Resources\BlogCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogCategory extends CreateRecord
{
    protected static string $resource = BlogCategoryResource::class;
}