<?php

namespace App\Filament\Clusters\Marketing;

use BackedEnum;
use Filament\Clusters\Cluster;

class MarketingCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    public static function getNavigationLabel(): string
    {
        return 'Marketing';
    }

    public static function getNavigationSort(): ?int
    {
        return 30;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('marketing_access');
    }
}
