<?php

use App\Http\Controllers\Admin\AdminAiReviewController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEmergencyServiceController;
use App\Http\Controllers\Admin\AdminMapController;
use App\Http\Controllers\Admin\AdminOperationsController;
use App\Http\Controllers\Admin\AdminPlatformStatController;
use App\Http\Controllers\Admin\AdminSignalementController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AttestationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignalementAnalysisController;
use App\Http\Controllers\SignalementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/impact', [PageController::class, 'impact'])->name('impact');
Route::get('/confidentialite', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/conditions', [LegalController::class, 'terms'])->name('legal.terms');

/*
|--------------------------------------------------------------------------
| Authentification (invités)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
    Route::get('/mot-de-passe-oublie', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('/deconnexion', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Espace citoyen
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/lire', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/lire-tout', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('/services-secours', [PageController::class, 'services'])->name('services.index');
    Route::get('/signaler', [PageController::class, 'report'])->name('report');
    Route::get('/signaler/rapide', [PageController::class, 'quickReport'])->name('report.quick');
    Route::get('/geolocalisation/adresse', [SignalementController::class, 'resolveAddress'])->name('geolocate.address');
    Route::post('/signaler/analyse', [SignalementAnalysisController::class, 'preview'])->name('signalement.analyze');
    Route::post('/signaler', [SignalementController::class, 'store'])->middleware('throttle:10,1')->name('signalement.store');

    Route::get('/historique', [PageController::class, 'history'])->name('history');
    Route::get('/signalement/{id}', [PageController::class, 'show'])->name('signalement.show');
    Route::get('/signalement/{id}/attestation', [AttestationController::class, 'show'])->name('signalement.attestation');
    Route::get('/signalement/{id}/attestation/telecharger', [AttestationController::class, 'download'])->name('signalement.attestation.download');
    Route::post('/signalement/{id}/annuler', [SignalementController::class, 'cancel'])->name('signalement.cancel');
});

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/operations', [AdminOperationsController::class, 'index'])->name('operations');
    Route::post('/signalements/{reference}/dispatch', [AdminOperationsController::class, 'assign'])->name('signalements.dispatch');
    Route::post('/signalements/{reference}/auto-dispatch', [AdminOperationsController::class, 'suggest'])->name('signalements.auto-dispatch');

    Route::get('/verification-ia', [AdminAiReviewController::class, 'index'])->name('ai-review.index');
    Route::post('/verification-ia/{reference}/approuver', [AdminAiReviewController::class, 'approve'])->name('ai-review.approve');
    Route::post('/verification-ia/{reference}/rejeter', [AdminAiReviewController::class, 'reject'])->name('ai-review.reject');

    Route::get('/carte', [AdminMapController::class, 'index'])->name('map');

    Route::get('/signalements/export', [AdminSignalementController::class, 'export'])->name('signalements.export');
    Route::get('/signalements', [AdminSignalementController::class, 'index'])->name('signalements.index');
    Route::get('/signalements/{reference}', [AdminSignalementController::class, 'show'])->name('signalements.show');
    Route::put('/signalements/{reference}', [AdminSignalementController::class, 'update'])->name('signalements.update');
    Route::delete('/signalements/{reference}', [AdminSignalementController::class, 'destroy'])->name('signalements.destroy');
    Route::patch('/signalements/{reference}/timeline/{step}', [AdminSignalementController::class, 'updateTimeline'])->name('signalements.timeline');

    Route::get('/services-secours', [AdminEmergencyServiceController::class, 'index'])->name('emergency-services.index');
    Route::post('/services-secours', [AdminEmergencyServiceController::class, 'store'])->name('emergency-services.store');
    Route::put('/services-secours/{service}', [AdminEmergencyServiceController::class, 'update'])->name('emergency-services.update');
    Route::delete('/services-secours/{service}', [AdminEmergencyServiceController::class, 'destroy'])->name('emergency-services.destroy');

    Route::get('/utilisateurs', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/utilisateurs', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/utilisateurs/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/utilisateurs/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/utilisateurs/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/statistiques', [AdminPlatformStatController::class, 'index'])->name('platform-stats.index');
    Route::put('/statistiques', [AdminPlatformStatController::class, 'update'])->name('platform-stats.update');
    Route::post('/statistiques/sync', [AdminPlatformStatController::class, 'sync'])->name('platform-stats.sync');

    Route::get('/journal', [AdminAuditLogController::class, 'index'])->name('audit.index');
});
