<?php

namespace App\Filament\Clusters\UserRole\Resources\Users;

use App\Filament\Clusters\UserRole\Resources\Users\Pages\CreateUser;
use App\Filament\Clusters\UserRole\Resources\Users\Pages\EditUser;
use App\Filament\Clusters\UserRole\Resources\Users\Pages\ListUsers;
use App\Filament\Clusters\UserRole\Resources\Users\Schemas\UserForm;
use App\Filament\Clusters\UserRole\Resources\Users\Tables\UsersTable;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $cluster = UserRoleCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    
    public static function getNavigationLabel(): string
    {
        return 'Manajemen User';
    }


    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user_read') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
