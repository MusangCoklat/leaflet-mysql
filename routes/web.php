<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarkerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route untuk halaman profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ Route untuk halaman peta Leaflet
    Route::get('/map', function () {
        return view('map');
    })->name('map');

    // ✅ Endpoint AJAX untuk marker
    Route::get('/markers', [MarkerController::class, 'index']);
    Route::post('/markers', [MarkerController::class, 'store']);

    Route::delete('/markers/{id}', [MarkerController::class, 'destroy']);

});

require __DIR__.'/auth.php';
