<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\BerandaController;
use Illuminate\Support\Facades\Route;

// Landing Page (Public - Dynamic)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Auth (Single Page Login + Daftar)
Route::get('/masuk', [WebAuthController::class, 'showAuth'])->name('masuk');
Route::post('/masuk', [WebAuthController::class, 'login'])->name('login.submit');
Route::post('/daftar', [WebAuthController::class, 'register'])->name('daftar.submit');
Route::post('/keluar', [WebAuthController::class, 'logout'])->name('keluar');

// Beranda Jamaah (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
});

// Download APK
Route::get('/download-app', function () {
    $path = public_path('downloads/MasjidBGC.apk');
    if (!file_exists($path)) {
        abort(404, 'File aplikasi belum tersedia.');
    }
    return response()->download($path, 'MasjidBGC.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('app.download');
