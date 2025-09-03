<?php

namespace App\Filament\Clusters\UserRole\Resources;

use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Filament\Clusters\UserRole\Resources\WebsiteIdentityResource\Pages;
use App\Models\WebsiteIdentity;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use BackedEnum;

/**
 * Filament Resource untuk Website Identity
 * 
 * Menggunakan pattern singleton - hanya ada satu record yang dapat diedit
 */
class WebsiteIdentityResource extends Resource
{
    protected static ?string $model = WebsiteIdentity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;
    
    protected static ?string $cluster = UserRoleCluster::class;

    protected static ?string $navigationLabel = 'Identitas Website';
    
    protected static ?string $modelLabel = 'Identitas Website';
    
    protected static ?string $pluralModelLabel = 'Identitas Website';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Utama')
                    ->description('Informasi dasar identitas website')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Website')
                            ->placeholder('Contoh: WebKhanza')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->placeholder('Deskripsi singkat tentang website ini...')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('tagline')
                            ->label('Tagline/Motto')
                            ->placeholder('Contoh: Sistem Terpadu untuk Manajemen Pegawai')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Kontak & Alamat')
                    ->description('Informasi kontak dan alamat')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email Kontak')
                            ->email()
                            ->placeholder('admin@example.com')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->placeholder('021-12345678')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Jalan, Kota, Provinsi, Kode Pos')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Media & Branding')
                    ->description('Upload logo dan favicon website')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Website')
                            ->directory('uploads/website-identity')
                            ->disk('public')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->loadingIndicatorPosition('center')
                            ->panelLayout('integrated')
                            ->removeUploadedFileButtonPosition('top-center')
                            ->uploadButtonPosition('center')
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio(null)
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload logo website (max 2MB, format: JPG, PNG, WEBP)')
                            ->nullable(),

                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon')
                            ->directory('uploads/website-identity')
                            ->disk('public')
                            ->image()
                            ->imagePreviewHeight('100')
                            ->loadingIndicatorPosition('center')
                            ->panelLayout('integrated')
                            ->removeUploadedFileButtonPosition('top-center')
                            ->uploadButtonPosition('center')
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio('1:1')
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/ico', 'image/png', 'image/jpeg'])
                            ->helperText('Upload favicon website (max 1MB, format: ICO, PNG, JPG, ukuran ideal: 32x32px)')
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Website')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                    
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->size(40),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => static::getUrl('edit', ['record' => $record]))
                    ->color('primary'),
            ])
            ->bulkActions([
                // Disable bulk actions untuk singleton
            ])
            ->emptyStateHeading('Data identitas website belum ada')
            ->emptyStateDescription('Silakan buat data identitas website terlebih dahulu.')
            ->defaultSort('id', 'asc');
    }

    public static function canCreate(): bool
    {
        // Hanya bisa create jika belum ada data
        return WebsiteIdentity::count() === 0;
    }

    public static function canDelete($record): bool
    {
        // Tidak boleh delete untuk menjaga singleton pattern
        return false;
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
            'index' => Pages\ListWebsiteIdentities::route('/'),
            'create' => Pages\CreateWebsiteIdentity::route('/create'),
            'edit' => Pages\EditWebsiteIdentity::route('/{record}/edit'),
        ];
    }
}