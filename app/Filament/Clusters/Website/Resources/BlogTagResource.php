<?php

namespace App\Filament\Clusters\Website\Resources;

use App\Filament\Clusters\Website\Resources\BlogTagResource\Pages\CreateBlogTag;
use App\Filament\Clusters\Website\Resources\BlogTagResource\Pages\EditBlogTag;
use App\Filament\Clusters\Website\Resources\BlogTagResource\Pages\ListBlogTags;
use App\Filament\Clusters\Website\WebsiteCluster;
use App\Models\BlogTag;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;

class BlogTagResource extends Resource
{
    protected static ?string $model = BlogTag::class;
    
    protected static ?string $cluster = WebsiteCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Tags';
    
    protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?int $navigationSort = 3;

    // Permission-based navigation
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_blog_tag') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_blog_tag') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_blog_tag') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_blog_tag') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('force_delete_blog_tag') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('restore_blog_tag') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Tag Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                            
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(BlogTag::class, 'slug', ignoreRecord: true),
                            
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->rows(3),
                            
                        Forms\Components\ColorPicker::make('color')
                            ->default('#10b981'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('SEO Settings')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->maxLength(60)
                            ->helperText('Leave blank to use tag name'),
                            
                        Forms\Components\Textarea::make('meta_description')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Leave blank to use description'),
                            
                        Forms\Components\TagsInput::make('meta_keywords')
                            ->separator(',')
                            ->placeholder('Add keywords'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\ColorColumn::make('color'),
                    
                Tables\Columns\TextColumn::make('blogs_count')
                    ->label('Posts')
                    ->counts('blogs'),
                    
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => ListBlogTags::route('/'),
            'create' => CreateBlogTag::route('/create'),
            'edit' => EditBlogTag::route('/{record}/edit'),
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