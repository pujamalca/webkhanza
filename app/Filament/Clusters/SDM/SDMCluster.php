<?php

namespace App\Filament\Clusters\SDM;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class SDMCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationLabel(): string
    {
        return 'SDM';
    }

    public static function getNavigationSort(): ?int
    {
        return 20;
    }
}
