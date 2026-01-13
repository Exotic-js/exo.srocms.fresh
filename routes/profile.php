<?php

use App\Http\Controllers\DonateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\Route;

Route::middleware(array_filter(['auth', config('settings.register_confirm') ? 'verified' : null]))->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::prefix('profile')->name('profile.')->group(function() {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/edit', [ProfileController::class, 'update'])->name('update');
        Route::delete('/edit', [ProfileController::class, 'destroy'])->name('destroy');

        Route::post('/edit/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
        Route::post('/edit/send-verify-code', [ProfileController::class, 'sendVerifyCode'])->name('resend.verify.code');
        Route::post('/edit/reset-secondary-password', [ProfileController::class, 'secondaryPasswordReset'])->name('reset.secondary.password');

        Route::get('/donate', [DonateController::class, 'index'])->name('donate');
        Route::get('/donate/{method}', [DonateController::class, 'show'])->name('donate.show');
        Route::post('/donate/{method}/process', [DonateController::class, 'process'])->middleware('throttle:5,1')->name('donate.process');

        Route::get('/tickets', [PanelController::class, 'ticket'])->name('tickets');
        Route::get('/tickets/create', [PanelController::class, 'createTicket'])->name('ticket.create');
        Route::get('/tickets/{ticket}', [PanelController::class, 'showTicket'])->name('ticket.show');
        Route::post('/tickets/send', [PanelController::class, 'sendTicket'])->name('ticket.send');

        Route::get('/voucher', [PanelController::class, 'vouchers'])->name('voucher');
        Route::post('/voucher/redeem', [PanelController::class, 'redeemVoucher'])->name('voucher.redeem');

        Route::get('/referral', [PanelController::class, 'referral'])->name('referral');
        Route::post('/referral-redeem', [PanelController::class, 'redeemReferral'])->name('referral.redeem');

        Route::get('/vote', [PanelController::class, 'vote'])->name('vote');
        Route::get('/vote/{id}', [PanelController::class, 'voting'])->name('vote.voting');

        Route::get('/silk-history', [PanelController::class, 'silkHistory'])->name('silk-history');
    });
});
