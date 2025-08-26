<?php

namespace App\Filament\Clusters\UserRole\Resources;

use App\Filament\Clusters\UserRole\Resources\SqlActivityLogResource\Pages\ListSqlActivityLogs;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use Spatie\Activitylog\Models\Activity;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

class SqlActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $cluster = UserRoleCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket-square';
    
    public static function getNavigationLabel(): string
    {
        return 'SQL Activity Log';
    }

    public static function getModelLabel(): string
    {
        return 'SQL Activity Log';
    }

    public static function getPluralModelLabel(): string
    {
        return 'SQL Activity Logs';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    protected static ?string $recordTitleAttribute = 'description';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('system_logs_access');
    }

    public static function canCreate(): bool
    {
        return false; // SQL logs are read-only
    }

    public static function canEdit($record): bool
    {
        return false; // SQL logs are read-only
    }

    public static function canDelete($record): bool
    {
        return false; // SQL logs are read-only
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('log_name', 'sql_queries');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Query Type & Table')
                    ->searchable()
                    ->badge()
                    ->color(function (string $state): string {
                        if (str_contains($state, 'SELECT')) return 'info';
                        if (str_contains($state, 'INSERT')) return 'success';
                        if (str_contains($state, 'UPDATE')) return 'warning';
                        if (str_contains($state, 'DELETE')) return 'danger';
                        if (str_contains($state, 'CREATE')) return 'purple';
                        if (str_contains($state, 'ALTER')) return 'orange';
                        if (str_contains($state, 'DROP')) return 'red';
                        return 'gray';
                    }),

                TextColumn::make('properties.query')
                    ->label('SQL Query')
                    ->limit(80)
                    ->wrap()
                    ->searchable()
                    ->tooltip(function ($record) {
                        return $record->properties['query'] ?? 'No query';
                    }),

                TextColumn::make('properties.execution_time')
                    ->label('Exec Time')
                    ->badge()
                    ->color(function (?string $state): string {
                        if (!$state) return 'gray';
                        $time = (float) str_replace(' ms', '', $state);
                        if ($time > 100) return 'danger';
                        if ($time > 50) return 'warning';
                        return 'success';
                    })
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('System'),

                TextColumn::make('properties.connection')
                    ->label('DB')
                    ->badge()
                    ->color('blue'),

                TextColumn::make('properties.ip_address')
                    ->label('IP Address')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Executed At')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('query_type')
                    ->label('Query Type')
                    ->options([
                        'SELECT' => 'SELECT',
                        'INSERT' => 'INSERT', 
                        'UPDATE' => 'UPDATE',
                        'DELETE' => 'DELETE',
                        'CREATE' => 'CREATE',
                        'ALTER' => 'ALTER',
                        'DROP' => 'DROP',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'],
                                function (Builder $query, $value): Builder {
                                    return $query->where('description', 'like', "%{$value}%");
                                }
                            );
                    }),

                SelectFilter::make('connection')
                    ->label('Database Connection')
                    ->options([
                        'mariadb' => 'MariaDB',
                        'mysql' => 'MySQL',
                        'sqlite' => 'SQLite',
                        'pgsql' => 'PostgreSQL',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'],
                                function (Builder $query, $value): Builder {
                                    return $query->whereJsonContains('properties->connection', $value);
                                }
                            );
                    }),

                Filter::make('execution_time')
                    ->label('Slow Queries (>50ms)')
                    ->toggle()
                    ->query(function (Builder $query): Builder {
                        return $query->whereRaw("CAST(JSON_EXTRACT(properties, '$.execution_time') AS DECIMAL(10,2)) > 50");
                    }),

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
                                function (Builder $query, $date): Builder {
                                    return $query->whereDate('created_at', '>=', $date);
                                }
                            )
                            ->when(
                                $data['to_date'],
                                function (Builder $query, $date): Builder {
                                    return $query->whereDate('created_at', '<=', $date);
                                }
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
            'index' => ListSqlActivityLogs::route('/'),
        ];
    }
}