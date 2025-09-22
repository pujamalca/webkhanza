<?php

namespace App\Filament\Clusters\Administrator\Resources;

use App\Filament\Clusters\Administrator\Resources\ActivityLogResource\Pages\ListActivityLogs;
use App\Filament\Clusters\Administrator\AdministratorCluster;
use Spatie\Activitylog\Models\Activity;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $cluster = AdministratorCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    public static function getNavigationLabel(): string
    {
        return 'Activity Log';
    }

    public static function getModelLabel(): string
    {
        return 'Activity Log';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Activity Logs';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    protected static ?string $recordTitleAttribute = 'description';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('activity_logs_view');
    }

    public static function canCreate(): bool
    {
        return false; // Activity logs are read-only
    }

    public static function canEdit($record): bool
    {
        return false; // Activity logs are read-only
    }

    public static function canDelete($record): bool
    {
        return false; // Activity logs are read-only
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Log Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'default' => 'gray',
                        'auth' => 'blue',
                        'users' => 'green',
                        'roles' => 'purple',
                        'sdm' => 'orange',
                        'pegawai' => 'cyan',
                        'dokter' => 'emerald',
                        'petugas' => 'yellow',
                        'berkas_pegawai' => 'red',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        'login' => 'success',
                        'logout' => 'gray',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable()
                    ->limit(50),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('System'),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => 
                        $state ? class_basename($state) : 'N/A'
                    )
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label('Event Type')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'restored' => 'Restored',
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ]),

                SelectFilter::make('log_name')
                    ->label('Log Category')
                    ->options([
                        'default' => 'Default',
                        'auth' => 'Authentication',
                        'users' => 'Users',
                        'roles' => 'Roles',
                        'sdm' => 'SDM',
                        'pegawai' => 'Pegawai',
                        'dokter' => 'Dokter',
                        'petugas' => 'Petugas',
                        'berkas_pegawai' => 'Berkas Pegawai',
                    ]),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from_date')
                            ->label('From Date'),
                        DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from_date'] ?? null) {
                            $indicators[] = 'From: ' . \Carbon\Carbon::parse($data['from_date'])->format('d M Y');
                        }

                        if ($data['to_date'] ?? null) {
                            $indicators[] = 'To: ' . \Carbon\Carbon::parse($data['to_date'])->format('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                // No actions since logs are read-only
            ])
            ->bulkActions([
                // No bulk actions since logs are read-only
            ]);
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
            'index' => ListActivityLogs::route('/'),
        ];
    }
}