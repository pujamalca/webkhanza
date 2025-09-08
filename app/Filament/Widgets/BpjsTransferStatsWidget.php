<?php

namespace App\Filament\Widgets;

use App\Models\BpjsTransfer;
use App\Models\BpjsTransferTask;
use App\Models\MarketingCategory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BpjsTransferStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Get BPJS transfer categories count
        $bpjsTransferCategoriesCount = MarketingCategory::active()
            ->forBpjsTransfer()
            ->count();

        // Get total BPJS transfers
        $totalBpjsTransfers = BpjsTransfer::count();

        // Get today's new transfers
        $todayNewTransfers = BpjsTransfer::whereDate('created_at', today())->count();

        // Get transfers with completed edukasi
        $completedEdukasi = BpjsTransfer::where('is_edukasi_completed', true)->count();

        // Get total completed tasks for BPJS transfer today
        $todayCompletedTasks = BpjsTransferTask::whereHas('category', function($query) {
                $query->forBpjsTransfer();
            })
            ->whereDate('completed_at', today())
            ->where('is_completed', true)
            ->count();

        // Get total tasks that should be completed today (transfers * categories)
        $totalPossibleTasks = $todayNewTransfers * $bpjsTransferCategoriesCount;

        // Calculate completion percentage
        $completionPercentage = $totalPossibleTasks > 0 
            ? round(($todayCompletedTasks / $totalPossibleTasks) * 100, 1)
            : 0;

        // Get transfers with photos uploaded
        $transfersWithPhotos = BpjsTransfer::whereNotNull('foto_bukti_mjkn')
            ->whereNotNull('foto_pasien')
            ->count();

        // Get this week's completed tasks
        $weekCompletedTasks = BpjsTransferTask::whereHas('category', function($query) {
                $query->forBpjsTransfer();
            })
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('is_completed', true)
            ->count();

        // Get transfers with pending tasks (have incomplete checklist)
        $transfersWithPendingTasks = BpjsTransfer::whereHas('tasks', function($query) {
                $query->where('is_completed', false);
            })
            ->orWhereDoesntHave('tasks')
            ->count();

        return [
            Stat::make('Total Pindah BPJS', $totalBpjsTransfers)
                ->description("{$todayNewTransfers} baru hari ini")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Edukasi Selesai', $completedEdukasi)
                ->description('Dari ' . $totalBpjsTransfers . ' total transfer')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Progress Tugas Hari Ini', $completionPercentage . '%')
                ->description("Dari {$totalPossibleTasks} total tugas")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($completionPercentage >= 80 ? 'success' : ($completionPercentage >= 50 ? 'warning' : 'danger')),

            Stat::make('Dokumen Lengkap', $transfersWithPhotos)
                ->description('Foto bukti & pasien terupload')
                ->descriptionIcon('heroicon-m-camera')
                ->color('info'),

            Stat::make('Tugas Pending', $transfersWithPendingTasks)
                ->description('Transfer dengan tugas belum selesai')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($transfersWithPendingTasks > 0 ? 'warning' : 'success'),

            Stat::make('Tugas Minggu Ini', $weekCompletedTasks)
                ->description('Total selesai dalam minggu')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('bpjs_transfer_view');
    }
}
