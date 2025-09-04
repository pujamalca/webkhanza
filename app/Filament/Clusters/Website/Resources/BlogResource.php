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
                Forms\Components\Tabs::make('Blog Content')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                    
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Blog::class, 'slug', ignoreRecord: true),
                                    
                                Forms\Components\Textarea::make('excerpt')
                                    ->maxLength(500)
                                    ->rows(3),
                                    
                                Forms\Components\Select::make('blog_category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\Textarea::make('description'),
                                        Forms\Components\ColorPicker::make('color'),
                                    ])
                                    ->searchable()
                                    ->preload(),
                                    
                                Forms\Components\Select::make('tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\Textarea::make('description'),
                                        Forms\Components\ColorPicker::make('color'),
                                    ])
                                    ->searchable()
                                    ->preload(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Content')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->fileAttachmentsDirectory('blog-attachments'),
                                    
                                Forms\Components\FileUpload::make('featured_image')
                                    ->image()
                                    ->directory('blog-images')
                                    ->imageEditor(),
                                    
                                Forms\Components\FileUpload::make('gallery_images')
                                    ->image()
                                    ->multiple()
                                    ->directory('blog-galleries')
                                    ->reorderable(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Settings')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'scheduled' => 'Scheduled',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->live(),
                                    
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Publish Date')
                                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['published', 'scheduled'])),
                                    
                                Forms\Components\DateTimePicker::make('scheduled_at')
                                    ->label('Schedule Date')
                                    ->visible(fn (Forms\Get $get) => $get('status') === 'scheduled'),
                                    
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured Post'),
                                    
                                Forms\Components\Toggle::make('allow_comments')
                                    ->label('Allow Comments')
                                    ->default(true),
                                    
                                Forms\Components\Toggle::make('is_sticky')
                                    ->label('Pin to Top'),
                                    
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(60)
                                    ->helperText('Leave blank to use post title'),
                                    
                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->helperText('Leave blank to use excerpt'),
                                    
                                Forms\Components\TagsInput::make('meta_keywords')
                                    ->separator(',')
                                    ->placeholder('Add keywords')
                                    ->helperText('Separate keywords with commas'),
                                    
                                Forms\Components\TextInput::make('canonical_url')
                                    ->url()
                                    ->helperText('Leave blank to use default URL'),
                                    
                                Forms\Components\Fieldset::make('Social Media')
                                    ->schema([
                                        Forms\Components\KeyValue::make('social_meta')
                                            ->keyLabel('Property')
                                            ->valueLabel('Content')
                                            ->addActionLabel('Add Meta Tag')
                                            ->helperText('Custom Open Graph and Twitter Card meta tags'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
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