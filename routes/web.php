<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerkasPegawaiController;

Route::get('/', function () {
    return view('welcome');
});

// Protected routes for berkas pegawai files - using same middleware as Filament admin
Route::middleware([
    'web',
    \Filament\Http\Middleware\Authenticate::class . ':admin',
])->group(function () {
    Route::get('/berkas-pegawai/{filename}', [BerkasPegawaiController::class, 'show'])
        ->name('berkas-pegawai.show')
        ->where('filename', '.*');
    
    Route::get('/berkas-pegawai/{filename}/download', [BerkasPegawaiController::class, 'download'])
        ->name('berkas-pegawai.download')
        ->where('filename', '.*');
});

// Add CORS middleware for storage files
Route::middleware([
    \App\Http\Middleware\CorsMiddleware::class,
])->group(function () {
    Route::get('/storage/{path}', function ($path) {
        $filePath = storage_path('app/public/' . $path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }
        
        return response()->file($filePath);
    })->where('path', '.*');
});
