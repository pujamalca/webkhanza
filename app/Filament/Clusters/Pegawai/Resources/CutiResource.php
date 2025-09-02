<?php

namespace App\Filament\Clusters\Pegawai\Resources;

use App\Filament\Clusters\Pegawai\Resources\CutiResource\Pages;
use App\Filament\Clusters\PegawaiCluster;
use App\Models\Cuti;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;

class CutiResource extends Resource
{
    protected static ?string $model = Cuti::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    
    protected static ?string $cluster = PegawaiCluster::class;

    protected static ?string $navigationLabel = 'Cuti';
    
    protected static ?string $modelLabel = 'Cuti';
    
    protected static ?string $pluralModelLabel = 'Data Cuti';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_own_cuti') || auth()->user()->can('view_all_cuti');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_cuti');
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        
        // Jika cuti sudah disetujui atau ditolak, tidak bisa diedit oleh siapa pun
        if (in_array($record->status, ['approved', 'rejected'])) {
            return false;
        }
        
        if ($user->can('edit_cuti')) {
            return true;
        }
        
        // Hanya bisa edit cuti sendiri dan statusnya masih pending
        return $user->can('view_own_cuti') && 
               $record->employee_id === $user->id && 
               $record->status === 'pending';
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        
        // Jika cuti sudah disetujui atau ditolak, tidak bisa dihapus oleh siapa pun
        if (in_array($record->status, ['approved', 'rejected'])) {
            return false;
        }
        
        if ($user->can('delete_cuti')) {
            return true;
        }
        
        // Hanya bisa hapus cuti sendiri dan statusnya masih pending
        return $user->can('view_own_cuti') && 
               $record->employee_id === $user->id && 
               $record->status === 'pending';
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        if ($user->can('view_all_cuti')) {
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
                                if ($user->can('view_all_cuti')) {
                                    return User::pluck('name', 'id');
                                }
                                return [$user->id => $user->name];
                            })
                            ->default(fn() => auth()->id())
                            ->required()
                            ->searchable()
                            ->preload()
                            ->visible(fn() => auth()->user()->can('view_all_cuti'))
                            ->disabled(fn() => !auth()->user()->can('view_all_cuti')),
                            
                        Forms\Components\Hidden::make('employee_id')
                            ->default(fn() => auth()->id())
                            ->visible(fn() => !auth()->user()->can('view_all_cuti')),
                    ]),

                Section::make('Periode Cuti')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->minDate(today())
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $set('end_date', null);
                                }
                            }),
                            
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->minDate(fn(Get $get) => $get('start_date') ?: today())
                            ->reactive()
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $startDate = $get('start_date');
                                if ($startDate && $state) {
                                    $start = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($state);
                                    $totalDays = $start->diffInDays($end) + 1;
                                    $set('calculated_days', $totalDays);
                                }
                            }),
                            
                        Forms\Components\Placeholder::make('calculated_days')
                            ->label('Total Hari')
                            ->content(function (Get $get) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');
                                
                                if ($startDate && $endDate) {
                                    $start = \Carbon\Carbon::parse($startDate);
                                    $end = \Carbon\Carbon::parse($endDate);
                                    $days = $start->diffInDays($end) + 1;
                                    return $days . ' hari';
                                }
                                
                                return 'Pilih tanggal mulai dan selesai';
                            }),
                    ])
                    ->columns(3),

                Section::make('Detail Cuti')
                    ->schema([
                        Forms\Components\Select::make('leave_type')
                            ->label('Jenis Cuti')
                            ->options([
                                'tahunan' => 'Cuti Tahunan',
                                'sakit' => 'Cuti Sakit',
                                'darurat' => 'Cuti Darurat',
                                'melahirkan' => 'Cuti Melahirkan',
                                'menikah' => 'Cuti Menikah',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->default('tahunan'),
                            
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Cuti')
                            ->required()
                            ->rows(4)
                            ->placeholder('Jelaskan alasan pengajuan cuti...'),
                    ])
                    ->columns(1),

                Section::make('Status Persetujuan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu Persetujuan',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('pending')
                            ->disabled(fn() => !auth()->user()->can('approve_cuti')),
                            
                        Forms\Components\Select::make('approved_by')
                            ->label('Disetujui Oleh')
                            ->options(User::whereHas('roles', function($query) {
                                $query->whereIn('name', ['admin', 'hr', 'manager']);
                            })->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Waktu Persetujuan')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->visible(fn() => auth()->user()->can('approve_cuti')),
                    
                // Hidden fields for regular users
                Forms\Components\Hidden::make('status')
                    ->default('pending')
                    ->visible(fn() => !auth()->user()->can('approve_cuti')),
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
                    ->visible(fn() => auth()->user()->can('view_all_cuti')),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_days')
                    ->label('Total Hari')
                    ->badge()
                    ->color('primary')
                    ->suffix(' hari'),
                    
                Tables\Columns\TextColumn::make('leave_type_label')
                    ->label('Jenis Cuti')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Cuti Tahunan' => 'success',
                        'Cuti Sakit' => 'danger',
                        'Cuti Darurat' => 'warning',
                        'Cuti Melahirkan' => 'info',
                        'Cuti Menikah' => 'primary',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                    
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('Belum diproses')
                    ->visible(fn() => auth()->user()->can('view_all_cuti')),
                    
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Waktu Persetujuan')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Belum diproses')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
                    
                SelectFilter::make('leave_type')
                    ->label('Jenis Cuti')
                    ->options([
                        'tahunan' => 'Cuti Tahunan',
                        'sakit' => 'Cuti Sakit',
                        'darurat' => 'Cuti Darurat',
                        'melahirkan' => 'Cuti Melahirkan',
                        'menikah' => 'Cuti Menikah',
                        'lainnya' => 'Lainnya',
                    ]),
                    
                Tables\Filters\Filter::make('date_range')
                    ->label('Periode Cuti')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),
                    
                SelectFilter::make('employee_id')
                    ->label('Pegawai')
                    ->options(fn() => User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn() => auth()->user()->can('view_all_cuti')),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => 
                        auth()->user()->can('approve_cuti') && 
                        $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan Cuti')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pengajuan cuti ini?')
                    ->action(function ($record) {
                        $record->approve(auth()->id());
                        
                        Notification::make()
                            ->title('Pengajuan cuti berhasil disetujui')
                            ->success()
                            ->send();
                    }),
                    
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => 
                        auth()->user()->can('approve_cuti') && 
                        $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan Cuti')
                    ->modalDescription('Apakah Anda yakin ingin menolak pengajuan cuti ini?')
                    ->action(function ($record) {
                        $record->reject(auth()->id());
                        
                        Notification::make()
                            ->title('Pengajuan cuti berhasil ditolak')
                            ->success()
                            ->send();
                    }),
                    
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
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->before(function ($records, $action) {
                            $approvedOrRejected = [];
                            
                            foreach ($records as $record) {
                                if (in_array($record->status, ['approved', 'rejected'])) {
                                    $statusLabel = $record->status === 'approved' ? 'disetujui' : 'ditolak';
                                    $approvedOrRejected[] = "Cuti {$record->employee->name} ({$record->start_date->format('d/m/Y')} - {$record->end_date->format('d/m/Y')}) - Status: {$statusLabel}";
                                }
                            }
                            
                            if (!empty($approvedOrRejected)) {
                                $recordsList = implode('\n', $approvedOrRejected);
                                
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Tidak dapat menghapus pengajuan cuti')
                                    ->body("Pengajuan cuti berikut tidak dapat dihapus karena sudah disetujui/ditolak:\n\n{$recordsList}")
                                    ->persistent()
                                    ->send();
                                    
                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('Belum ada data cuti')
            ->emptyStateDescription('Silahkan tambahkan pengajuan cuti baru.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCutis::route('/'),
            'create' => Pages\CreateCuti::route('/create'),
            'edit' => Pages\EditCuti::route('/{record}/edit'),
            'view' => Pages\ViewCuti::route('/{record}'),
        ];
    }
}