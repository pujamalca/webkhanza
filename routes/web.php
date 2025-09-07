<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerkasPegawaiController;
use App\Http\Controllers\LandingPageController;

Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');

// Blog routes
Route::get('/blog', [LandingPageController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [LandingPageController::class, 'blogDetail'])->name('blog.detail');

// Protected routes for berkas pegawai files - using same middleware as Filament admin
Route::middleware([
    'web',
    \Filament\Http\Middleware\Authenticate::class . ':admin',
])->group(function () {
    Route::get('/berkas-pegawai/{filename}', [BerkasPegawaiController::class, 'show'])
        ->name('berkas-pegawai.show')
        ->where('filename', '.*');
    
    Route::get('/berkas-pegawai/{filename}/download', [BerkasPegawaiController::class, 'download'])
        ->name('berkas-pegawai.download');
        
    // Route for storing photo data via AJAX before form submission
    Route::post('/store-photo-temp', function() {
        $photo = request()->input('photo_data');
        $type = request()->input('type', 'check_in'); // Default to check_in
        
        if ($photo) {
            $sessionKey = $type === 'check_out' ? 'temp_check_out_photo' : 'temp_check_in_photo';
            session()->put($sessionKey, $photo);
            return response()->json(['success' => true, 'length' => strlen($photo), 'type' => $type]);
        }
        return response()->json(['success' => false]);
    })->name('store-photo-temp');

    // Route for checkout photo page
    Route::get('/checkout-photo/{id}', [\App\Http\Controllers\CheckoutPhotoController::class, 'show'])
        ->name('checkout-photo.show');
    
    Route::post('/checkout-photo/{id}', [\App\Http\Controllers\CheckoutPhotoController::class, 'store'])
        ->name('checkout-photo.store');
    
    // Marketing task API
    Route::get('/admin/api/marketing-task/test', [\App\Http\Controllers\Api\MarketingTaskController::class, 'test'])
        ->name('api.marketing-task.test');
    Route::post('/admin/api/marketing-task/toggle', [\App\Http\Controllers\Api\MarketingTaskController::class, 'toggle'])
        ->name('api.marketing-task.toggle');
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
