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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\DatePicker;
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
            ->columns(array_merge($baseColumns, $categoryColumns))
            ->filters([
                Filter::make('tgl_registrasi')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal') 
                            ->default(now())
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal']) {
                            $indicators[] = Indicator::make('Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y'))
                                ->removeField('dari_tanggal');
                        }
                        if ($data['sampai_tanggal']) {
                            $indicators[] = Indicator::make('Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y'))
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