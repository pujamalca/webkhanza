<?php

namespace App\Filament\Clusters\Erm\Resources;

use App\Filament\Clusters\ErmCluster;
use App\Filament\Clusters\Erm\Resources\QuickRegistrationResource\Pages;
use App\Models\RegistrationTemplate;
use App\Models\RegPeriksa;
use App\Models\Pasien;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Icons\Heroicon;

class QuickRegistrationResource extends Resource
{
    protected static ?string $model = RegPeriksa::class;

    protected static ?string $cluster = ErmCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = 'Registrasi Cepat';

    protected static ?string $modelLabel = 'Registrasi Cepat';

    protected static ?string $pluralModelLabel = 'Registrasi Cepat';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->schema([
                        Select::make('no_rkm_medis')
                            ->label('Cari Pasien')
                            ->placeholder('Ketik No. RM / Nama / NIK / No. Kartu untuk mencari pasien')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                Pasien::with('marketingTasks')
                                    ->where('no_rkm_medis', 'like', "%{$search}%")
                                    ->orWhere('nm_pasien', 'like', "%{$search}%")
                                    ->orWhere('no_ktp', 'like', "%{$search}%")
                                    ->orWhere('no_peserta', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($patient) {
                                        $label = "{$patient->no_rkm_medis} - {$patient->nm_pasien}";
                                        
                                        // Add patient identifiers
                                        if ($patient->no_ktp) {
                                            $label .= " (NIK: {$patient->no_ktp})";
                                        }
                                        if ($patient->no_peserta) {
                                            $label .= " (Kartu: {$patient->no_peserta})";
                                        }
                                        
                                        // Add marketing status indicator
                                        $pendingTasks = $patient->marketingTasks()->where('is_completed', false)->count();
                                        $completedTasks = $patient->marketingTasks()->where('is_completed', true)->count();
                                        
                                        if ($pendingTasks > 0) {
                                            $label .= " [📋 Marketing: {$pendingTasks} pending]";
                                        } elseif ($completedTasks > 0) {
                                            $label .= " [✅ Marketing: completed]";
                                        }
                                        
                                        return [$patient->no_rkm_medis => $label];
                                    })->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => 
                                Pasien::where('no_rkm_medis', $value)->first()?->nm_pasien
                            )
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $patient = Pasien::with('marketingTasks')->where('no_rkm_medis', $state)->first();
                                    if ($patient) {
                                        $set('patient_name', $patient->nm_pasien);
                                        
                                        // Build patient info with marketing status
                                        $info = "NIK: {$patient->no_ktp} | Kartu: {$patient->no_peserta}";
                                        
                                        $pendingTasks = $patient->marketingTasks()->where('is_completed', false)->count();
                                        $completedTasks = $patient->marketingTasks()->where('is_completed', true)->count();
                                        
                                        if ($pendingTasks > 0) {
                                            $info .= " | 📋 Marketing: {$pendingTasks} task pending";
                                        } elseif ($completedTasks > 0) {
                                            $info .= " | ✅ Marketing: {$completedTasks} task completed";
                                        } else {
                                            $info .= " | 📋 Marketing: No tasks";
                                        }
                                        
                                        $set('patient_info', $info);
                                        
                                        // Auto-detect status daftar berdasarkan history registrasi
                                        $hasRegistration = $patient->regPeriksa()->count() > 0;
                                        $set('stts_daftar', $hasRegistration ? 'Lama' : 'Baru');
                                    }
                                } else {
                                    $set('patient_name', '');
                                    $set('patient_info', '');
                                    $set('stts_daftar', '');
                                }
                            })
                            ->required(),

                        TextInput::make('patient_name')
                            ->label('Nama Pasien')
                            ->disabled(),

                        TextInput::make('patient_info')
                            ->label('Info Pasien')
                            ->disabled(),

                        TextInput::make('stts_daftar')
                            ->label('Status Daftar')
                            ->disabled()
                            ->dehydrated(),
                    ]),

                Section::make('Template Registrasi')
                    ->schema([
                        Select::make('template_id')
                            ->label('Pilih Template')
                            ->options(RegistrationTemplate::active()->get()->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $template = RegistrationTemplate::find($state);
                                    if ($template) {
                                        $set('kd_dokter', $template->kd_dokter);
                                        $set('kd_poli', $template->kd_poli);
                                        $set('kd_pj', $template->kd_pj);
                                        $set('biaya_reg', $template->biaya_reg);
                                        $set('status_lanjut', $template->status_lanjut);
                                        
                                        // Show template details
                                        $set('dokter_name', $template->dokter->nm_dokter ?? '');
                                        $set('poli_name', $template->poliklinik->nm_poli ?? '');
                                        $set('pj_name', $template->penjab->png_jawab ?? '');
                                    }
                                }
                            }),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('dokter_name')
                                    ->label('Dokter')
                                    ->disabled(),

                                TextInput::make('poli_name')
                                    ->label('Poliklinik')
                                    ->disabled(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('pj_name')
                                    ->label('Cara Bayar')
                                    ->disabled(),

                                TextInput::make('biaya_reg')
                                    ->label('Biaya Registrasi')
                                    ->disabled()
                                    ->prefix('Rp'),

                                TextInput::make('status_lanjut')
                                    ->label('Status Lanjut')
                                    ->disabled(),
                            ]),
                    ]),

                // Hidden fields for form data
                Hidden::make('kd_dokter'),
                Hidden::make('kd_poli'),
                Hidden::make('kd_pj'),
                Hidden::make('stts_daftar'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tgl_registrasi')
                    ->label('Tgl Registrasi')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_reg')
                    ->label('Jam')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->searchable(),

                Tables\Columns\TextColumn::make('penjab.png_jawab')
                    ->label('Cara Bayar')
                    ->badge(),

                Tables\Columns\TextColumn::make('stts')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'Sudah' => 'success',
                        'Belum' => 'warning',
                        default => 'gray'
                    }),
            ])
            ->defaultSort('jam_reg', 'desc')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuickRegistrations::route('/'),
            'create' => Pages\CreateQuickRegistration::route('/create'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('registration_quick_access');
    }
}