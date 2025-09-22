<?php

namespace App\Filament\Resources\Erm;

use App\Filament\Resources\Erm\RegistrationTemplateResource\Pages;
use App\Models\RegistrationTemplate;
use App\Models\Dokter;
use App\Models\Poliklinik;
use App\Models\Penjab;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Icons\Heroicon;

class RegistrationTemplateResource extends Resource
{
    protected static ?string $model = RegistrationTemplate::class;

    protected static ?int $navigationSort = -10000;

    public static function getNavigationGroup(): ?string
    {
        return 'Electronic Medical Record';
    }


    protected static ?string $slug = 'erm/registration-templates';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'Template Registrasi';

    protected static ?string $modelLabel = 'Template Registrasi';

    protected static ?string $pluralModelLabel = 'Template Registrasi';

    
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Template')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Template')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Template DPP Reguler'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi singkat tentang template ini'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Template aktif akan muncul di form registrasi cepat'),
                    ]),

                Section::make('Pengaturan Default')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('kd_dokter')
                                    ->label('Dokter Default')
                                    ->required()
                                    ->searchable()
                                    ->options(Dokter::all()->pluck('nm_dokter', 'kd_dokter')),

                                Select::make('kd_poli')
                                    ->label('Poliklinik Default')
                                    ->required()
                                    ->searchable()
                                    ->options(Poliklinik::all()->pluck('nm_poli', 'kd_poli')),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('kd_pj')
                                    ->label('Cara Bayar Default')
                                    ->required()
                                    ->searchable()
                                    ->options(Penjab::all()->pluck('png_jawab', 'kd_pj')),

                                TextInput::make('biaya_reg')
                                    ->label('Biaya Registrasi')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->minValue(0),

                                Select::make('status_lanjut')
                                    ->label('Status Lanjut')
                                    ->required()
                                    ->options([
                                        'Ralan' => 'Rawat Jalan',
                                        'Ranap' => 'Rawat Inap',
                                    ])
                                    ->default('Ralan'),
                            ]),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('poliklinik.nm_poli')
                    ->label('Poliklinik')
                    ->searchable(),

                Tables\Columns\TextColumn::make('penjab.png_jawab')
                    ->label('Cara Bayar')
                    ->badge()
                    ->color(fn($state) => match(strtolower($state ?? '')) {
                        'bpjs kesehatan' => 'success',
                        'umum' => 'info',
                        'pribadi' => 'warning',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('biaya_reg')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active_only')
                    ->label('Hanya Aktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('Edit'),
                \Filament\Actions\Action::make('toggle_active')
                    ->label('Toggle Status')
                    ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn($record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn($record) => $record->update(['is_active' => !$record->is_active]))
                    ->requiresConfirmation()
                    ->modalHeading('Ubah Status Template')
                    ->modalDescription(fn($record) => $record->is_active 
                        ? 'Template akan dinonaktifkan dan tidak akan muncul di form registrasi cepat.' 
                        : 'Template akan diaktifkan dan akan muncul di form registrasi cepat.')
                    ->modalSubmitActionLabel('Ubah Status'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrationTemplates::route('/'),
            'create' => Pages\CreateRegistrationTemplate::route('/create'),
            'edit' => Pages\EditRegistrationTemplate::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('registration_template_manage');
    }
}