<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard-example', function () {
    return view('dashboard-example');
})->middleware(['auth', 'verified'])->name('dashboard-example');

Route::view('/home', 'pages.home')->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('dashboard', DashboardController::class)->only(['index', 'show']);
    Route::resource('areas', AreaController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::resource('devices', DeviceController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['index', 'store', 'edit', 'update',  'destroy']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
