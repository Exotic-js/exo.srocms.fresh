<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonateController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/news', [HomeController::class, 'news'])->name('news');
Route::get('/download', [HomeController::class, 'download'])->name('download');
Route::get('/post/{slug}', [HomeController::class, 'post'])->name('post.show');
Route::get('/page/{slug}', [HomeController::class, 'page'])->name('page.show');

Route::get('/language/{locale}', [HomeController::class, 'locale'])->name('locale');
Route::any('/callback/{method}', [DonateController::class, 'callback'])->name('callback');
Route::any('/webhook/{method}', [DonateController::class, 'webhook'])->name('webhook');
Route::any('/postback/{site}', [PanelController::class, 'postback'])->name('postback');
