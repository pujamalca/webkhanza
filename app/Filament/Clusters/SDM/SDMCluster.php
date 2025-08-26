<?php

namespace App\Filament\Clusters\SDM;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class SDMCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'SDM';
    }

    public static function getNavigationSort(): ?int
    {
        return 20;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyPermission([
            'pegawai_read', 'dokter_read', 'petugas_read', 'berkas_pegawai_read'
        ]);
    }
}
