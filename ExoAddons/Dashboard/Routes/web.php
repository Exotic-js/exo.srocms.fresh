<?php

use ExoAddons\Dashboard\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    $prefix = config('exodash.prefix', 'exo-admin');

    // Login routes (no guest middleware — controller handles the redirect logic)
    Route::prefix($prefix)->name('exodash.')->group(function () {
        Route::get('/login',  [DashboardController::class, 'login'])->name('login');
        Route::post('/login', [DashboardController::class, 'authenticate'])->name('authenticate');
    });

    // Admin-protected routes
    Route::middleware(['auth', 'admin'])
        ->prefix($prefix)
        ->name('exodash.')
        ->group(function () {
            // Addon list
            Route::get('/addons', [DashboardController::class, 'addons'])->name('addons');

            // Per-addon management
            Route::get('/addons/{slug}',          [DashboardController::class, 'manage'])->name('manage');
            Route::post('/addons/{slug}/save',     [DashboardController::class, 'saveConfig'])->name('save');

            // Lifecycle
            Route::post('/addons/{slug}/setup',     [DashboardController::class, 'setup'])->name('setup');
            Route::post('/addons/{slug}/toggle',    [DashboardController::class, 'toggle'])->name('toggle');
            Route::post('/addons/{slug}/uninstall', [DashboardController::class, 'uninstall'])->name('uninstall');
            Route::post('/addons/{slug}/update',    [DashboardController::class, 'update'])->name('update');
        });
});
