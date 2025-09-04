<?php

namespace App\Filament\Clusters\Website;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class WebsiteCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Website Management';
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';
    
    protected static ?int $navigationSort = 20;
    
    protected static ?string $slug = 'website';
}
