<?php

namespace App\Filament\Clusters\Pegawai\Resources;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;
use App\Filament\Clusters\PegawaiCluster;
use App\Models\Absent;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\ImageColumn;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\HtmlString;

class AbsentResource extends Resource
{
    protected static ?string $model = Absent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    
    protected static ?string $cluster = PegawaiCluster::class;

    protected static ?string $navigationLabel = 'Absensi';
    
    protected static ?string $modelLabel = 'Absensi';
    
    protected static ?string $pluralModelLabel = 'Data Absensi';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        try {
            return $user->can('view_own_absent') || $user->can('view_all_absent');
        } catch (\Exception $e) {
            // If permission system fails, allow admin roles
            return $user->hasRole(['Super Admin', 'Admin', 'HRD Manager']);
        }
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        try {
            return $user->can('create_absent');
        } catch (\Exception $e) {
            // If permission system fails, allow admin roles
            return $user->hasRole(['Super Admin', 'Admin', 'HRD Manager']);
        }
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        
        if ($user->can('edit_absent')) {
            return true;
        }
        
        return $user->can('view_own_absent') && $record->employee_id === $user->id;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        // Use permission-based access control instead of hardcoded roles
        return $user->can('delete_absent');
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        if ($user->can('view_all_absent')) {
            return parent::getEloquentQuery();
        }
        
        return parent::getEloquentQuery()->where('employee_id', $user->id);
    }

    public static function form(Schema $schema): Schema
    {        
        return $schema
            ->schema([
                Section::make('Absen Masuk')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Pegawai')
                            ->options(function () {
                                $user = auth()->user();
                                if ($user->can('view_all_absent')) {
                                    return User::pluck('name', 'id');
                                }
                                return [$user->id => $user->name];
                            })
                            ->default(fn() => auth()->id())
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn() => !auth()->user()->can('view_all_absent')),
                            
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->default(today())
                            ->disabled()
                            ->maxDate(today()),

                        Forms\Components\Hidden::make('attendance_type')
                            ->default('masuk'),
                            
                        Forms\Components\Hidden::make('status')
                            ->default('hadir'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (opsional)...'),

                        Forms\Components\Placeholder::make('camera_checkin_inline')
                            ->label('Foto Absensi Masuk')
                            ->content(new HtmlString('
                                <div class="space-y-4">
                                    <div class="text-center">
                                        <div id="camera_status_checkin" class="text-sm font-medium">📷 Memuat kamera...</div>
                                    </div>
                                    
                                    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                                        <div id="my_camera_checkin" class="w-full h-full flex items-center justify-center">
                                            <div class="text-gray-500">Memuat kamera...</div>
                                        </div>
                                    </div>
                                    
                                    <div id="results_checkin" class="text-center"></div>
                                    
                                    <div class="flex justify-center space-x-3">
                                        <button id="btn_capture_checkin" type="button"
                                                onclick="takeSnapshotCheckIn()"
                                                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                            📸 Ambil Foto Masuk
                                        </button>
                                        
                                        <button id="btn_retake_checkin" type="button"
                                                onclick="retakePhotoCheckIn()"
                                                class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700"
                                                style="display: none;">
                                            🔄 Foto Ulang
                                        </button>
                                    </div>
                                </div>
                                
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
                                <script>
                                console.log("🚀 CHECK-IN CAMERA SCRIPT EXECUTING");
                                
                                // Configure webcam for check-in
                                if (typeof Webcam !== "undefined") {
                                    Webcam.set({
                                        width: 370,
                                        height: 300,
                                        image_format: "jpeg",
                                        jpeg_quality: 90
                                    });
                                    console.log("✅ Webcam configured for check-in");
                                }
                                
                                // Initialize camera immediately
                                setTimeout(function() {
                                    console.log("🎬 Initializing camera for check-in...");
                                    try {
                                        Webcam.attach("#my_camera_checkin");
                                        document.getElementById("camera_status_checkin").innerHTML = "✅ Kamera aktif - Siap mengambil foto masuk";
                                        console.log("✅ Check-in camera attached successfully");
                                    } catch(e) {
                                        console.error("❌ Check-in camera error:", e);
                                        document.getElementById("camera_status_checkin").innerHTML = "❌ Gagal mengakses kamera";
                                    }
                                }, 1000);
                                
                                // Simple take snapshot function for check-in
                                window.takeSnapshotCheckIn = function() {
                                    console.log("📸 Taking check-in snapshot...");
                                    
                                    Webcam.snap(function(data_uri) {
                                        console.log("📷 Check-in photo captured, length:", data_uri.length);
                                        
                                        // Show preview
                                        document.getElementById("results_checkin").innerHTML = 
                                            "<img src=\"" + data_uri + "\" style=\"max-width: 300px; border-radius: 8px;\"/>";
                                        
                                        // Save to form field
                                        const photoField = document.querySelector("[name=\"check_in_photo\"]");
                                        if (photoField) {
                                            photoField.value = data_uri;
                                            console.log("✅ Photo saved to check_in_photo field");
                                        }
                                        
                                        // Save to localStorage as backup
                                        localStorage.setItem("checkin_photo_backup", data_uri);
                                        
                                        // Send to session via AJAX
                                        fetch("/store-photo-temp", {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json",
                                                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                                            },
                                            body: JSON.stringify({
                                                photo_data: data_uri,
                                                type: "check_in"
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            console.log("✅ Check-in AJAX response:", data);
                                        })
                                        .catch(error => {
                                            console.error("❌ Check-in AJAX error:", error);
                                        });
                                        
                                        // Update UI
                                        document.getElementById("camera_status_checkin").innerHTML = "📸 Foto masuk berhasil diambil!";
                                        document.getElementById("btn_capture_checkin").style.display = "none";
                                        document.getElementById("btn_retake_checkin").style.display = "inline-block";
                                    });
                                };
                                
                                // Simple retake function
                                window.retakePhotoCheckIn = function() {
                                    console.log("🔄 Retaking check-in photo...");
                                    
                                    document.getElementById("results_checkin").innerHTML = "";
                                    document.getElementById("camera_status_checkin").innerHTML = "✅ Kamera aktif - Siap mengambil foto masuk";
                                    document.getElementById("btn_capture_checkin").style.display = "inline-block";
                                    document.getElementById("btn_retake_checkin").style.display = "none";
                                    
                                    // Clear form field
                                    const photoField = document.querySelector("[name=\"check_in_photo\"]");
                                    if (photoField) photoField.value = "";
                                    
                                    // Clear backup
                                    localStorage.removeItem("checkin_photo_backup");
                                };
                                </script>
                            '))
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('check_in_photo')
                            ->label('')
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'display: none;'])
                            ->dehydrated()
                            ->rows(1)
                            ->live(false),
                            
                        // Emergency backup fields untuk memastikan foto terkirim
                        Forms\Components\Textarea::make('check_in_photo_backup')
                            ->label('')
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'display: none;'])
                            ->dehydrated()
                            ->rows(1)
                            ->live(false),
                            
                        Forms\Components\Textarea::make('check_in_photo_emergency')
                            ->label('')
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'display: none;'])
                            ->dehydrated()
                            ->rows(1)
                            ->live(false),
                            
                        Forms\Components\Textarea::make('check_in_photo_emergency_1')
                            ->label('')
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'display: none;'])
                            ->dehydrated()
                            ->rows(1)
                            ->live(false),
                            
                        Forms\Components\Textarea::make('check_in_photo_emergency_2')
                            ->label('')
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'display: none;'])
                            ->dehydrated()
                            ->rows(1)
                            ->live(false),
                            
                        Forms\Components\Textarea::make('check_in_photo_emergency_3')
                            ->label('')
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'display: none;'])
                            ->dehydrated()
                            ->rows(1)
                            ->live(false),
                            
                        // Additional Hidden fields as last resort
                        Forms\Components\Hidden::make('check_in_photo_hidden_1'),
                        Forms\Components\Hidden::make('check_in_photo_hidden_2'),
                        Forms\Components\Hidden::make('check_in_photo_hidden_3'),
                        
                        // Chunked data fields for large photos
                        Forms\Components\Hidden::make('photo_chunk_1'),
                        Forms\Components\Hidden::make('photo_chunk_2'),
                        Forms\Components\Hidden::make('photo_chunk_3'),
                        Forms\Components\Hidden::make('photo_chunk_4'),
                        Forms\Components\Hidden::make('photo_chunk_5'),
                        Forms\Components\Hidden::make('photo_chunk_count'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->visible(fn() => auth()->user()->can('view_all_absent')),
                    
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Masuk')
                    ->time('H:i')
                    ->sortable()
                    ->badge()
                    ->color(fn(?string $state) => $state ? Color::Green : Color::Gray),
                    
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Pulang')
                    ->time('H:i')
                    ->sortable()
                    ->badge()
                    ->color(fn(?string $state) => $state ? Color::Blue : Color::Gray),
                    
                Tables\Columns\TextColumn::make('total_working_hours')
                    ->label('Total Jam')
                    ->badge()
                    ->color(Color::Indigo),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'hadir' => Color::Green,
                        'terlambat' => Color::Yellow,
                        'izin' => Color::Blue,
                        'tidak_hadir' => Color::Red,
                        default => Color::Gray,
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        default => $state,
                    }),
                    
                Tables\Columns\ImageColumn::make('check_in_photo')
                    ->label('Foto Masuk')
                    ->circular()
                    ->size(40)
                    ->disk('public')
                    ->visibility('public')
                    ->defaultImageUrl('https://via.placeholder.com/40x40/e5e7eb/9ca3af?text=No+Photo'),
                    
                Tables\Columns\ImageColumn::make('check_out_photo')
                    ->label('Foto Pulang')
                    ->circular()
                    ->size(40)
                    ->disk('public')
                    ->visibility('public')
                    ->defaultImageUrl('https://via.placeholder.com/40x40/e5e7eb/9ca3af?text=No+Photo'),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                    ]),
                    
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->format('d/m/Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
                    
                SelectFilter::make('employee_id')
                    ->label('Pegawai')
                    ->options(fn() => User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn() => auth()->user()->can('view_all_absent')),
            ])
            ->actions([
                Action::make('absen_pulang')
                    ->label('Absen Pulang')
                    ->icon('heroicon-o-camera')
                    ->color('success')
                    ->visible(fn($record) => empty($record->check_out))
                    ->url(fn($record) => '/checkout-photo/' . $record->id)
                    ->openUrlInNewTab(false)
                    ->before(function (array $data, $record) {
                        \Log::info('Before absen pulang action:', [
                            'record_id' => $record->id,
                            'employee_id' => $record->employee_id,
                            'data_keys' => array_keys($data),
                            'photo_data_exists' => isset($data['photo_data']),
                            'photo_data_length' => isset($data['photo_data']) ? strlen($data['photo_data']) : 0,
                            'photo_data_preview' => isset($data['photo_data']) ? substr($data['photo_data'], 0, 50) . '...' : null
                        ]);
                    })
                    ->action(function (array $data, $record) {
                        try {
                            // Debug log
                            \Log::info('Absen pulang data received:', [
                                'has_photo_data' => isset($data['photo_data']),
                                'photo_data_length' => isset($data['photo_data']) ? strlen($data['photo_data']) : 0,
                                'all_keys' => array_keys($data),
                                'data_sample' => array_map(function($v) { 
                                    return is_string($v) && strlen($v) > 100 ? substr($v, 0, 100) . '...' : $v; 
                                }, $data)
                            ]);
                            
                            // Check for photo data in form or session
                            $photoData = $data['photo_data'] ?? null;
                            
                            // If no photo in form, try session backup
                            if (empty($photoData)) {
                                $photoData = session()->get('temp_check_out_photo');
                                if ($photoData) {
                                    \Log::info('✅ Found check_out photo in session!', [
                                        'length' => strlen($photoData),
                                        'preview' => substr($photoData, 0, 100)
                                    ]);
                                    // Clear session after use
                                    session()->forget('temp_check_out_photo');
                                }
                            }
                            
                            // Validate photo data
                            if (empty($photoData)) {
                                throw new \Exception('Foto diperlukan untuk absen pulang. Silakan ambil foto terlebih dahulu.');
                            }
                            
                            // Save photo
                            $photoPath = self::saveBase64Image($photoData, 'check_out', $record->employee_id);
                            
                            if (!$photoPath) {
                                throw new \Exception('Gagal menyimpan foto. Silakan coba lagi.');
                            }
                            
                            // Update record
                            $record->update([
                                'check_out' => now()->format('H:i:s'),
                                'check_out_photo' => $photoPath,
                                'notes' => $data['notes'] ?? $record->notes,
                            ]);
                            
                            Notification::make()
                                ->title('Absen pulang berhasil!')
                                ->body('Waktu pulang: ' . now()->format('H:i'))
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Log::error('Absen pulang error:', ['error' => $e->getMessage(), 'data' => $data]);
                            
                            Notification::make()
                                ->title('Gagal absen pulang')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel('Konfirmasi Absen Pulang')
                    ->modalCancelActionLabel('Batal'),
                    
                ViewAction::make()
                    ->label('Lihat'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn() => auth()->user()->can('delete_absent')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ])
                ->visible(fn() => auth()->user()->can('delete_absent')),
            ])
            ->emptyStateHeading('Belum ada data absensi')
            ->emptyStateDescription('Silahkan tambahkan data absensi baru.')
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsents::route('/'),
            'create' => Pages\CreateAbsent::route('/absen-masuk'),
            'view' => Pages\ViewAbsent::route('/{record}'),
        ];
    }

    private static function saveBase64Image(string $base64Data, string $type, $employeeId): ?string
    {
        try {
            // Remove data:image/jpeg;base64, prefix if exists
            if (strpos($base64Data, 'data:image') !== false) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            }
            
            // Validate base64 data
            $imageData = base64_decode($base64Data, true);
            if ($imageData === false) {
                \Log::error('Invalid base64 data for ' . $type);
                return null;
            }
            
            // Generate unique filename
            $filename = 'absent-' . $type . '-' . $employeeId . '-' . time() . '.jpg';
            $path = 'absent-photos/' . $filename;
            
            // Save to storage
            StorageFacade::disk('public')->put($path, $imageData);
            
            return $path;
            
        } catch (\Exception $e) {
            \Log::error('Failed to save image: ' . $e->getMessage());
            return null;
        }
    }
}