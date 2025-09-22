<?php

namespace App\Filament\Clusters\Administrator\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(',')
                    ->sortable(),
                    
                IconColumn::make('is_logged_in')
                    ->label('Status Login')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                TextColumn::make('logged_in_at')
                    ->label('Login Aktif')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Tidak aktif'),
                    
                TextColumn::make('device_info')
                    ->label('Perangkat')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            $info = json_decode($state, true);
                            return ($info['browser'] ?? 'Unknown') . ' - ' . ($info['os'] ?? 'Unknown');
                        }
                        return 'Tidak ada info';
                    })
                    ->limit(20)
                    ->tooltip(function ($state) {
                        if ($state) {
                            $info = json_decode($state, true);
                            return "Browser: " . ($info['browser'] ?? 'Unknown') . "\n" .
                                   "OS: " . ($info['os'] ?? 'Unknown') . "\n" .
                                   "IP: " . ($info['last_login_ip'] ?? 'Unknown');
                        }
                        return 'Tidak ada informasi perangkat';
                    }),
                    
                TextColumn::make('last_login_ip')
                    ->label('IP Terakhir')
                    ->searchable()
                    ->placeholder('Belum pernah login'),
                    
                TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Belum pernah login'),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_logged_in')
                    ->label('Sedang Login')
                    ->query(fn (Builder $query): Builder => $query->where('is_logged_in', true)),
                    
                Filter::make('has_roles')
                    ->label('Memiliki Role')
                    ->query(fn (Builder $query): Builder => $query->whereHas('roles')),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),
                    
                Action::make('reset_session')
                    ->label('Reset Session')
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Session User')
                    ->modalDescription(fn ($record) => "Ini akan memaksa logout user '{$record->name}' dan mereset semua data session.")
                    ->modalSubmitActionLabel('Reset Session')
                    ->action(function ($record) {
                        // Log admin action
                        \Log::info('=== ADMIN RESET SESSION ===', [
                            'admin_id' => auth()->id(),
                            'admin_name' => auth()->user()->name,
                            'target_user_id' => $record->id,
                            'target_user_name' => $record->name,
                            'before_reset' => [
                                'is_logged_in' => $record->is_logged_in,
                                'has_device_token' => !is_null($record->device_token),
                                'logged_in_at' => $record->logged_in_at?->toDateTimeString()
                            ]
                        ]);
                        
                        // Fire logout event first (for consistency)
                        if ($record->is_logged_in) {
                            event(new \Illuminate\Auth\Events\Logout('web', $record));
                        }
                        
                        // Clean up all database sessions for this user
                        \DB::table('sessions')
                            ->where('user_id', $record->id)
                            ->delete();
                        
                        // Ensure user is logged out in database
                        $record->setLoggedOut();
                        
                        \Log::info('=== ADMIN RESET SESSION COMPLETED ===', [
                            'admin_id' => auth()->id(),
                            'target_user_id' => $record->id,
                            'sessions_deleted' => \DB::table('sessions')->where('user_id', $record->id)->count(),
                            'after_reset' => [
                                'is_logged_in' => $record->fresh()->is_logged_in,
                                'has_device_token' => !is_null($record->fresh()->device_token)
                            ]
                        ]);
                        
                        Notification::make()
                            ->title('Session berhasil direset')
                            ->body("User '{$record->name}' telah logout paksa dan semua session dihapus")
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->is_logged_in || $record->device_token),
                    
            ])
            ->headerActions([
                Action::make('force_logout_all')
                    ->label('Logout Semua User')
                    ->icon('heroicon-o-power')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Logout Semua User yang Sedang Login')
                    ->modalDescription(function () {
                        $loggedInCount = \App\Models\User::where('is_logged_in', true)->count();
                        return "PERHATIAN: Ini akan logout {$loggedInCount} user yang sedang login di sistem!";
                    })
                    ->modalSubmitActionLabel('Ya, Logout Semua')
                    ->action(function () {
                        $loggedInUsers = \App\Models\User::where('is_logged_in', true)->get();
                        $userCount = $loggedInUsers->count();
                        
                        if ($userCount === 0) {
                            Notification::make()
                                ->title('Tidak ada user yang sedang login')
                                ->body('Semua user sudah dalam status logout')
                                ->info()
                                ->send();
                            return;
                        }
                        
                        \Log::info('=== ADMIN FORCE LOGOUT ALL USERS ===', [
                            'admin_id' => auth()->id(),
                            'admin_name' => auth()->user()->name,
                            'users_to_logout' => $userCount,
                            'user_names' => $loggedInUsers->pluck('name')->toArray()
                        ]);
                        
                        foreach ($loggedInUsers as $user) {
                            // Fire logout event for each user
                            event(new \Illuminate\Auth\Events\Logout('web', $user));
                        }
                        
                        // Clean up all sessions
                        \DB::table('sessions')->delete();
                        
                        \Log::info('=== ADMIN FORCE LOGOUT ALL COMPLETED ===', [
                            'admin_id' => auth()->id(),
                            'users_logged_out' => $userCount
                        ]);
                        
                        Notification::make()
                            ->title('Semua user berhasil logout')
                            ->body("Total {$userCount} user telah di-logout paksa dari sistem")
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => auth()->user()->can('users_edit') && \App\Models\User::where('is_logged_in', true)->exists()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                    
                    \Filament\Actions\BulkAction::make('bulk_reset_sessions')
                        ->label('Reset Session Terpilih')
                        ->icon('heroicon-o-arrow-right-start-on-rectangle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Session Multiple Users')
                        ->modalDescription(fn ($records) => "Ini akan mereset session untuk " . count($records) . " user yang dipilih")
                        ->modalSubmitActionLabel('Reset Sessions')
                        ->action(function ($records) {
                            $resetCount = 0;
                            $userNames = [];
                            
                            \Log::info('=== ADMIN BULK RESET SESSIONS ===', [
                                'admin_id' => auth()->id(),
                                'admin_name' => auth()->user()->name,
                                'users_count' => count($records),
                                'user_ids' => $records->pluck('id')->toArray()
                            ]);
                            
                            foreach ($records as $user) {
                                if ($user->is_logged_in || $user->device_token) {
                                    // Fire logout event
                                    if ($user->is_logged_in) {
                                        event(new \Illuminate\Auth\Events\Logout('web', $user));
                                    }
                                    
                                    // Clean up sessions
                                    \DB::table('sessions')->where('user_id', $user->id)->delete();
                                    
                                    // Set logged out
                                    $user->setLoggedOut();
                                    
                                    $resetCount++;
                                    $userNames[] = $user->name;
                                }
                            }
                            
                            \Log::info('=== ADMIN BULK RESET SESSIONS COMPLETED ===', [
                                'admin_id' => auth()->id(),
                                'users_reset' => $resetCount,
                                'user_names' => $userNames
                            ]);
                            
                            Notification::make()
                                ->title('Bulk reset session berhasil')
                                ->body("Total {$resetCount} user berhasil di-reset: " . implode(', ', array_slice($userNames, 0, 3)) . (count($userNames) > 3 ? '...' : ''))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
