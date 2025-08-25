<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Informasi Role')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Role')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Nama role akan digunakan dalam sistem permission'),
                            
                        TextInput::make('guard_name')
                            ->label('Guard Name')
                            ->default('web')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Biasanya "web" untuk aplikasi web'),
                    ]),

                Fieldset::make('Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Permission')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                return Permission::all()->groupBy(function ($permission) {
                                    // Group by prefix (before first underscore)
                                    $parts = explode('_', $permission->name);
                                    return $parts[0] ?? 'other';
                                })->map(function ($group) {
                                    return $group->pluck('name', 'id');
                                })->toArray();
                            })
                            ->columns(2)
                            ->gridDirection('row')
                            ->helperText('Pilih permission yang akan diberikan kepada role ini'),
                    ])
                    ->collapsible(),
            ]);
    }
}
