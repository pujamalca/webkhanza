<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class PegawaiCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    
    protected static ?string $navigationLabel = 'Pegawai';
    
    protected static ?string $slug = 'pegawai';
    
    protected static ?int $navigationSort = 3;
    
    public static function getNavigationBadge(): ?string
    {
        // Bisa ditambahkan logic untuk menampilkan badge, misalnya jumlah absensi hari ini
        return null;
    }
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        
        return $user && (
            $user->can('view_own_absent') || 
            $user->can('view_all_absent') ||
            $user->can('view_own_cuti') || 
            $user->can('view_all_cuti')
        );
    }
}