<?php

use App\Http\Controllers\DonateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(array_filter(['auth', config('settings.register_confirm') ? 'verified' : null]))->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::prefix('profile')->name('profile.')->group(function() {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/edit', [ProfileController::class, 'update'])->name('update');
        Route::delete('/edit', [ProfileController::class, 'destroy'])->name('destroy');

        Route::post('/edit/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
        Route::post('/edit/resend-verify-code', [ProfileController::class, 'resendVerifyCode'])->name('resend.verify.code');
        Route::post('/edit/reset-secondary-password', [ProfileController::class, 'secondaryPasswordReset'])->name('reset.secondary.password');

        Route::get('/voucher', [ProfileController::class, 'voucher'])->name('voucher');
        Route::post('/voucher-redeem', [ProfileController::class, 'redeemVoucher'])->name('voucher.redeem');

        Route::get('/referral', [ProfileController::class, 'referral'])->name('referral');
        Route::post('/referral-redeem', [ProfileController::class, 'redeemReferral'])->name('referral.redeem');
        Route::post('/referral-fingerprint', [ProfileController::class, 'fingerprintReferral'])->name('referral.fingerprint');
        Route::get('/silk-history', [ProfileController::class, 'silkHistory'])->name('silk-history');

        Route::get('/vote', [VoteController::class, 'index'])->name('vote');
        Route::post('/vote/{id}', [VoteController::class, 'voting'])->name('vote.voting');

        Route::get('/donate', [DonateController::class, 'index'])->name('donate');
        Route::get('/donate/{method}', [DonateController::class, 'show'])->name('donate.show');
        Route::post('/donate/{method}/process', [DonateController::class, 'process'])->middleware('throttle:5,1')->name('donate.process');

        Route::get('/tickets', [ProfileController::class,'tickets'])->name('tickets');
        Route::get('/tickets/create', [ProfileController::class,'createTicket'])->name('ticket.create');
        Route::get('/tickets/{ticket_id}', [ProfileController::class,'showTicket'])->name('ticket.show');
        Route::post('/tickets/send', [ProfileController::class,'sendTicket'])->name('ticket.send');
    });
});
