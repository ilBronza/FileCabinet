<?php

use IlBronza\FileCabinet\Http\Controllers\Offline\OfflineController;
use Illuminate\Support\Facades\Route;

if (config('filecabinet.offline.enabled', true))
{
    Route::prefix('filecabinet-manager/offline')->name(config('filecabinet.routePrefix') . 'offline.')->group(function () {
        // Only the generic shell/assets are public and cached. No user data or CSRF token in the shell.
        Route::get('/app', [OfflineController::class, 'shell'])->name('shell');
        Route::get('/sw.js', [OfflineController::class, 'worker'])->name('worker');
        Route::middleware(array_merge(['web', 'auth'], config('filecabinet.offline.middleware', [])))->group(function () {
            Route::get('/open/{filecabinet}', [OfflineController::class, 'entry'])->name('entry');
            Route::get('/api/session', [OfflineController::class, 'session'])->name('session');
            Route::get('/api/snapshot/{filecabinet}', [OfflineController::class, 'snapshot'])->name('snapshot');
            Route::post('/api/sync/{filecabinet}/{dossier}', [OfflineController::class, 'sync'])->name('sync');
        });
    });
}
