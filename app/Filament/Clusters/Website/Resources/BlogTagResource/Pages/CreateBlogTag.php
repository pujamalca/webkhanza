<?php

namespace App\Filament\Clusters\Website\Resources\BlogTagResource\Pages;

use App\Filament\Clusters\Website\Resources\BlogTagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogTag extends CreateRecord
{
    protected static string $resource = BlogTagResource::class;
}