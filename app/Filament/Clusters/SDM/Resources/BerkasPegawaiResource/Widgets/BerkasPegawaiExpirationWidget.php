<?php

namespace App\Filament\Clusters\SDM\Resources\BerkasPegawaiResource\Widgets;

use App\Models\BerkasPegawai;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BerkasPegawaiExpirationWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $summary = BerkasPegawai::getExpirationSummary();
        
        $stats = [];
        
        if ($summary['expiring_1_month'] > 0) {
            $stats[] = Stat::make('Expires 1 Bulan', $summary['expiring_1_month'])
                ->description('Dokumen yang akan expires dalam 1 bulan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger');
        }
        
        if ($summary['expiring_3_months'] > 0) {
            $stats[] = Stat::make('Expires 3 Bulan', $summary['expiring_3_months'])
                ->description('Dokumen yang akan expires dalam 3 bulan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning');
        }
        
        if ($summary['expiring_6_months'] > 0) {
            $stats[] = Stat::make('Expires 6 Bulan', $summary['expiring_6_months'])
                ->description('Dokumen yang akan expires dalam 6 bulan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info');
        }
        
        // If no expiring documents, show a positive message
        if (empty($stats)) {
            $stats[] = Stat::make('Status Berkas', 'Semua Aman')
                ->description('Tidak ada dokumen yang akan expires dalam 6 bulan ke depan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success');
        }
        
        return $stats;
    }
    
    protected function getColumns(): int
    {
        $summary = BerkasPegawai::getExpirationSummary();
        $count = 0;
        
        if ($summary['expiring_1_month'] > 0) $count++;
        if ($summary['expiring_3_months'] > 0) $count++;
        if ($summary['expiring_6_months'] > 0) $count++;
        
        return max(1, $count);
    }
}