<?php

namespace App\Filament\Widgets;

use App\Models\MarketingCategory;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopMarketingCategoriesWidget extends TableWidget
{
    protected static ?string $heading = 'Top Kategori Marketing Berdasarkan Completion Rate';
    
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('category_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'patient_marketing' => 'Data Pasien',
                        'bpjs_transfer' => 'Pindah BPJS',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'patient_marketing' => 'info',
                        'bpjs_transfer' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_completed')
                    ->label('Tugas Selesai')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('completed_today')
                    ->label('Selesai Hari Ini')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('completed_this_week')
                    ->label('Minggu Ini')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('avg_completion_per_day')
                    ->label('Rata-rata/Hari')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('total_completed', 'desc')
            ->paginated(false)
            ->striped()
            ->defaultPaginationPageOption(10);
    }

    protected function getTableQuery(): Builder
    {
        return MarketingCategory::active()
            ->withCount([
                'patientTasks as patient_completed_total' => function ($query) {
                    $query->where('is_completed', true);
                },
                'patientTasks as patient_completed_today' => function ($query) {
                    $query->where('is_completed', true)
                          ->whereDate('completed_at', today());
                },
                'patientTasks as patient_completed_this_week' => function ($query) {
                    $query->where('is_completed', true)
                          ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()]);
                },
            ])
            ->select([
                'marketing_categories.*',
                DB::raw('CASE 
                    WHEN category_type = "patient_marketing" THEN 
                        (SELECT COUNT(*) FROM marketing_patient_tasks WHERE category_id = marketing_categories.id AND is_completed = 1)
                    ELSE 
                        (SELECT COUNT(*) FROM bpjs_transfer_tasks WHERE category_id = marketing_categories.id AND is_completed = 1)
                    END as total_completed'),
                DB::raw('CASE 
                    WHEN category_type = "patient_marketing" THEN 
                        (SELECT COUNT(*) FROM marketing_patient_tasks WHERE category_id = marketing_categories.id AND is_completed = 1 AND DATE(completed_at) = CURDATE())
                    ELSE 
                        (SELECT COUNT(*) FROM bpjs_transfer_tasks WHERE category_id = marketing_categories.id AND is_completed = 1 AND DATE(completed_at) = CURDATE())
                    END as completed_today'),
                DB::raw('CASE 
                    WHEN category_type = "patient_marketing" THEN 
                        (SELECT COUNT(*) FROM marketing_patient_tasks WHERE category_id = marketing_categories.id AND is_completed = 1 AND completed_at >= DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY) AND completed_at < DATE_ADD(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY), INTERVAL 7 DAY))
                    ELSE 
                        (SELECT COUNT(*) FROM bpjs_transfer_tasks WHERE category_id = marketing_categories.id AND is_completed = 1 AND completed_at >= DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY) AND completed_at < DATE_ADD(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY), INTERVAL 7 DAY))
                    END as completed_this_week'),
                DB::raw('ROUND(CASE 
                    WHEN category_type = "patient_marketing" THEN 
                        (SELECT COUNT(*) FROM marketing_patient_tasks WHERE category_id = marketing_categories.id AND is_completed = 1 AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) / 7
                    ELSE 
                        (SELECT COUNT(*) FROM bpjs_transfer_tasks WHERE category_id = marketing_categories.id AND is_completed = 1 AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) / 7
                    END, 1) as avg_completion_per_day'),
            ]);
    }

    public static function canView(): bool
    {
        return auth()->user()->can('patient_marketing_view') || auth()->user()->can('bpjs_transfer_view');
    }
}
