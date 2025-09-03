<?php

namespace App\Http\Controllers;

use App\Models\Absent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class CheckoutPhotoController extends Controller
{
    public function show($id)
    {
        // Find the absent record
        $absent = Absent::findOrFail($id);
        
        // Check authorization
        $user = auth()->user();
        if (!$user->can('edit_absent') && $absent->employee_id !== $user->id) {
            abort(403, 'Tidak memiliki akses untuk absen pulang record ini');
        }
        
        // Check if already checked out
        if (!empty($absent->check_out)) {
            return redirect()->back()->with('error', 'Record ini sudah memiliki absen pulang');
        }
        
        return view('checkout-photo', compact('absent'));
    }
    
    public function store(Request $request, $id)
    {
        $absent = Absent::findOrFail($id);
        
        // Check authorization
        $user = auth()->user();
        if (!$user->can('edit_absent') && $absent->employee_id !== $user->id) {
            abort(403, 'Tidak memiliki akses untuk absen pulang record ini');
        }
        
        // Check if already checked out
        if (!empty($absent->check_out)) {
            return redirect()->back()->with('error', 'Record ini sudah memiliki absen pulang');
        }
        
        try {
            // Get photo data from various sources
            $photoData = null;
            
            // 1. Try from form field
            $photoData = $request->input('photo_data');
            
            // 2. Try from session if not found in form
            if (empty($photoData)) {
                $photoData = session()->get('temp_check_out_photo');
                if ($photoData) {
                    \Log::info('✅ Found check_out photo in session!', [
                        'length' => strlen($photoData),
                        'preview' => substr($photoData, 0, 100)
                    ]);
                    session()->forget('temp_check_out_photo');
                }
            }
            
            // Validate photo data
            if (empty($photoData)) {
                throw new \Exception('Foto diperlukan untuk absen pulang. Silakan ambil foto terlebih dahulu.');
            }
            
            \Log::info('Processing check_out photo', [
                'photo_data_type' => gettype($photoData),
                'photo_data_length' => is_string($photoData) ? strlen($photoData) : null,
                'is_base64' => is_string($photoData) && str_starts_with($photoData, 'data:image')
            ]);
            
            // Save photo
            $photoPath = $this->saveBase64Image($photoData, 'check_out', $absent->employee_id);
            
            if (!$photoPath) {
                throw new \Exception('Gagal menyimpan foto. Silakan coba lagi.');
            }
            
            // Update record
            $absent->update([
                'check_out' => now()->format('H:i:s'),
                'check_out_photo' => $photoPath,
                'notes' => $request->input('notes') ?? $absent->notes,
            ]);
            
            \Log::info('✅ Absen pulang berhasil!', [
                'absent_id' => $absent->id,
                'employee_id' => $absent->employee_id,
                'check_out_time' => $absent->check_out,
                'photo_path' => $photoPath
            ]);
            
            return redirect('/admin/pegawai/absents')->with('success', 'Absen pulang berhasil! Waktu: ' . now()->format('H:i'));
            
        } catch (\Exception $e) {
            \Log::error('Absen pulang error:', [
                'error' => $e->getMessage(),
                'absent_id' => $id,
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    private function saveBase64Image($base64Data, string $type, $employeeId): ?string
    {
        \Log::info('saveBase64Image called', [
            'type' => $type,
            'data_length' => strlen($base64Data),
            'data_preview' => substr($base64Data, 0, 100),
            'has_data_prefix' => str_starts_with($base64Data, 'data:image')
        ]);
        
        try {
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
            $filename = 'absent-' . $type . '-' . $employeeId . '-' . time() . '.jpg';
            $path = 'absent-photos/' . $filename;
            
            \Log::info('Attempting to save image', [
                'filename' => $filename,
                'path' => $path,
                'storage_disk' => 'public'
            ]);
            
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
                'path' => $path ?? 'unknown'
            ]);
            return null;
        }
    }
}