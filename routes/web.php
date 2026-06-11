<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SignalementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes MVP — Smart Emergency AI (Citoyen)
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

Route::post('/deconnexion', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/signaler', [PageController::class, 'report'])->name('report');
    Route::get('/geolocalisation/adresse', [SignalementController::class, 'resolveAddress'])->name('geolocate.address');
    Route::post('/signaler', [SignalementController::class, 'store'])->name('signalement.store');
    Route::get('/historique', [PageController::class, 'history'])->name('history');
    Route::get('/signalement/{id}', [PageController::class, 'show'])->name('signalement.show');
});
