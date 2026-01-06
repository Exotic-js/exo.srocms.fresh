<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CharactersController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\VoteController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/index', [AdminController::class, 'index'])->name('admin');

    Route::prefix('admin')->name('admin.')->group(function() {
        Route::get('/donate-logs', [AdminController::class, 'donateLogs'])->name('donate.logs');
        Route::get('/referral-logs', [AdminController::class, 'referralLogs'])->name('referral.logs');
        Route::get('/vote-logs', [AdminController::class, 'voteLogs'])->name('vote.logs');
        Route::get('/smc-logs', [AdminController::class, 'smcLogs'])->name('smc.logs');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');

        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/view', [UsersController::class, 'view'])->name('users.view');
        Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');

        Route::post('/users/{user}/silk', [UsersController::class, 'silk'])->name('users.silk');
        Route::post('/users/{user}/block', [UsersController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/unblock', [UsersController::class, 'unblock'])->name('users.unblock');

        Route::get('/characters', [CharactersController::class, 'index'])->name('characters.index');
        Route::get('/characters/{char}/view', [CharactersController::class, 'view'])->name('characters.view');
        Route::put('/characters/{char}', [CharactersController::class, 'update'])->name('characters.update');
        Route::put('/characters/{char}/unstuck', [CharactersController::class, 'unstuck'])->name('characters.unstuck');

        Route::get('/news', [NewsController::class, 'index'])->name('news.index');
        Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
        Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
        Route::get('/news/{news}/delete', [NewsController::class, 'confirmDelete'])->name('news.delete');
        Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');

        Route::get('/download', [DownloadController::class, 'index'])->name('download.index');
        Route::get('/download/create', [DownloadController::class, 'create'])->name('download.create');
        Route::post('/download', [DownloadController::class, 'store'])->name('download.store');
        Route::get('/download/{download}/edit', [DownloadController::class, 'edit'])->name('download.edit');
        Route::put('/download/{download}', [DownloadController::class, 'update'])->name('download.update');
        Route::get('/download/{download}/delete', [DownloadController::class, 'confirmDelete'])->name('download.delete');
        Route::delete('/download/{download}', [DownloadController::class, 'destroy'])->name('download.destroy');

        Route::get('/pages', [PagesController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PagesController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PagesController::class, 'store'])->name('pages.store');
        Route::get('/pages/{pages}/edit', [PagesController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{pages}', [PagesController::class, 'update'])->name('pages.update');
        Route::get('/pages/{pages}/delete', [PagesController::class, 'confirmDelete'])->name('pages.delete');
        Route::delete('/pages/{pages}', [PagesController::class, 'destroy'])->name('pages.destroy');

        Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
        Route::get('/vouchers/create', [VoucherController::class, 'create'])->name('vouchers.create');
        Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
        Route::get('/vouchers/{voucher}/toggle', [VoucherController::class, 'toggle'])->name('vouchers.toggle');

        Route::get('/tickets', [TicketController::class,'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [TicketController::class,'show'])->name('ticket.show');
        Route::post('/tickets/{ticket}/reply', [TicketController::class,'reply'])->name('ticket.reply');
        Route::post('/tickets/{ticket}/close', [TicketController::class,'close'])->name('ticket.close');
    });
});
