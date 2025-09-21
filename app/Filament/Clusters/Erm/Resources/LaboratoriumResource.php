<?php

namespace App\Filament\Clusters\Erm\Resources;

use App\Filament\Clusters\Erm\Resources\LaboratoriumResource\Pages;
use App\Filament\Clusters\ErmCluster;
use App\Models\DetailPeriksaLab;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class LaboratoriumResource extends Resource
{
    protected static ?string $model = DetailPeriksaLab::class;

    protected static ?string $cluster = ErmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return 'Laboratorium';
    }

    public static function getModelLabel(): string
    {
        return 'Hasil Laboratorium';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Hasil Laboratorium';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('regPeriksa.no_rkm_medis')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('templateLaboratorium.Pemeriksaan')
                    ->label('Pemeriksaan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->searchable()
                    ->color(fn ($record) => $record->is_abnormal ? 'danger' : 'gray'),

                TextColumn::make('nilai_rujukan')
                    ->label('Nilai Rujukan')
                    ->placeholder('-'),

                TextColumn::make('templateLaboratorium.satuan')
                    ->label('Satuan')
                    ->placeholder('-'),

                TextColumn::make('keterangan')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match (strtoupper($state)) {
                        'L' => 'warning',
                        'H' => 'danger',
                        'T' => 'danger',
                        default => 'success'
                    })
                    ->formatStateUsing(fn ($state) => match (strtoupper($state)) {
                        'L' => 'Rendah',
                        'H' => 'Tinggi',
                        'T' => 'Tidak Normal',
                        default => 'Normal'
                    }),

                TextColumn::make('tgl_periksa')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('jam')
                    ->label('Jam')
                    ->time('H:i'),
            ])
            ->filters([
                Filter::make('tanggal_range')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_periksa', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_periksa', '<=', $date),
                            );
                    }),

                SelectFilter::make('status')
                    ->label('Status Hasil')
                    ->options([
                        'normal' => 'Normal',
                        'abnormal' => 'Abnormal',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'abnormal') {
                            return $query->whereIn('keterangan', ['L', 'T', 'H']);
                        }
                        if ($data['value'] === 'normal') {
                            return $query->whereNotIn('keterangan', ['L', 'T', 'H']);
                        }
                        return $query;
                    }),

                Filter::make('cari_pasien')
                    ->form([
                        TextInput::make('nama_pasien')
                            ->label('Nama Pasien'),
                        TextInput::make('no_rm')
                            ->label('No. RM'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['nama_pasien'],
                                fn (Builder $query, $nama): Builder => $query->whereHas('regPeriksa.pasien',
                                    fn (Builder $q) => $q->where('nm_pasien', 'like', "%{$nama}%")
                                ),
                            )
                            ->when(
                                $data['no_rm'],
                                fn (Builder $query, $no_rm): Builder => $query->whereHas('regPeriksa',
                                    fn (Builder $q) => $q->where('no_rkm_medis', 'like', "%{$no_rm}%")
                                ),
                            );
                    }),
            ])
            ->defaultSort('tgl_periksa', 'desc')
            ->poll('30s') // Auto refresh setiap 30 detik
            ->striped()
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaboratoriums::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['templateLaboratorium', 'jenisPerawatan', 'regPeriksa.pasien'])
            ->orderBy('tgl_periksa', 'desc')
            ->orderBy('jam', 'desc');
    }
}