<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonateController;

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/news', [PageController::class, 'news'])->name('news');
Route::get('/download', [PageController::class, 'download'])->name('download');
Route::get('/post/{slug}', [PageController::class, 'post'])->name('pages.post.show');
Route::get('/page/{slug}', [PageController::class, 'page'])->name('pages.page.show');

Route::get('/language/{locale}', [PageController::class, 'locale'])->name('locale');
Route::any('/callback/{method}', [DonateController::class, 'callback'])->name('callback');
Route::any('/webhook/{method}', [DonateController::class, 'webhook'])->name('webhook');
Route::any('/postback/{site}', [PanelController::class, 'postback'])->name('postback');

require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
require __DIR__.'/history.php';
require __DIR__.'/ranking.php';
require __DIR__.'/admin.php';
