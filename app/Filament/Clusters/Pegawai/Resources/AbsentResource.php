<?php

namespace App\Filament\Clusters\Pegawai\Resources;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;
use App\Filament\Clusters\PegawaiCluster;
use App\Models\Absent;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Intervention\Image\ImageManagerStatic as Image;

class AbsentResource extends Resource
{
    protected static ?string $model = Absent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    
    protected static ?string $cluster = PegawaiCluster::class;

    protected static ?string $navigationLabel = 'Absensi';
    
    protected static ?string $modelLabel = 'Absensi';
    
    protected static ?string $pluralModelLabel = 'Data Absensi';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_own_absent') || auth()->user()->can('view_all_absent');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_absent');
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        
        if ($user->can('edit_absent')) {
            return true;
        }
        
        return $user->can('view_own_absent') && $record->employee_id === $user->id;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        
        if ($user->can('delete_absent')) {
            return true;
        }
        
        return $user->can('view_own_absent') && $record->employee_id === $user->id;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        if ($user->can('view_all_absent')) {
            return parent::getEloquentQuery();
        }
        
        return parent::getEloquentQuery()->where('employee_id', $user->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pegawai')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Pegawai')
                            ->options(function () {
                                $user = auth()->user();
                                if ($user->can('view_all_absent')) {
                                    return User::pluck('name', 'id');
                                }
                                return [$user->id => $user->name];
                            })
                            ->default(fn() => auth()->id())
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn() => !auth()->user()->can('view_all_absent')),
                            
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->default(today())
                            ->maxDate(today()),
                    ])
                    ->columns(2),

                Section::make('Waktu & Status')
                    ->schema([
                        Forms\Components\TimePicker::make('check_in')
                            ->label('Waktu Masuk')
                            ->default(now()->format('H:i'))
                            ->seconds(false),
                            
                        Forms\Components\TimePicker::make('check_out')
                            ->label('Waktu Pulang')
                            ->seconds(false),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'hadir' => 'Hadir',
                                'tidak_hadir' => 'Tidak Hadir',
                                'terlambat' => 'Terlambat',
                                'izin' => 'Izin',
                            ])
                            ->required()
                            ->default('hadir'),
                    ])
                    ->columns(3),

                Section::make('Foto Absensi')
                    ->schema([
                        FileUpload::make('check_in_photo')
                            ->label('Foto Masuk')
                            ->image()
                            ->directory('absent-photos')
                            ->visibility('public')
                            ->maxSize(512) // 500KB
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800')
                                                        ->helperText('Maksimal 500KB. Format: JPG, PNG, WEBP'),
                            
                        FileUpload::make('check_out_photo')
                            ->label('Foto Pulang')
                            ->image()
                            ->directory('absent-photos')
                            ->visibility('public')
                            ->maxSize(512) // 500KB
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800')
                                                        ->helperText('Maksimal 500KB. Format: JPG, PNG, WEBP'),
                    ])
                    ->columns(2),

                Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (opsional)...'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->visible(fn() => auth()->user()->can('view_all_absent')),
                    
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Masuk')
                    ->time('H:i')
                    ->sortable()
                    ->badge()
                    ->color(fn(?string $state): string => $state ? Color::Green : Color::Gray),
                    
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Pulang')
                    ->time('H:i')
                    ->sortable()
                    ->badge()
                    ->color(fn(?string $state): string => $state ? Color::Blue : Color::Gray),
                    
                Tables\Columns\TextColumn::make('total_working_hours')
                    ->label('Total Jam')
                    ->badge()
                    ->color(Color::Indigo),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => Color::Green,
                        'terlambat' => Color::Yellow,
                        'izin' => Color::Blue,
                        'tidak_hadir' => Color::Red,
                        default => Color::Gray,
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        default => $state,
                    }),
                    
                ImageColumn::make('check_in_photo')
                    ->label('Foto Masuk')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->visible(false)
                    ->toggleable(),
                    
                ImageColumn::make('check_out_photo')
                    ->label('Foto Pulang')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->visible(false)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                    ]),
                    
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->format('d/m/Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
                    
                SelectFilter::make('employee_id')
                    ->label('Pegawai')
                    ->options(fn() => User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn() => auth()->user()->can('view_all_absent')),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit'),
                ViewAction::make()
                    ->label('Lihat'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada data absensi')
            ->emptyStateDescription('Silahkan tambahkan data absensi baru.')
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsents::route('/'),
            'create' => Pages\CreateAbsent::route('/create'),
            'edit' => Pages\EditAbsent::route('/{record}/edit'),
            'view' => Pages\ViewAbsent::route('/{record}'),
        ];
    }
}