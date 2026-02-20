<?php

use App\Http\Controllers\Game\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/game', [GameController::class, 'index'])->name('game');
Route::prefix('game')->name('game.')->group(function() {
    Route::any('/webmall', [GameController::class, 'webmall'])->name('webmall');
    Route::any('/ranking', [GameController::class, 'ranking'])->name('ranking');
    Route::any('/survey', [GameController::class, 'survey'])->name('survey');
    Route::any('/fortress', [GameController::class, 'fortress'])->name('fortress');
    Route::any('/banner', [GameController::class, 'banner'])->name('banner');
});
