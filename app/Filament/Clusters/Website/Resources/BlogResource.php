<?php

namespace App\Filament\Clusters\Website\Resources;

use App\Filament\Clusters\Website\Resources\BlogResource\Pages\CreateBlog;
use App\Filament\Clusters\Website\Resources\BlogResource\Pages\EditBlog;
use App\Filament\Clusters\Website\Resources\BlogResource\Pages\ListBlogs;
use App\Filament\Clusters\Website\WebsiteCluster;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    
    protected static ?string $cluster = WebsiteCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Blog Posts';
    
    protected static ?string $recordTitleAttribute = 'title';
    
    protected static ?int $navigationSort = 1;

    // Permission-based navigation
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_blog') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_blog') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_blog') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_blog') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->can('force_delete_blog') ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can('restore_blog') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Textarea::make('excerpt')
                    ->maxLength(500)
                    ->rows(3),
                    
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->fileAttachmentsDirectory('blog-attachments')
                    ->fileAttachmentsVisibility('public'),
                    
                Forms\Components\FileUpload::make('featured_image')
                    ->label('Featured Image')
                    ->image()
                    ->disk('public')
                    ->directory('blog-images')
                    ->nullable(),
                    
                Forms\Components\Select::make('blog_category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                    
                Forms\Components\Select::make('tags')
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->multiple(),
                    
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
                        'archived' => 'Archived',
                    ])
                    ->default('draft')
                    ->required(),
                    
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publish Date')
                    ->nullable(),
                    
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured Post')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->disk('public')
                    ->size(60)
                    ->circular(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? 'gray')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'warning',
                        'archived' => 'danger',
                    })
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
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
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
                        'archived' => 'Archived',
                    ]),
                    
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\Filter::make('is_featured')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true))
                    ->label('Featured Posts'),
                    
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListBlogs::route('/'),
            'create' => CreateBlog::route('/create'),
            'edit' => EditBlog::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}