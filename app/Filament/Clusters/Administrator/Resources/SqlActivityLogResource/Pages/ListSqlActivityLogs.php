<?php

namespace App\Filament\Clusters\Administrator\Resources\SqlActivityLogResource\Pages;

use App\Filament\Clusters\Administrator\Resources\SqlActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListSqlActivityLogs extends ListRecords
{
    protected static string $resource = SqlActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since SQL logs are read-only
        ];
    }
}