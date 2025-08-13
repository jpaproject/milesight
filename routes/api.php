<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceReadingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api-key')->group(function () {
    Route::get('/device-exists', [DeviceController::class, 'checkDeviceExists']);
    Route::post('/device-readings', [DeviceReadingController::class, 'store']);
});

Route::get('/terminals/{terminal}/areas', function ($terminalId) {
    return \App\Models\Area::where('terminal_id', $terminalId)
        ->select('id', 'name')
        ->get();
});
