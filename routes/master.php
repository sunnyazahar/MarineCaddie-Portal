<?php

use App\Http\Controllers\Master\MasterAuthController;
use App\Http\Controllers\Master\MasterSetupController;
use App\Support\Installation;
use Illuminate\Support\Facades\Route;

// Registered only in a master setup copy (APP_MODE=master); a normal portal never exposes these.
if (! Installation::isMaster()) {
    return;
}

Route::prefix('master')->name('master.')->group(function () {
    Route::get('/login', [MasterAuthController::class, 'show'])->name('login');
    Route::post('/login', [MasterAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');

    Route::middleware('master.auth')->group(function () {
        Route::get('/', [MasterSetupController::class, 'show'])->name('show');
        Route::post('/test-database', [MasterSetupController::class, 'testDatabase'])
            ->middleware('throttle:10,1')
            ->name('test-database');
        Route::post('/provision', [MasterSetupController::class, 'provision'])
            ->middleware('throttle:5,1')
            ->name('provision');
        Route::post('/logout', [MasterAuthController::class, 'logout'])->name('logout');
    });
});
