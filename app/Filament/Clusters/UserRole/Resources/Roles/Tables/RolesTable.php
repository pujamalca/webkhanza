<?php

namespace App\Filament\Clusters\UserRole\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('permissions_count')
                    ->label('Jumlah Permission')
                    ->counts('permissions')
                    ->sortable(),
                    
                TextColumn::make('users_count')
                    ->label('Jumlah User')
                    ->counts('users')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->before(function ($records, $action) {
                            $rolesWithUsers = [];
                            
                            foreach ($records as $record) {
                                $usersCount = $record->users()->count();
                                if ($usersCount > 0) {
                                    $rolesWithUsers[] = "'{$record->name}' ({$usersCount} user)";
                                }
                            }
                            
                            if (!empty($rolesWithUsers)) {
                                $rolesList = implode(', ', $rolesWithUsers);
                                
                                Notification::make()
                                    ->danger()
                                    ->title('Tidak dapat menghapus role')
                                    ->body("Role berikut tidak dapat dihapus karena masih memiliki user: {$rolesList}")
                                    ->persistent()
                                    ->send();
                                    
                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
