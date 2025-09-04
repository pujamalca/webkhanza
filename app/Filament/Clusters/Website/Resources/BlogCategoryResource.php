<?php

namespace App\Filament\Clusters\Website\Resources;

use App\Filament\Clusters\Website\Resources\BlogCategoryResource\Pages\CreateBlogCategory;
use App\Filament\Clusters\Website\Resources\BlogCategoryResource\Pages\EditBlogCategory;
use App\Filament\Clusters\Website\Resources\BlogCategoryResource\Pages\ListBlogCategories;
use App\Filament\Clusters\Website\WebsiteCluster;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;
    
    protected static ?string $cluster = WebsiteCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Categories';
    
    protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?int $navigationSort = 2;

    // Permission-based navigation
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_blog_category') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_blog_category') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_blog_category') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_blog_category') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('force_delete_blog_category') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('restore_blog_category') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Textarea::make('description')
                    ->maxLength(1000)
                    ->rows(3),
                    
                Forms\Components\FileUpload::make('image')
                    ->label('Category Image')
                    ->image()
                    ->disk('public')
                    ->directory('blog-categories')
                    ->nullable(),
                    
                Forms\Components\ColorPicker::make('color')
                    ->label('Category Color')
                    ->default('#3b82f6'),
                    
                Forms\Components\TextInput::make('icon')
                    ->label('Icon Class')
                    ->placeholder('fas fa-folder')
                    ->maxLength(255),
                    
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name'),
                    
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
                    
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->size(40),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\ColorColumn::make('color'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('blogs_count')
                    ->label('Posts')
                    ->counts('blogs'),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                    
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name'),
                    
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('sort_order');
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
            'index' => ListBlogCategories::route('/'),
            'create' => CreateBlogCategory::route('/create'),
            'edit' => EditBlogCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}