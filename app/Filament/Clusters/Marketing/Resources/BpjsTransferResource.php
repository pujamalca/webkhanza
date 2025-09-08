<?php

namespace App\Filament\Clusters\Marketing\Resources;

use App\Filament\Clusters\Marketing\MarketingCluster;
use App\Filament\Clusters\Marketing\Resources\BpjsTransferResource\Pages;
use App\Models\BpjsTransfer;
use App\Models\MarketingCategory;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BpjsTransferResource extends Resource
{
    protected static ?string $model = BpjsTransfer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $cluster = MarketingCluster::class;

    protected static ?string $navigationLabel = 'Pindah BPJS';

    protected static ?string $modelLabel = 'Pindah BPJS';

    protected static ?string $pluralModelLabel = 'Data Pindah BPJS';

    protected static ?string $recordTitleAttribute = 'nama_pasien';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Pasien')
                    ->schema([
                        Forms\Components\TextInput::make('nama_pasien')
                            ->label('Nama Pasien')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('jumlah_keluarga')
                            ->label('Jumlah Anggota Keluarga')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->helperText('Jumlah anggota keluarga yang ikut pindah BPJS'),
                        Forms\Components\TextInput::make('no_peserta_lama')
                            ->label('No. Peserta BPJS Lama')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                    
                Section::make('Kontak & Rencana')
                    ->schema([
                        Forms\Components\TextInput::make('no_telepon')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3),
                        Forms\Components\DatePicker::make('tanggal_rencana_pindah')
                            ->label('Tanggal Rencana Pindah')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(2),
                    
                Section::make('Upload Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_bukti_mjkn')
                            ->label('Foto Bukti MJKn Sudah Pindah')
                            ->image()
                            ->disk('public')
                            ->directory('bpjs-transfers/bukti-mjkn')
                            ->imagePreviewHeight('200')
                            ->required()
                            ->helperText('Upload foto bukti bahwa MJKn sudah dipindah'),
                        Forms\Components\FileUpload::make('foto_pasien')
                            ->label('Foto Pasien')
                            ->image()
                            ->disk('public')
                            ->directory('bpjs-transfers/foto-pasien')
                            ->imagePreviewHeight('200')
                            ->required()
                            ->helperText('Upload foto identitas pasien'),
                    ])
                    ->columns(2),
                    
                Section::make('Status Edukasi')
                    ->schema([
                        Forms\Components\Toggle::make('is_edukasi_completed')
                            ->label('Pasien sudah diedukasi tentang proses pemindahan BPJS')
                            ->default(true)
                            ->helperText('Centang jika pasien sudah dijelaskan mengenai proses pemindahan BPJS'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->helperText('Catatan tambahan mengenai proses pemindahan BPJS'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $baseColumns = [
            Tables\Columns\TextColumn::make('nama_pasien')
                ->label('Nama Pasien')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('jumlah_keluarga')
                ->label('Jml. Keluarga')
                ->alignCenter()
                ->badge()
                ->color('info'),
            Tables\Columns\TextColumn::make('no_peserta_lama')
                ->label('No. Peserta Lama')
                ->searchable()
                ->copyable()
                ->copyMessage('No. Peserta berhasil disalin!')
                ->toggleable(),
            Tables\Columns\TextColumn::make('nik')
                ->label('NIK')
                ->searchable()
                ->copyable()
                ->copyMessage('NIK berhasil disalin!')
                ->toggleable(),
            Tables\Columns\TextColumn::make('tanggal_rencana_pindah')
                ->label('Tgl. Rencana Pindah')
                ->date()
                ->sortable()
                ->toggleable(),
            Tables\Columns\IconColumn::make('is_edukasi_completed')
                ->label('Edukasi')
                ->boolean()
                ->tooltip(fn ($record) => $record->is_edukasi_completed ? 
                    'Edukasi selesai pada ' . $record->edukasi_completed_at?->format('d/m/Y H:i') : 
                    'Belum diedukasi'),
        ];

        // Tambah kolom dinamis untuk setiap kategori marketing (hanya untuk BPJS transfer)
        $categories = MarketingCategory::active()->forBpjsTransfer()->orderBy('name')->get();
        $categoryColumns = [];
        
        foreach ($categories as $category) {
            $categoryColumns[] = Tables\Columns\ViewColumn::make("category_{$category->id}")
                ->label($category->name)
                ->view('filament.tables.columns.bpjs-transfer-category-status')
                ->viewData(['category_id' => $category->id]);
        }

        return $table
            ->columns(array_merge($baseColumns, $categoryColumns))
            ->filters([
                Tables\Filters\TernaryFilter::make('is_edukasi_completed')
                    ->label('Status Edukasi')
                    ->boolean()
                    ->trueLabel('Sudah Edukasi')
                    ->falseLabel('Belum Edukasi')
                    ->native(false),
                Tables\Filters\Filter::make('tanggal_rencana_pindah')
                    ->label('Filter Tanggal Rencana Pindah')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_rencana_pindah', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_rencana_pindah', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                //
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBpjsTransfers::route('/'),
            'create' => Pages\CreateBpjsTransfer::route('/create'),
            'edit' => Pages\EditBpjsTransfer::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('bpjs_transfer_view');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['createdBy', 'edukasiCompletedBy', 'tasks.category', 'tasks.completedBy']);
    }

}