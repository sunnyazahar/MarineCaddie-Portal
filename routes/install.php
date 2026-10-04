<?php

use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

Route::middleware('install.pending')->prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallerController::class, 'show'])->name('show');
    Route::post('/test-database', [InstallerController::class, 'testDatabase'])
        ->middleware('throttle:10,1')
        ->name('test-database');
    Route::post('/', [InstallerController::class, 'install'])
        ->middleware('throttle:5,1')
        ->name('run');
    Route::post('/handoff', [InstallerController::class, 'handoff'])
        ->middleware('throttle:5,1')
        ->name('handoff');
});
