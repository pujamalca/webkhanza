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
        return 'User Role';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Role';
    }

    public static function getNavigationSort(): ?int
    {
        return 500;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('administrator_access');
    }
}
