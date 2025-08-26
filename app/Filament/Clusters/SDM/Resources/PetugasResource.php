<?php

namespace App\Filament\Clusters\SDM\Resources;

use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\CreatePetugas;
use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\EditPetugas;
use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\ListPetugas;
use App\Filament\Clusters\SDM\Resources\PetugasResource\Pages\ViewPetugas;
use App\Filament\Clusters\SDM\SDMCluster;
use App\Models\Petugas;
use App\Models\Pegawai;
use App\Models\Jabatan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PetugasResource extends Resource
{
    protected static ?string $model = Petugas::class;

    protected static ?string $cluster = SDMCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationLabel(): string
    {
        return 'Petugas';
    }

    public static function getModelLabel(): string
    {
        return 'Petugas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Petugas';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('petugas_read');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('petugas_create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('petugas_update');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('petugas_delete');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Identitas')
                    ->description('Informasi dasar petugas')
                    ->schema([
                        Select::make('nip')
                            ->label('NIK (Pilih dari Pegawai)')
                            ->relationship(
                                'pegawai', 
                                'nik',
                                fn ($query, $record) => $query->whereNotIn('nik', function ($subQuery) use ($record) {
                                    $subQuery->select('nip')
                                        ->from('petugas')
                                        ->whereNotNull('nip');
                                    // Allow current record's NIK when editing
                                    if ($record && $record->nip) {
                                        $subQuery->where('nip', '!=', $record->nip);
                                    }
                                })
                            )
                            ->getOptionLabelFromRecordUsing(fn (Pegawai $record): string => "{$record->nik} - {$record->nama}")
                            ->searchable(['nik', 'nama'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $pegawai = Pegawai::where('nik', $state)->first();
                                    if ($pegawai) {
                                        // Auto-fill from pegawai
                                        $set('nama', $pegawai->nama);
                                        $set('tmp_lahir', $pegawai->tmp_lahir);
                                        $set('alamat', $pegawai->alamat);
                                        $set('gol_darah', $pegawai->gol_darah);
                                        $set('agama', $pegawai->agama);
                                        $set('no_telp', $pegawai->no_telp);
                                        $set('email', $pegawai->email);
                                        
                                        // Convert jk from Pria/Wanita to L/P for petugas
                                        if ($pegawai->jk === 'Pria') {
                                            $set('jk', 'L');
                                        } elseif ($pegawai->jk === 'Wanita') {
                                            $set('jk', 'P');
                                        }
                                        
                                        // Set jabatan from pegawai.jbtn to kd_jbtn
                                        if ($pegawai->jbtn && $pegawai->jbtn !== '-' && $pegawai->jbtn !== '') {
                                            // Find jabatan by name (exact or partial match)
                                            $jabatan = \App\Models\Jabatan::where('nm_jbtn', 'LIKE', '%' . $pegawai->jbtn . '%')
                                                ->orWhere('nm_jbtn', $pegawai->jbtn)
                                                ->first();
                                            if ($jabatan) {
                                                $set('kd_jbtn', $jabatan->kd_jbtn);
                                            }
                                        }
                                        
                                        // Set default status aktif
                                        $set('status', 1);
                                    }
                                }
                            })
                            ->columnSpan(2),
                        
                        TextInput::make('nama')
                            ->label('Nama Petugas')
                            ->required()
                            ->maxLength(50)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        
                        Select::make('jk')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(4),

                Section::make('Data Pribadi')
                    ->description('Informasi pribadi petugas (Sebagian auto-filled, sebagian input manual)')
                    ->schema([
                        TextInput::make('tmp_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(20)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                        
                        DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->columnSpan(1),
                        
                        Select::make('gol_darah')
                            ->label('Golongan Darah')
                            ->options([
                                'A' => 'A',
                                'B' => 'B', 
                                'O' => 'O',
                                'AB' => 'AB',
                                '-' => 'Tidak Diketahui',
                            ])
                            ->columnSpan(1),
                        
                        TextInput::make('agama')
                            ->label('Agama')
                            ->maxLength(12)
                            ->columnSpan(1),
                        
                        Select::make('stts_nikah')
                            ->label('Status Nikah')
                            ->options([
                                'BELUM MENIKAH' => 'Belum Menikah',
                                'MENIKAH' => 'Menikah', 
                                'JANDA' => 'Janda',
                                'DUDHA' => 'Dudha',
                                'JOMBLO' => 'Jomblo',
                            ])
                            ->columnSpan(2),
                    ])
                    ->columns(4),

                Section::make('Kontak & Alamat')
                    ->description('Informasi kontak petugas (Auto-filled dari data pegawai, dapat diedit)')
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpan(2),
                        
                        TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(13)
                            ->columnSpan(1),
                        
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(50)
                            ->columnSpan(1),
                    ])
                    ->columns(4),

                Section::make('Data Profesi')
                    ->description('Informasi jabatan dan status')
                    ->schema([
                        Select::make('kd_jbtn')
                            ->label('Jabatan')
                            ->relationship('jabatan', 'nm_jbtn')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('kd_jbtn')
                                    ->label('Kode Jabatan')
                                    ->required()
                                    ->maxLength(5),
                                TextInput::make('nm_jbtn')
                                    ->label('Nama Jabatan')
                                    ->required()
                                    ->maxLength(25),
                            ])
                            ->columnSpan(2),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                1 => 'Aktif',
                                0 => 'Non Aktif',
                            ])
                            ->required()
                            ->default(1)
                            ->columnSpan(2),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->label('NIK')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama')
                    ->label('Nama Petugas')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('jk')
                    ->label('JK')
                    ->formatStateUsing(fn (string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->sortable(),
                
                TextColumn::make('jabatan.nm_jbtn')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum ada jabatan'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ]);
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
            'index' => ListPetugas::route('/'),
            'create' => CreatePetugas::route('/create'),
            'view' => ViewPetugas::route('/{record}'),
            'edit' => EditPetugas::route('/{record}/edit'),
        ];
    }
}