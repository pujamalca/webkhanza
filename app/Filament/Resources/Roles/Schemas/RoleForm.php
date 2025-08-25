<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
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

                Section::make('Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Permission')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                $permissions = Permission::all()->sortBy('name');
                                $options = [];
                                
                                foreach ($permissions as $permission) {
                                    $parts = explode('_', $permission->name);
                                    $group = ucfirst($parts[0] ?? 'Other');
                                    $action = ucfirst($parts[1] ?? '');
                                    
                                    $label = $group . ' - ' . $action;
                                    $options[$permission->id] = $label;
                                }
                                
                                return $options;
                            })
                            ->columns(3)
                            ->bulkToggleable()
                            ->searchable()
                            ->helperText('Pilih permission yang akan diberikan kepada role ini. Gunakan "Select All" untuk memilih semua permission.'),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
