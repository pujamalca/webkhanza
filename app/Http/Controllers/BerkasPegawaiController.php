<?php

namespace App\Http\Controllers;

use App\Models\BerkasPegawai;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BerkasPegawaiController extends Controller
{
    /**
     * Display the specified file with authorization check
     */
    public function show(Request $request, $filename): Response|StreamedResponse
    {
        // Authentication is handled by middleware, so we're good here

        // Verify that the file exists in berkas_pegawai records
        $berkas = BerkasPegawai::where('berkas', 'berkas_pegawai/' . $filename)->first();
        
        if (!$berkas) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        $filePath = 'berkas_pegawai/' . $filename;
        
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found in storage');
        }

        // Get file content and mime type
        $file = Storage::disk('local')->get($filePath);
        $mimeType = Storage::disk('local')->mimeType($filePath);
        $size = Storage::disk('local')->size($filePath);

        // Return file response with appropriate headers
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', $size)
            ->header('Content-Disposition', 'inline; filename="' . basename($filename) . '"')
            ->header('Cache-Control', 'private, max-age=3600');
    }

    /**
     * Download the specified file
     */
    public function download(Request $request, $filename): Response|StreamedResponse
    {
        // Authentication is handled by middleware, so we're good here

        // Verify that the file exists in berkas_pegawai records
        $berkas = BerkasPegawai::where('berkas', 'berkas_pegawai/' . $filename)->first();
        
        if (!$berkas) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        $filePath = 'berkas_pegawai/' . $filename;
        
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found in storage');
        }

        // Get original filename from berkas record or use the filename
        $originalName = basename($berkas->berkas);

        // Return download response
        return Storage::disk('local')->download($filePath, $originalName);
    }
}