<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes MVP Frontend — Smart Emergency AI (Citoyen)
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/connexion', [PageController::class, 'login'])->name('login');
Route::get('/inscription', [PageController::class, 'register'])->name('register');
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
Route::get('/signaler', [PageController::class, 'report'])->name('report');
Route::get('/historique', [PageController::class, 'history'])->name('history');
Route::get('/signalement/{id}', [PageController::class, 'show'])->name('signalement.show');
