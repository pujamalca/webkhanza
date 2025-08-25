<?php

namespace App\Filament\Resources\Users\Tables;

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
                    
                Action::make('reset_device')
                    ->label('Reset Perangkat')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Perangkat User')
                    ->modalDescription('Ini akan memaksa logout user dari semua perangkat dan mengizinkan login dari perangkat baru.')
                    ->modalSubmitActionLabel('Reset Perangkat')
                    ->action(function ($record) {
                        // Logout user dari semua perangkat
                        $record->logoutFromAllDevices();
                        
                        Notification::make()
                            ->title('Perangkat berhasil direset')
                            ->body("User {$record->name} telah logout dari semua perangkat")
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->is_logged_in || $record->device_token),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
