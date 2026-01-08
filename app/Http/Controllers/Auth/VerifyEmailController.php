<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SRO\Portal\MuhAlteredInfo;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('profile', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            if (config('global.server.version') !== 'vSRO') {
                DB::transaction(function () use ($request) {
                    MuhAlteredInfo::where('JID', $request->user()->jid)->update(['EmailReceptionStatus' => 'Y', 'EmailCertificationStatus' => 'Y']);
                });
            }

            event(new Verified($request->user()));
        }

        return redirect()->intended(route('profile', absolute: false).'?verified=1');
    }
}
