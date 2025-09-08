<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use App\Models\Absent;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateAbsent extends CreateRecord
{
    protected static string $resource = AbsentResource::class;
    
    public function create(bool $another = false): void
    {
        // Check if user already has absent record for today
        $employeeId = auth()->user()->can('view_all_absent') 
            ? $this->data['employee_id'] ?? auth()->id()
            : auth()->id();
            
        $existingAbsent = \App\Models\Absent::where('employee_id', $employeeId)
            ->whereDate('date', today())
            ->first();
            
        if ($existingAbsent) {
            \Filament\Notifications\Notification::make()
                ->title('Absen Sudah Ada!')
                ->body('Anda sudah melakukan absen masuk hari ini pada ' . $existingAbsent->check_in . '. Tidak dapat melakukan absen masuk lagi.')
                ->warning()
                ->persistent()
                ->send();
                
            // Redirect to index with warning
            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }
        
        // Try to get photo from session before normal processing
        $sessionPhoto = session()->get('temp_check_in_photo');
        if ($sessionPhoto) {
            \Log::info('Found photo in session', ['length' => strlen($sessionPhoto)]);
        }
        
        parent::create($another);
    }
    
    // Removed storePhotoData method - using AJAX session storage instead
    
    protected static ?string $title = 'Absen Masuk';
    
    public function getHeading(): string
    {
        return 'Absen Masuk';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get raw Livewire request data for detailed debugging
        $livewireData = request()->get('components', []);
        $livewireSnapshot = $livewireData[0]['snapshot'] ?? [];
        $livewireUpdates = request()->get('components', [])[0]['updates'] ?? [];
        
        // Debug log to see when this is called
        \Log::info('CreateAbsent::mutateFormDataBeforeCreate called', [
            'timestamp' => now()->format('H:i:s.u'),
            'all_data_keys' => array_keys($data),
            'data_sample' => array_map(function($v) { 
                if (is_string($v) && strlen($v) > 100) {
                    return 'STRING[' . strlen($v) . ']' . substr($v, 0, 50) . '...';
                }
                return $v;
            }, $data),
            'has_check_in_photo' => isset($data['check_in_photo']),
            'check_in_photo_type' => isset($data['check_in_photo']) ? gettype($data['check_in_photo']) : null,
            'check_in_photo_length' => isset($data['check_in_photo']) && is_string($data['check_in_photo']) ? strlen($data['check_in_photo']) : null,
            'livewire_snapshot_data_keys' => isset($livewireSnapshot['data']) ? array_keys($livewireSnapshot['data']) : [],
            'livewire_updates_count' => count($livewireUpdates),
            'request_method' => request()->method(),
            'content_type' => request()->header('Content-Type'),
            'content_length' => request()->header('Content-Length'),
            'raw_request_size' => strlen(request()->getContent()),
        ]);
        
        // Set employee_id untuk user yang tidak memiliki akses view_all_absent
        if (!auth()->user()->can('view_all_absent')) {
            $data['employee_id'] = auth()->id();
        }
        
        // Set tanggal otomatis ke hari ini
        $data['date'] = today();
        
        // Handle absensi masuk (form create hanya untuk absen masuk)
        $data['check_in'] = now()->format('H:i:s');
        
        // Handle camera capture photos untuk check_in - SUPER AGGRESSIVE APPROACH
        $photoData = null;
        
        // Try ALL possible field sources including raw HTTP data
        $possibleFields = [
            'check_in_photo',
            'check_in_photo_backup', 
            'check_in_photo_emergency',
            'check_in_photo_emergency_1',
            'check_in_photo_emergency_2', 
            'check_in_photo_emergency_3',
            'check_in_photo_hidden_1',
            'check_in_photo_hidden_2',
            'check_in_photo_hidden_3',
            'check_in_photo_debug',
            'photo_chunk_1',
            'photo_chunk_2',
            'photo_chunk_3',
            'photo_chunk_4',
            'photo_chunk_5'
        ];
        
        // FIRST: Try all submitted form data fields
        foreach ($possibleFields as $fieldName) {
            if (isset($data[$fieldName]) && !empty($data[$fieldName])) {
                $photoData = is_array($data[$fieldName]) ? 
                    (isset($data[$fieldName][0]) ? $data[$fieldName][0] : null) : 
                    $data[$fieldName];
                    
                if (!empty($photoData) && is_string($photoData) && strlen($photoData) > 1000) {
                    \Log::info("✅ Found photo in field: {$fieldName}", [
                        'length' => strlen($photoData),
                        'preview' => substr($photoData, 0, 100)
                    ]);
                    break;
                }
            }
        }
        
        // SECOND: If no photo found, try direct request data
        if (empty($photoData)) {
            \Log::warning('No photo found in form data, checking request directly...');
            $request = request();
            
            foreach ($possibleFields as $fieldName) {
                $requestValue = $request->input($fieldName);
                if (!empty($requestValue) && is_string($requestValue) && strlen($requestValue) > 1000) {
                    \Log::info("✅ Found photo in request: {$fieldName}", [
                        'length' => strlen($requestValue),
                        'preview' => substr($requestValue, 0, 100)
                    ]);
                    $photoData = $requestValue;
                    break;
                }
            }
        }
        
        // THIRD: If still no photo, try $_POST directly
        if (empty($photoData)) {
            \Log::warning('No photo found in request, checking $_POST directly...');
            
            foreach ($possibleFields as $fieldName) {
                if (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
                    $postValue = $_POST[$fieldName];
                    if (is_string($postValue) && strlen($postValue) > 1000) {
                        \Log::info("✅ Found photo in \$_POST: {$fieldName}", [
                            'length' => strlen($postValue),
                            'preview' => substr($postValue, 0, 100)
                        ]);
                        $photoData = $postValue;
                        break;
                    }
                }
            }
        }
        
        // FOURTH: Try to reassemble chunked data
        if (empty($photoData)) {
            $chunkCount = isset($data['photo_chunk_count']) ? (int) $data['photo_chunk_count'] : 0;
            if ($chunkCount > 0) {
                \Log::info('Trying to reassemble chunked photo data', ['chunk_count' => $chunkCount]);
                
                $chunkedData = '';
                for ($i = 1; $i <= $chunkCount; $i++) {
                    $chunkField = "photo_chunk_$i";
                    if (isset($data[$chunkField]) && !empty($data[$chunkField])) {
                        $chunkedData .= $data[$chunkField];
                        \Log::info("Found chunk $i", ['length' => strlen($data[$chunkField])]);
                    }
                }
                
                if (!empty($chunkedData)) {
                    \Log::info('✅ Successfully reassembled chunked photo!', [
                        'total_length' => strlen($chunkedData),
                        'preview' => substr($chunkedData, 0, 100)
                    ]);
                    $photoData = $chunkedData;
                }
            }
        }
        
        // FIFTH: If still no photo, try session as last resort
        if (empty($photoData)) {
            // Try check_in photo first (for create absent page)
            $sessionPhoto = session()->get('temp_check_in_photo');
            
            // If not found, try check_out photo (fallback)
            if (!$sessionPhoto) {
                $sessionPhoto = session()->get('temp_check_out_photo');
            }
            
            if (!empty($sessionPhoto)) {
                \Log::info('✅ Found photo in session!', [
                    'length' => strlen($sessionPhoto),
                    'preview' => substr($sessionPhoto, 0, 100)
                ]);
                $photoData = $sessionPhoto;
                // Clear session photos after use
                session()->forget('temp_check_in_photo');
                session()->forget('temp_check_out_photo');
            }
        }
        
        // Process photo if found
        if (!empty($photoData)) {
            \Log::info('Processing check_in_photo', [
                'photo_data_type' => gettype($photoData),
                'photo_data_length' => is_string($photoData) ? strlen($photoData) : null,
                'photo_data_preview' => is_string($photoData) ? substr($photoData, 0, 100) : null,
                'is_base64' => is_string($photoData) && str_starts_with($photoData, 'data:image')
            ]);
            
            if (is_string($photoData)) {
                $savedPath = $this->saveBase64Image($photoData, 'check_in');
                if ($savedPath) {
                    $data['check_in_photo'] = $savedPath;
                    \Log::info('✅ Photo saved successfully', [
                        'path' => $savedPath,
                        'file_exists' => Storage::disk('public')->exists($savedPath),
                        'file_size' => Storage::disk('public')->size($savedPath)
                    ]);
                } else {
                    \Log::error('❌ Failed to save photo, removing from data');
                    unset($data['check_in_photo']);
                }
            } else {
                \Log::warning('❌ Invalid photo data type', [
                    'photoData_type' => gettype($photoData)
                ]);
                unset($data['check_in_photo']);
            }
        }
        
        if (empty($photoData)) {
            \Log::info('No photo data found, proceeding without photo', [
                'all_form_fields' => array_keys($data),
                'session_photo_exists' => session()->has('temp_check_in_photo')
            ]);
            
            // Allow creation without photo - photo is optional
            unset($data['check_in_photo']);
        }
        
        // Remove check_out data dan attendance_type (tidak disimpan ke database)
        unset($data['check_out_photo']);
        unset($data['check_out']);
        unset($data['attendance_type']);
        
        // Remove ALL backup and emergency fields
        $cleanupFields = [
            'check_in_photo_backup',
            'check_in_photo_emergency',
            'check_in_photo_emergency_1',
            'check_in_photo_emergency_2',
            'check_in_photo_emergency_3',
            'check_in_photo_hidden_1',
            'check_in_photo_hidden_2', 
            'check_in_photo_hidden_3',
            'check_in_photo_debug',
            'photo_chunk_1',
            'photo_chunk_2',
            'photo_chunk_3', 
            'photo_chunk_4',
            'photo_chunk_5',
            'photo_chunk_count'
        ];
        
        foreach ($cleanupFields as $field) {
            unset($data[$field]);
        }
        
        \Log::info('Final data for create', [
            'final_keys' => array_keys($data),
            'has_final_photo' => isset($data['check_in_photo']),
            'final_photo_path' => $data['check_in_photo'] ?? null
        ]);
        
        return $data;
    }
    
    private function saveBase64Image($base64Data, string $type): ?string
    {
        \Log::info('saveBase64Image called', [
            'type' => $type,
            'data_length' => strlen($base64Data),
            'data_preview' => substr($base64Data, 0, 100),
            'has_data_prefix' => str_starts_with($base64Data, 'data:image')
        ]);
        
        // Validate input data
        if (empty($base64Data) || !is_string($base64Data)) {
            \Log::error('Invalid input data for saveBase64Image', [
                'empty' => empty($base64Data),
                'type' => gettype($base64Data)
            ]);
            return null;
        }
        
        // Remove data:image/jpeg;base64, prefix if exists
        if (strpos($base64Data, 'data:image') !== false) {
            $originalLength = strlen($base64Data);
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            \Log::info('Removed data prefix', [
                'original_length' => $originalLength,
                'new_length' => strlen($base64Data)
            ]);
        }
        
        // Validate base64 data
        $imageData = base64_decode($base64Data, true);
        if ($imageData === false) {
            \Log::error('Invalid base64 data for ' . $type, [
                'base64_sample' => substr($base64Data, 0, 100)
            ]);
            return null;
        }
        
        \Log::info('Base64 decoded successfully', [
            'image_data_size' => strlen($imageData)
        ]);
        
        // Generate unique filename
        $filename = 'absent-' . $type . '-' . auth()->id() . '-' . time() . '.jpg';
        $path = 'absent-photos/' . $filename;
        
        \Log::info('Attempting to save image', [
            'filename' => $filename,
            'path' => $path,
            'storage_disk' => 'public'
        ]);
        
        try {
            // Ensure directory exists
            $directory = dirname($path);
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
                \Log::info('Created directory', ['directory' => $directory]);
            }
            
            // Save to storage
            $result = Storage::disk('public')->put($path, $imageData);
            
            if ($result) {
                \Log::info('Image saved successfully', [
                    'path' => $path,
                    'file_exists' => Storage::disk('public')->exists($path),
                    'file_size' => Storage::disk('public')->size($path)
                ]);
                return $path;
            } else {
                \Log::error('Storage put returned false', ['path' => $path]);
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to save image', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'path' => $path
            ]);
            return null;
        }
    }
}