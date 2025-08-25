<?php

namespace App\Filament\Clusters\UserRole;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class UserRoleCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getNavigationLabel(): string
    {
        return 'Administrator';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }
}
