<?php

namespace App\Filament\Clusters\Administrator\Resources\Roles;

use App\Filament\Clusters\Administrator\Resources\Roles\Pages\CreateRole;
use App\Filament\Clusters\Administrator\Resources\Roles\Pages\EditRole;
use App\Filament\Clusters\Administrator\Resources\Roles\Pages\ListRoles;
use App\Filament\Clusters\Administrator\Resources\Roles\Schemas\RoleForm;
use App\Filament\Clusters\Administrator\Resources\Roles\Tables\RolesTable;
use App\Filament\Clusters\Administrator\AdministratorCluster;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $cluster = AdministratorCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    
    public static function getNavigationLabel(): string
    {
        return 'Role & Permission';
    }


    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('roles_view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('roles_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('roles_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('roles_delete');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
