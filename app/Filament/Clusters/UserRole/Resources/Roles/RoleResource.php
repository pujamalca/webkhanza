<?php

namespace App\Filament\Clusters\UserRole\Resources\Roles;

use App\Filament\Clusters\UserRole\Resources\Roles\Pages\CreateRole;
use App\Filament\Clusters\UserRole\Resources\Roles\Pages\EditRole;
use App\Filament\Clusters\UserRole\Resources\Roles\Pages\ListRoles;
use App\Filament\Clusters\UserRole\Resources\Roles\Schemas\RoleForm;
use App\Filament\Clusters\UserRole\Resources\Roles\Tables\RolesTable;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $cluster = UserRoleCluster::class;

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

    public static function canAccess(): bool
    {
        return auth()->user()?->can('role_read') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
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
