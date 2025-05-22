<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\UserConfigController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route API untuk ambil detail progress berdasarkan status
    Route::get('/api/progress-by-status', [DashboardController::class, 'getProgressByStatus']);
    Route::get('/api/progress-all', [DashboardController::class, 'getAllProgress']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Progress
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');

    // Officer bisa create
    Route::get('/progress/create', [ProgressController::class, 'create'])
        ->middleware('can:isOfficer')
        ->name('progress.create');
    Route::post('/progress', [ProgressController::class, 'store'])
        ->middleware('can:isOfficer')
        ->name('progress.store');
    Route::get('/progress/{id}', [ProgressController::class, 'show'])
        ->middleware('can:isOfficer')
        ->name('progress.show');

    // PM & VP bisa approve/reject
    Route::post('/progress/{progress}/approve', [ProgressController::class, 'approve'])
        ->middleware('can:canApprove')
        ->name('progress.approve');
    Route::post('/progress/{progress}/reject', [ProgressController::class, 'reject'])
        ->middleware('can:canApprove')
        ->name('progress.reject');

    // User Config hanya admin
    Route::middleware('can:isAdmin')->group(function () {
        Route::get('/userconfig', [UserConfigController::class, 'index'])->name('userconfig.index');
        Route::get('/userconfig/create', [UserConfigController::class, 'create'])->name('userconfig.create');
        Route::post('/userconfig', [UserConfigController::class, 'store'])->name('userconfig.store');
    });
});

require __DIR__ . '/auth.php';
