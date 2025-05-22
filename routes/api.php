<?php

use App\Http\Controllers\Api\ProgressApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserApiController::class, 'index']);
    Route::get('/progress', [ProgressApiController::class, 'index']);
    Route::get('/api/progress-by-status', [DashboardController::class, 'getProgressByStatus']);
});
