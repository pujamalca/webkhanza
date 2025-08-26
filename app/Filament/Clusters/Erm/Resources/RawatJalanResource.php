<?php

namespace App\Filament\Clusters\Erm\Resources;

use App\Filament\Clusters\Erm\Resources\RawatJalanResource\Pages;
use App\Filament\Clusters\ErmCluster;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Penjab;
use App\Models\Poliklinik;
use App\Models\RegPeriksa;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RawatJalanResource extends Resource
{
    protected static ?string $model = RegPeriksa::class;

    protected static ?string $cluster = ErmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Rawat Jalan';
    }

    public static function getModelLabel(): string
    {
        return 'Registrasi Rawat Jalan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Rawat Jalan';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('rawat_jalan_view');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('rawat_jalan_view_details');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('rawat_jalan_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('rawat_jalan_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('rawat_jalan_delete');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status_lanjut', 'Ralan');
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Registrasi')
                    ->schema([
                        TextInput::make('no_reg')
                            ->label('No. Registrasi')
                            ->required()
                            ->numeric()
                            ->default(function () {
                                $maxReg = RegPeriksa::whereDate('tgl_registrasi', now())
                                    ->max('no_reg');
                                return ($maxReg ?? 0) + 1;
                            }),

                        Hidden::make('no_rawat')
                            ->default(function () {
                                $today = now()->format('Y/m/d');
                                $count = RegPeriksa::whereDate('tgl_registrasi', now())->count() + 1;
                                return $today . '/' . str_pad($count, 6, '0', STR_PAD_LEFT);
                            }),

                        DatePicker::make('tgl_registrasi')
                            ->label('Tanggal Registrasi')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        TimePicker::make('jam_reg')
                            ->label('Jam Registrasi')
                            ->required()
                            ->default(now()->format('H:i:s'))
                            ->seconds(false),

                        Select::make('no_rkm_medis')
                            ->label('Pasien')
                            ->relationship('pasien', 'nm_pasien', function (Builder $query) {
                                return $query->orderBy('nm_pasien');
                            })
                            ->getOptionLabelFromRecordUsing(fn (Pasien $record): string => "{$record->no_rkm_medis} - {$record->nm_pasien}")
                            ->searchable(['no_rkm_medis', 'nm_pasien'])
                            ->required()
                            ->createOptionForm([
                                TextInput::make('no_rkm_medis')
                                    ->label('No. Rekam Medis')
                                    ->required(),
                                TextInput::make('nm_pasien')
                                    ->label('Nama Pasien')
                                    ->required(),
                            ]),

                        Select::make('kd_poli')
                            ->label('Poliklinik')
                            ->relationship('poliklinik', 'nm_poli')
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('kd_dokter', null);
                            }),

                        Select::make('kd_dokter')
                            ->label('Dokter')
                            ->relationship('dokter', 'nm_dokter')
                            ->searchable()
                            ->required(),

                        Select::make('kd_pj')
                            ->label('Cara Bayar')
                            ->relationship('penjab', 'png_jawab')
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Data Penanggung Jawab')
                    ->schema([
                        TextInput::make('p_jawab')
                            ->label('Penanggung Jawab')
                            ->maxLength(30)
                            ->required(),

                        TextInput::make('almt_pj')
                            ->label('Alamat PJ')
                            ->maxLength(60),

                        Select::make('hubunganpj')
                            ->label('Hubungan dengan Pasien')
                            ->options([
                                'KELUARGA' => 'Keluarga',
                                'SAUDARA' => 'Saudara',
                                'ORANG TUA' => 'Orang Tua',
                                'ANAK' => 'Anak',
                                'SUAMI' => 'Suami',
                                'ISTRI' => 'Istri',
                                'LAINNYA' => 'Lainnya',
                            ])
                            ->default('KELUARGA'),
                    ])
                    ->columns(3),

                Section::make('Status & Biaya')
                    ->schema([
                        TextInput::make('biaya_reg')
                            ->label('Biaya Registrasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Select::make('stts')
                            ->label('Status Periksa')
                            ->options([
                                'Belum' => 'Belum Periksa',
                                'Sudah' => 'Sudah Periksa',
                                'Batal' => 'Batal',
                            ])
                            ->default('Belum')
                            ->required(),

                        Select::make('stts_daftar')
                            ->label('Status Daftar')
                            ->options([
                                'Lama' => 'Pasien Lama',
                                'Baru' => 'Pasien Baru',
                            ])
                            ->default('Lama')
                            ->required(),

                        Hidden::make('status_lanjut')
                            ->default('Ralan'),

                        TextInput::make('umurdaftar')
                            ->label('Umur Daftar')
                            ->maxLength(3),

                        Select::make('sttsumur')
                            ->label('Satuan Umur')
                            ->options([
                                'Th' => 'Tahun',
                                'Bl' => 'Bulan',
                                'Hr' => 'Hari',
                            ])
                            ->default('Th'),

                        Select::make('status_bayar')
                            ->label('Status Bayar')
                            ->options([
                                'Belum Bayar' => 'Belum Bayar',
                                'Sudah Bayar' => 'Sudah Bayar',
                            ])
                            ->default('Belum Bayar'),

                        Select::make('status_poli')
                            ->label('Status Poli')
                            ->options([
                                'Lama' => 'Kunjungan Lama',
                                'Baru' => 'Kunjungan Baru',
                            ])
                            ->default('Lama'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('no_reg')
                    ->label('No. Reg')
                    ->sortable(),

                TextColumn::make('tgl_registrasi')
                    ->label('Tgl Registrasi')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('jam_reg')
                    ->label('Jam')
                    ->time('H:i'),

                TextColumn::make('pasien.no_rkm_medis')
                    ->label('No. RM')
                    ->searchable(),

                TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->weight('bold')
                    ->limit(30),

                TextColumn::make('pasien.jk')
                    ->label('JK')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'L' => 'blue',
                        'P' => 'pink',
                        default => 'gray',
                    }),

                TextColumn::make('poliklinik.nm_poli')
                    ->label('Poliklinik')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('penjab.png_jawab')
                    ->label('Cara Bayar')
                    ->badge()
                    ->color('success'),

                TextColumn::make('stts')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Belum' => 'warning',
                        'Sudah' => 'success',
                        'Batal' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('biaya_reg')
                    ->label('Biaya')
                    ->money('IDR')
                    ->alignEnd(),

                TextColumn::make('stts_daftar')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'success',
                        'Lama' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('kd_poli')
                    ->label('Poliklinik')
                    ->relationship('poliklinik', 'nm_poli')
                    ->searchable(),

                SelectFilter::make('kd_dokter')
                    ->label('Dokter')
                    ->relationship('dokter', 'nm_dokter')
                    ->searchable(),

                SelectFilter::make('kd_pj')
                    ->label('Cara Bayar')
                    ->relationship('penjab', 'png_jawab'),

                SelectFilter::make('stts')
                    ->label('Status Periksa')
                    ->options([
                        'Belum' => 'Belum Periksa',
                        'Sudah' => 'Sudah Periksa',
                        'Batal' => 'Batal',
                    ]),

                SelectFilter::make('stts_daftar')
                    ->label('Status Daftar')
                    ->options([
                        'Lama' => 'Pasien Lama',
                        'Baru' => 'Pasien Baru',
                    ]),

                Filter::make('tgl_registrasi')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('Dari Tanggal'),
                        DatePicker::make('to_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '>=', $date)
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_registrasi', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from_date'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['from_date'])->format('d M Y');
                        }

                        if ($data['to_date'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['to_date'])->format('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('tgl_registrasi', 'desc')
            ->defaultSort('jam_reg', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRawatJalans::route('/'),
            'create' => Pages\CreateRawatJalan::route('/create'),
            'view' => Pages\ViewRawatJalan::route('/{record}'),
            'edit' => Pages\EditRawatJalan::route('/{record}/edit'),
        ];
    }
}