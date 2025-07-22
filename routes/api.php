<?php

use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/register', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    
    // User management routes
    Route::apiResource('users', UserController::class)->except(['store']);
    
    // Current user route
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    });
});