<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateAbsent extends CreateRecord
{
    protected static string $resource = AbsentResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set employee_id untuk user yang tidak memiliki akses view_all_absent
        if (!auth()->user()->can('view_all_absent')) {
            $data['employee_id'] = auth()->id();
        }
        
        // Set tanggal otomatis ke hari ini
        $data['date'] = today();
        
        // Set status default ke hadir
        $data['status'] = 'hadir';
        
        $attendanceType = $data['attendance_type'] ?? 'masuk';
        
        if ($attendanceType === 'masuk') {
            // Handle absensi masuk
            $data['check_in'] = now()->format('H:i:s');
            
            // Handle camera capture photos untuk check_in
            if (isset($data['check_in_photo']) && !empty($data['check_in_photo'])) {
                $photoData = is_array($data['check_in_photo']) ? 
                    (isset($data['check_in_photo'][0]) ? $data['check_in_photo'][0] : null) : 
                    $data['check_in_photo'];
                
                if (!empty($photoData) && is_string($photoData)) {
                    $savedPath = $this->saveBase64Image($photoData, 'check_in');
                    if ($savedPath) {
                        $data['check_in_photo'] = $savedPath;
                    } else {
                        unset($data['check_in_photo']);
                    }
                } else {
                    unset($data['check_in_photo']);
                }
            } else {
                unset($data['check_in_photo']);
            }
            
            // Remove check_out data untuk absen masuk
            unset($data['check_out_photo']);
            unset($data['check_out']);
            
        } else {
            // Handle absensi pulang
            $data['check_out'] = now()->format('H:i:s');
            
            // Handle camera capture photos untuk check_out
            if (isset($data['check_out_photo']) && !empty($data['check_out_photo'])) {
                $photoData = is_array($data['check_out_photo']) ? 
                    (isset($data['check_out_photo'][0]) ? $data['check_out_photo'][0] : null) : 
                    $data['check_out_photo'];
                
                if (!empty($photoData) && is_string($photoData)) {
                    $savedPath = $this->saveBase64Image($photoData, 'check_out');
                    if ($savedPath) {
                        $data['check_out_photo'] = $savedPath;
                    } else {
                        unset($data['check_out_photo']);
                    }
                } else {
                    unset($data['check_out_photo']);
                }
            } else {
                unset($data['check_out_photo']);
            }
            
            // Remove check_in data untuk absen pulang
            unset($data['check_in_photo']);
            unset($data['check_in']);
        }
        
        // Remove attendance_type dari data (tidak disimpan ke database)
        unset($data['attendance_type']);
        
        return $data;
    }
    
    private function saveBase64Image($base64Data, string $type): ?string
    {
        // Validate input data
        if (empty($base64Data) || !is_string($base64Data)) {
            return null;
        }
        
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
        $filename = 'absent-' . $type . '-' . auth()->id() . '-' . time() . '.jpg';
        $path = 'absent-photos/' . $filename;
        
        try {
            // Save to storage
            Storage::disk('public')->put($path, $imageData);
            return $path;
        } catch (\Exception $e) {
            \Log::error('Failed to save image: ' . $e->getMessage());
            return null;
        }
    }
}