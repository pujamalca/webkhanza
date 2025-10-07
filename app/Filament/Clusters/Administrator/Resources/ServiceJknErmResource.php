<?php

namespace App\Filament\Clusters\Administrator\Resources;

use App\Filament\Clusters\Administrator\AdministratorCluster;
use App\Filament\Clusters\Administrator\Resources\ServiceJknErmResource\Pages;
use App\Models\ReferensiMobilejknBpjsErm;
use App\Models\ReferensiMobilejknBpjsTaskid;
use App\Models\Pasien;
use App\Models\RegPeriksa;
use App\Models\Poliklinik;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;

class ServiceJknErmResource extends Resource
{
    protected static ?string $model = RegPeriksa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $cluster = AdministratorCluster::class;

    protected static ?string $navigationLabel = 'Service JKN ERM';

    protected static ?string $modelLabel = 'Service JKN ERM';

    protected static ?string $pluralModelLabel = 'Service JKN ERM';

    protected static ?int $navigationSort = 10;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('administrator_access') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Registrasi')
                    ->description('Informasi data registrasi pasien')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_periksa')
                            ->label('Tanggal Periksa')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('jam_periksa')
                            ->label('Jam Periksa')
                            ->required()
                            ->native(false)
                            ->seconds(true)
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('no_rkm_medis')
                            ->label('Pasien')
                            ->relationship('pasien', 'nm_pasien')
                            ->searchable(['no_rkm_medis', 'nm_pasien'])
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->no_rkm_medis} - {$record->nm_pasien}")
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $pasien = Pasien::find($state);
                                    if ($pasien) {
                                        // Load registrasi terakhir untuk pasien ini
                                        $tanggal = $get('tanggal_periksa');
                                        $jam = $get('jam_periksa');

                                        if ($tanggal && $jam) {
                                            $regPeriksa = RegPeriksa::where('no_rkm_medis', $state)
                                                ->where('tgl_registrasi', $tanggal)
                                                ->where('jam_reg', $jam)
                                                ->first();

                                            if ($regPeriksa) {
                                                $set('no_rawat', $regPeriksa->no_rawat);
                                                $set('kodepoli', $regPeriksa->kd_poli);
                                                $set('nama_poli', $regPeriksa->poliklinik?->nm_poli);
                                            }
                                        }
                                    }
                                }
                            })
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('no_rawat')
                            ->label('No. Rawat')
                            ->placeholder('Akan terisi otomatis dari data registrasi')
                            ->maxLength(17)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Data BPJS')
                    ->description('Informasi terkait BPJS')
                    ->schema([
                        Forms\Components\TextInput::make('no_kartu')
                            ->label('No. Kartu BPJS')
                            ->placeholder('1234567890123')
                            ->maxLength(25)
                            ->columnSpan(1),

                        Forms\Components\Select::make('kodepoli')
                            ->label('Kode Poli BPJS')
                            ->relationship('poliklinik', 'nm_poli', fn($query) => $query->orderBy('nm_poli'))
                            ->searchable(['kd_poli', 'nm_poli'])
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->kd_poli} - {$record->nm_poli}")
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $poli = Poliklinik::find($state);
                                    if ($poli) {
                                        $set('nama_poli', $poli->nm_poli);
                                    }
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('nama_poli')
                            ->label('Nama Poli')
                            ->placeholder('Akan terisi otomatis dari kode poli')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('nomor_referensi')
                            ->label('Nomor Referensi')
                            ->numeric()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('jenis_kunjungan')
                            ->label('Jenis Kunjungan')
                            ->numeric()
                            ->helperText('1: Kunjungan Sakit, 2: Kunjungan Sehat, 3: Konsultasi, 4: Rujukan')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Tracking Task ID')
                    ->description('Status tracking untuk setiap tahapan pelayanan')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('taskid')
                            ->label('Task ID Aktif')
                            ->numeric()
                            ->default(0)
                            ->helperText('0: Belum mulai, 1-7: Tahapan aktif, 99: Selesai')
                            ->columnSpan(2),

                        Forms\Components\DateTimePicker::make('taskid1')
                            ->label('Task 1: Checkin')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('taskid2')
                            ->label('Task 2: Tunggu Poli')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('taskid3')
                            ->label('Task 3: Mulai Periksa')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('taskid4')
                            ->label('Task 4: Panggil Farmasi')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('taskid5')
                            ->label('Task 5: Tunggu Obat')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('taskid6')
                            ->label('Task 6: Obat Disiapkan')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('taskid7')
                            ->label('Task 7: Obat Diserahkan')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('taskid99')
                            ->label('Task 99: Selesai')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->seconds(true)
                            ->columnSpan(1),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status_kirim')
                            ->label('Status Kirim')
                            ->options([
                                'Belum' => 'Belum',
                                'Sudah' => 'Sudah',
                            ])
                            ->default('Belum')
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // Group data per no_rawat dengan task ID dan waktu sebagai kolom
                return $query->from('reg_periksa as r')
                    ->join('pasien as p', 'r.no_rkm_medis', '=', 'p.no_rkm_medis')
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t1', function($join) {
                        $join->on('r.no_rawat', '=', 't1.no_rawat')
                             ->where('t1.taskid', '=', '1');
                    })
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t2', function($join) {
                        $join->on('r.no_rawat', '=', 't2.no_rawat')
                             ->where('t2.taskid', '=', '2');
                    })
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t3', function($join) {
                        $join->on('r.no_rawat', '=', 't3.no_rawat')
                             ->where('t3.taskid', '=', '3');
                    })
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t4', function($join) {
                        $join->on('r.no_rawat', '=', 't4.no_rawat')
                             ->where('t4.taskid', '=', '4');
                    })
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t5', function($join) {
                        $join->on('r.no_rawat', '=', 't5.no_rawat')
                             ->where('t5.taskid', '=', '5');
                    })
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t6', function($join) {
                        $join->on('r.no_rawat', '=', 't6.no_rawat')
                             ->where('t6.taskid', '=', '6');
                    })
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t7', function($join) {
                        $join->on('r.no_rawat', '=', 't7.no_rawat')
                             ->where('t7.taskid', '=', '7');
                    })
                    ->leftJoin('referensi_mobilejkn_bpjs_taskid as t99', function($join) {
                        $join->on('r.no_rawat', '=', 't99.no_rawat')
                             ->where('t99.taskid', '=', '99');
                    })
                    ->whereExists(function($query) {
                        $query->selectRaw(1)
                              ->from('referensi_mobilejkn_bpjs_taskid')
                              ->whereColumn('referensi_mobilejkn_bpjs_taskid.no_rawat', 'r.no_rawat');
                    })
                    ->select([
                        'r.no_rawat',
                        'r.no_rkm_medis',
                        'r.tgl_registrasi',
                        'p.nm_pasien',
                        't1.waktu as waktu_task1',
                        't2.waktu as waktu_task2',
                        't3.waktu as waktu_task3',
                        't4.waktu as waktu_task4',
                        't5.waktu as waktu_task5',
                        't6.waktu as waktu_task6',
                        't7.waktu as waktu_task7',
                        't99.waktu as waktu_task99',
                    ])
                    ->groupBy('r.no_rawat', 'r.no_rkm_medis', 'r.tgl_registrasi', 'p.nm_pasien',
                             't1.waktu', 't2.waktu', 't3.waktu', 't4.waktu', 't5.waktu', 't6.waktu', 't7.waktu', 't99.waktu')
                    ->orderBy('r.no_rawat', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('no_rkm_medis')
                    ->label('No. RM')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('waktu_task1')
                    ->label('Task 1')
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('waktu_task2')
                    ->label('Task 2')
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('waktu_task3')
                    ->label('Task 3')
                    ->dateTime('H:i:s')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('waktu_task4')
                    ->label('Task 4')
                    ->dateTime('H:i:s')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('waktu_task5')
                    ->label('Task 5')
                    ->dateTime('H:i:s')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('waktu_task6')
                    ->label('Task 6')
                    ->dateTime('H:i:s')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('waktu_task7')
                    ->label('Task 7')
                    ->dateTime('H:i:s')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('waktu_task99')
                    ->label('Task 99')
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tgl_registrasi')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn ($query, $date) => $query->whereDate('r.tgl_registrasi', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn ($query, $date) => $query->whereDate('r.tgl_registrasi', '<=', $date),
                            );
                    })
            ])
            ->actions([
                //
            ])
            ->bulkActions([])
            ->defaultSort('no_rawat', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceJknErms::route('/'),
            'create' => Pages\CreateServiceJknErm::route('/create'),
            'edit' => Pages\EditServiceJknErm::route('/{record}/edit'),
            'antrean-per-tanggal' => Pages\AntreanPerTanggal::route('/antrean-per-tanggal'),
            'validasi-task-id' => Pages\ValidasiTaskId::route('/validasi-task-id'),
        ];
    }
}
