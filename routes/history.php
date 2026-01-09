<?php

use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/history', [HistoryController::class, 'index'])->name('history');
Route::prefix('history')->name('history.')->group(function() {
    Route::get('/schedule', [HistoryController::class, 'schedule'])->name('schedule');
    Route::get('/unique', [HistoryController::class, 'unique'])->name('unique');
    Route::get('/unique-advanced', [HistoryController::class, 'uniqueAdvanced'])->name('unique-advanced');
    Route::get('/fortress', [HistoryController::class, 'fortress'])->name('fortress');
    Route::get('/global', [HistoryController::class, 'global'])->name('global');
    Route::get('/item-plus', [HistoryController::class, 'itemPlus'])->name('item-plus');
    Route::get('/item-drop', [HistoryController::class, 'itemDrop'])->name('item-drop');
    Route::get('/pvp-kill', [HistoryController::class, 'pvpKill'])->name('pvp-kill');
    Route::get('/job-kill', [HistoryController::class, 'jobKill'])->name('job-kill');
});
