<?php

namespace App\Filament\Clusters\Marketing\Resources;

use App\Filament\Clusters\Marketing\MarketingCluster;
use App\Filament\Clusters\Marketing\Resources\PatientMarketingResource\Pages;
use App\Models\RegPeriksa;
use App\Models\MarketingCategory;
use App\Models\MarketingPatientTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class PatientMarketingResource extends Resource
{
    protected static ?string $model = RegPeriksa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = MarketingCluster::class;

    protected static ?string $navigationLabel = 'Data Pasien Marketing';

    protected static ?string $modelLabel = 'Pasien Marketing';

    protected static ?string $pluralModelLabel = 'Data Pasien Marketing';

    public static function table(Table $table): Table
    {
        $baseColumns = [
            Tables\Columns\TextColumn::make('pasien.nm_pasien')
                ->label('Nama Pasien')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('penjab.png_jawab')
                ->label('Cara Bayar')
                ->searchable()
                ->badge()
                ->color(fn($state) => match(strtolower($state ?? '')) {
                    'bpjs kesehatan' => 'success',
                    'umum' => 'info', 
                    'pribadi' => 'warning',
                    default => 'gray'
                }),
            Tables\Columns\TextColumn::make('pasien.no_peserta')
                ->label('No. Peserta')
                ->searchable()
                ->copyable()
                ->copyMessage('No. Peserta berhasil disalin!')
                ->tooltip('Klik untuk menyalin'),
            Tables\Columns\TextColumn::make('pasien.no_ktp')
                ->label('NIK')
                ->searchable()
                ->copyable()
                ->copyMessage('NIK berhasil disalin!')
                ->tooltip('Klik untuk menyalin'),
            Tables\Columns\TextColumn::make('tgl_registrasi')
                ->label('Tgl Registrasi')
                ->date()
                ->sortable(),
        ];

        // Tambah kolom dinamis untuk setiap kategori marketing (hanya untuk patient marketing)
        $categories = MarketingCategory::active()->forPatientMarketing()->orderBy('name')->get();
        $categoryColumns = [];
        
        foreach ($categories as $category) {
            $categoryColumns[] = Tables\Columns\ViewColumn::make("category_{$category->id}")
                ->label($category->name)
                ->view('filament.tables.columns.marketing-category-status')
                ->viewData(['category_id' => $category->id]);
        }

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('tgl_registrasi', today()))
            ->columns(array_merge($baseColumns, $categoryColumns))
            ->filters([
                Tables\Filters\Filter::make('all_dates')
                    ->label('Semua Tanggal')
                    ->query(fn (Builder $query): Builder => $query->withoutGlobalScope('today'))
                    ->toggle(),
                Tables\Filters\SelectFilter::make('quick_date')
                    ->label('Tanggal Cepat')
                    ->options([
                        now()->format('Y-m-d') => 'Hari Ini (' . now()->format('d/m/Y') . ')',
                        now()->subDay()->format('Y-m-d') => 'Kemarin (' . now()->subDay()->format('d/m/Y') . ')',
                        now()->subDays(2)->format('Y-m-d') => now()->subDays(2)->format('d/m/Y'),
                        now()->subDays(3)->format('Y-m-d') => now()->subDays(3)->format('d/m/Y'),
                        now()->subDays(4)->format('Y-m-d') => now()->subDays(4)->format('d/m/Y'),
                        now()->subDays(5)->format('Y-m-d') => now()->subDays(5)->format('d/m/Y'),
                        now()->subDays(6)->format('Y-m-d') => now()->subDays(6)->format('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            return $query->withoutGlobalScope('today')->whereDate('tgl_registrasi', $data['value']);
                        }
                        return $query;
                    }),
                Tables\Filters\Filter::make('tgl_registrasi')
                    ->label('Filter Tanggal Registrasi')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // Jika ada filter tanggal, override default today filter
                        if ($data['dari_tanggal'] || $data['sampai_tanggal']) {
                            $query = $query->withoutGlobalScope('today');
                        }
                        
                        return $query
                            ->when(
                                $data['dari_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y'))
                                ->removeField('dari_tanggal');
                        }
                        
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y'))
                                ->removeField('sampai_tanggal');
                        }
                        
                        return $indicators;
                    }),
            ])
            ->defaultSort('tgl_registrasi', 'desc')
            ->actions([
                //
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatientMarketing::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('patient_marketing_view');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['pasien.marketingTasks.category', 'pasien', 'dokter', 'poliklinik', 'penjab']);
    }
}