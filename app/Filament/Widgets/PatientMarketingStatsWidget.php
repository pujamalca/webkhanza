<?php

namespace App\Filament\Widgets;

use App\Models\MarketingCategory;
use App\Models\MarketingPatientTask;
use App\Models\RegPeriksa;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PatientMarketingStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Get patient marketing categories count
        $patientMarketingCategoriesCount = MarketingCategory::active()
            ->forPatientMarketing()
            ->count();

        // Get today's registrations count
        $todayRegistrations = RegPeriksa::whereDate('tgl_registrasi', today())->count();

        // Get total completed tasks for patient marketing today
        $todayCompletedTasks = MarketingPatientTask::whereHas('category', function($query) {
                $query->forPatientMarketing();
            })
            ->whereDate('completed_at', today())
            ->where('is_completed', true)
            ->count();

        // Get total tasks that should be completed today (registrations * categories)
        $totalPossibleTasks = $todayRegistrations * $patientMarketingCategoriesCount;

        // Calculate completion percentage
        $completionPercentage = $totalPossibleTasks > 0 
            ? round(($todayCompletedTasks / $totalPossibleTasks) * 100, 1)
            : 0;

        // Get pending tasks count
        $pendingTasks = $totalPossibleTasks - $todayCompletedTasks;

        // Get this week's statistics
        $weekCompletedTasks = MarketingPatientTask::whereHas('category', function($query) {
                $query->forPatientMarketing();
            })
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('is_completed', true)
            ->count();

        return [
            Stat::make('Pasien Hari Ini', $todayRegistrations)
                ->description('Total registrasi hari ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Tugas Selesai Hari Ini', $todayCompletedTasks)
                ->description($pendingTasks > 0 ? "{$pendingTasks} tugas pending" : 'Semua tugas selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($completionPercentage >= 80 ? 'success' : ($completionPercentage >= 50 ? 'warning' : 'danger')),

            Stat::make('Progress Hari Ini', $completionPercentage . '%')
                ->description("Dari {$totalPossibleTasks} total tugas")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($completionPercentage >= 80 ? 'success' : ($completionPercentage >= 50 ? 'warning' : 'danger')),

            Stat::make('Kategori Aktif', $patientMarketingCategoriesCount)
                ->description('Kategori checklist tersedia')
                ->descriptionIcon('heroicon-m-tag')
                ->color('primary'),

            Stat::make('Tugas Minggu Ini', $weekCompletedTasks)
                ->description('Total selesai dalam minggu')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->can('patient_marketing_view');
    }
}
