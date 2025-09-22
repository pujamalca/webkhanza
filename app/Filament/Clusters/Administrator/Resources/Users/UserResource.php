<?php

namespace App\Filament\Clusters\Administrator\Resources\Users;

use App\Filament\Clusters\Administrator\Resources\Users\Pages\CreateUser;
use App\Filament\Clusters\Administrator\Resources\Users\Pages\EditUser;
use App\Filament\Clusters\Administrator\Resources\Users\Pages\ListUsers;
use App\Filament\Clusters\Administrator\Resources\Users\Schemas\UserForm;
use App\Filament\Clusters\Administrator\Resources\Users\Tables\UsersTable;
use App\Filament\Clusters\Administrator\AdministratorCluster;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $cluster = AdministratorCluster::class;

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

    public static function canViewAny(): bool
    {
        return auth()->user()->can('users_view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('users_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('users_edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('users_delete');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
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
