<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TrendingController as ApiTrendingController;
use App\Http\Controllers\Api\DeviceController as ApiDeviceController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceReadingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/devices/latest', [ApiDeviceController::class, 'latestReadings']);
    
    // Member Profile Sub-system
    Route::prefix('member')->group(function () {
        Route::put('/profile', [ProfileController::class, 'updateProfileJson']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });

    // Logs Sub-system
    Route::prefix('logs')->group(function () {
        Route::get('/', [LogController::class, 'index']);
        Route::get('/devices', [LogController::class, 'devices']);
        Route::get('/export', [LogController::class, 'export']);
    });

    // Trending Sub-system
    Route::get('/trending', [ApiTrendingController::class, 'index']);
});

Route::middleware('auth.api-key')->group(function () {
    Route::get('/device-exists', [DeviceController::class, 'checkDeviceExists']);
    Route::post('/device-readings', [DeviceReadingController::class, 'store']);
});

Route::get('/terminals/{terminal}/areas', function ($terminalId) {
    return \App\Models\Area::where('terminal_id', $terminalId)
        ->select('id', 'name')
        ->get();
});
