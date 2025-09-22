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

    protected static ?int $navigationSort = 300;

    public static function getNavigationGroup(): ?string
    {
        return 'Pegawai';
    }
    
    public static function getNavigationBadge(): ?string
    {
        // Bisa ditambahkan logic untuk menampilkan badge, misalnya jumlah absensi hari ini
        return null;
    }
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Check if any pegawai permissions exist, if not, allow access for testing
        $permissions = [
            'view_own_absent', 'view_all_absent',
            'view_own_cuti', 'view_all_cuti'
        ];
        
        try {
            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }
            
            // If no permissions found but user is admin/superadmin, allow access
            if ($user->hasRole(['Super Admin', 'Admin', 'HRD Manager'])) {
                return true;
            }
            
        } catch (\Exception $e) {
            // If permission system is not working, allow access for admin roles
            if ($user->hasRole(['Super Admin', 'Admin', 'HRD Manager'])) {
                return true;
            }
        }
        
        return false;
    }
}