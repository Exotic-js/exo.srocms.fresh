<?php

use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');
Route::prefix('ranking')->name('ranking.')->group(function() {
    Route::any('/player', [RankingController::class, 'playerRanking'])->name('player');
    Route::any('/guild', [RankingController::class, 'guildRanking'])->name('guild');
    Route::any('/unique', [RankingController::class, 'uniqueRanking'])->name('unique');
    Route::any('/unique-monthly', [RankingController::class, 'uniqueMonthlyRanking'])->name('unique-monthly');
    Route::any('/honor', [RankingController::class, 'honorRanking'])->name('honor');
    Route::any('/job', [RankingController::class, 'jobRanking'])->name('job');
    Route::any('/job-all', [RankingController::class, 'jobAllRanking'])->name('job-all');
    Route::any('/job-hunter', [RankingController::class, 'jobHunterRanking'])->name('job-hunter');
    Route::any('/job-thieve', [RankingController::class, 'jobThieveRanking'])->name('job-thieve');
    Route::any('/job-trader', [RankingController::class, 'jobTraderRanking'])->name('job-trader');
    Route::any('/fortress-player', [RankingController::class, 'fortressPlayerRanking'])->name('fortress-player');
    Route::any('/fortress-guild', [RankingController::class, 'fortressGuildRanking'])->name('fortress-guild');
    Route::any('/pvp-kd', [RankingController::class, 'pvpKDRanking'])->name('pvp-kd');
    Route::any('/job-kd', [RankingController::class, 'jobKDRanking'])->name('job-kd');
    Route::any('/custom/{type}', [RankingController::class, 'customRanking'])->name('custom');

    Route::get('/character/{name}', [RankingController::class, 'characterView'])->name('character.view');
    Route::get('/guild/{name}', [RankingController::class, 'guildView'])->name('guild.view');
    Route::any('/guild/crest/{bin}', [RankingController::class, 'guildCrest'])->name('guild.crest');
});
