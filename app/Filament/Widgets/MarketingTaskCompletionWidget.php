<?php

namespace App\Filament\Widgets;

use App\Models\BpjsTransferTask;
use App\Models\MarketingCategory;
use App\Models\MarketingPatientTask;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MarketingTaskCompletionWidget extends ChartWidget
{
    protected ?string $heading = 'Completion Rate per Kategori Marketing (7 Hari Terakhir)';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get categories with their completion data for the last 7 days
        $patientMarketingCategories = MarketingCategory::active()
            ->forPatientMarketing()
            ->get();

        $bpjsTransferCategories = MarketingCategory::active()
            ->forBpjsTransfer()
            ->get();

        $datasets = [];
        $labels = [];
        
        // Generate labels for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
        }

        // Patient Marketing Categories Data
        foreach ($patientMarketingCategories as $category) {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                
                $completedTasks = MarketingPatientTask::where('category_id', $category->id)
                    ->whereDate('completed_at', $date)
                    ->where('is_completed', true)
                    ->count();
                
                $data[] = $completedTasks;
            }
            
            $datasets[] = [
                'label' => $category->name . ' (Pasien)',
                'data' => $data,
                'borderColor' => $this->getColorForCategory($category->id, 'patient'),
                'backgroundColor' => $this->getColorForCategory($category->id, 'patient', 0.1),
                'tension' => 0.4,
            ];
        }

        // BPJS Transfer Categories Data
        foreach ($bpjsTransferCategories as $category) {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                
                $completedTasks = BpjsTransferTask::where('category_id', $category->id)
                    ->whereDate('completed_at', $date)
                    ->where('is_completed', true)
                    ->count();
                
                $data[] = $completedTasks;
            }
            
            $datasets[] = [
                'label' => $category->name . ' (BPJS)',
                'data' => $data,
                'borderColor' => $this->getColorForCategory($category->id, 'bpjs'),
                'backgroundColor' => $this->getColorForCategory($category->id, 'bpjs', 0.1),
                'tension' => 0.4,
                'borderDash' => [5, 5], // Dashed line untuk BPJS
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    private function getColorForCategory($categoryId, $type, $alpha = 1): string
    {
        // Color palette
        $colors = [
            'patient' => [
                'rgb(59, 130, 246)', // Blue
                'rgb(16, 185, 129)', // Green  
                'rgb(245, 101, 101)', // Red
                'rgb(251, 191, 36)', // Yellow
                'rgb(139, 92, 246)', // Purple
            ],
            'bpjs' => [
                'rgb(236, 72, 153)', // Pink
                'rgb(34, 197, 94)', // Emerald
                'rgb(249, 115, 22)', // Orange
                'rgb(168, 85, 247)', // Violet
                'rgb(20, 184, 166)', // Teal
            ]
        ];

        $colorIndex = $categoryId % count($colors[$type]);
        $color = $colors[$type][$colorIndex];
        
        if ($alpha < 1) {
            return str_replace('rgb', 'rgba', $color) . ', ' . $alpha . ')';
        }
        
        return $color;
    }

    public static function canView(): bool
    {
        return auth()->user()->can('patient_marketing_view') || auth()->user()->can('bpjs_transfer_view');
    }
}
