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
