<?php

namespace App\Filament\Clusters\Administrator;

use BackedEnum;
use Filament\Clusters\Cluster;

class AdministratorCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    public static function getNavigationLabel(): string
    {
        return 'Administrator';
    }

    public static function getNavigationSort(): ?int
    {
        return 200;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('administrator_access');
    }
}
