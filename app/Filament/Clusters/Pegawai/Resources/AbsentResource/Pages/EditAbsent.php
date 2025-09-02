<?php

namespace App\Filament\Clusters\Pegawai\Resources\AbsentResource\Pages;

use App\Filament\Clusters\Pegawai\Resources\AbsentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditAbsent extends EditRecord
{
    protected static string $resource = AbsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat'),
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle camera capture photos untuk check_out (base64 to file)
        if (isset($data['check_out_photo']) && !empty($data['check_out_photo'])) {
            // Handle array dari Filament atau string dari camera capture
            $photoData = is_array($data['check_out_photo']) ? 
                (isset($data['check_out_photo'][0]) ? $data['check_out_photo'][0] : null) : 
                $data['check_out_photo'];
            
            if (!empty($photoData) && is_string($photoData)) {
                $savedPath = $this->saveBase64Image($photoData, 'check_out');
                if ($savedPath) {
                    $data['check_out_photo'] = $savedPath;
                    // Set waktu pulang otomatis ke sekarang jika berhasil simpan foto pulang
                    $data['check_out'] = now()->format('H:i:s');
                } else {
                    unset($data['check_out_photo']);
                }
            } else {
                unset($data['check_out_photo']);
            }
        }
        
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
            \Storage::disk('public')->put($path, $imageData);
            return $path;
        } catch (\Exception $e) {
            \Log::error('Failed to save image: ' . $e->getMessage());
            return null;
        }
    }
}