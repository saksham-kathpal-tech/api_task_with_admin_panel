<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// Public authentication routes
// Admin-only login route (used by admin panel)
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require JWT token)
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin only routes (prefixed with /admin)
    Route::middleware('admin')->prefix('admin')->group(function () {
        // User management
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{user}', [UserController::class, 'show']);
        Route::put('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);

        // User status management
        Route::post('users/{user}/block', [UserController::class, 'block']);
        Route::post('users/{user}/unblock', [UserController::class, 'unblock']);
    });
});

