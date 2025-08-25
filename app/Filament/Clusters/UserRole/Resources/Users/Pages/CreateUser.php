<?php

namespace App\Filament\Clusters\UserRole\Resources\Users\Pages;

use App\Filament\Clusters\UserRole\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
