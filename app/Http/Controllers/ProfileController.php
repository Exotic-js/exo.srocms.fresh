<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\DonateLog;
use App\Models\PasswordResetToken;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\SRO\Account\SecondaryPassword;
use App\Models\SRO\Account\SkSilkBuyList;
use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\SRO\Portal\MuEmail;
use App\Models\SRO\Portal\MuhAlteredInfo;
use App\Models\Voucher;
use App\Notifications\SendVerifyCode;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $config = [
            'characterImage' => config('ranking.character_image'),
            'characterImageVSRO' => config('ranking.character_image_vsro'),
            'characterRace' => config('ranking.character_race'),
            'vipLevel' => config('ranking.vip_level'),
        ];

        return view('profile.index', [
            'user' => $request->user(),
            'config' => $config,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        if (config('settings.update_type') === 'verify_code') {
            return $this->updateEmailByCode($request);
        }

        return $this->updateEmailByPassword($request);
    }

    protected function updateEmailByPassword(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        DB::transaction(function () use ($user) {
            if (config('global.server.version') === 'vSRO') {
                TbUser::where('JID', $user->jid)->update(['Email' => $user->email,]);
            } else {
                MuEmail::where('JID', $user->jid)->update(['EmailAddr' => $user->email,]);

                MuhAlteredInfo::where('JID', $user->jid)->update([
                    'EmailAddr' => $user->email,
                    'EmailReceptionStatus' => config('settings.register_confirm') ? 'N' : 'Y',
                    'EmailCertificationStatus' => config('settings.register_confirm') ? 'N' : 'Y',
                ]);
            }
        });

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    protected function updateEmailByCode(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->validate([
            'verify_code_email' => 'required|string',
            'new_email' => 'required|email',
        ]);

        $user  = $request->user();
        $token = PasswordResetToken::getToken($user->email);

        if (!$token || $request->verify_code_email !== $token->token || $token->isExpired()) {
            return back()->withErrors([
                'verify_code_email' => 'The provided verification code is invalid or expired.',
            ]);
        }

        $user->email = $request->new_email;
        $user->email_verified_at = null;
        $user->save();

        DB::transaction(function () use ($user) {
            if (config('global.server.version') === 'vSRO') {
                TbUser::where('JID', $user->jid)->update(['Email' => $user->email,]);
            } else {
                MuEmail::where('JID', $user->jid)->update(['EmailAddr' => $user->email,]);

                MuhAlteredInfo::where('JID', $user->jid)->update([
                    'EmailAddr' => $user->email,
                    'EmailReceptionStatus' => config('settings.register_confirm') ? 'N' : 'Y',
                    'EmailCertificationStatus' => config('settings.register_confirm') ? 'N' : 'Y',
                ]);
            }
        });

        $token->deleteToken();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        //$user = $request->user();

        //Auth::logout();

        //$user->delete();

        //$request->session()->invalidate();
        //$request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function secondaryPasswordReset(Request $request): RedirectResponse
    {
        if (config('settings.update_type') === 'verify_code') {
            return $this->resetSecondaryPasswordByCode($request);
        }

        return $this->secondaryPasswordResetByPassword($request);
    }

    public function secondaryPasswordResetByPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $tbUser = TbUser::where('password', md5($request->password))->first();
        if (!$tbUser) {
            return back()->with('passcode_error', 'Invalid password provided. Please try again.');
        }

        if (SecondaryPassword::where('UserJID', $tbUser->JID)->delete()) {
            return back()->with('passcode_success', 'Your secondary password has been reset successfully!');
        }

        return back()->with('passcode_error', 'No secondary password was found for your account.');
    }

    private function resetSecondaryPasswordByCode(Request $request): RedirectResponse
    {
        $request->validate([
            'verify_code_secondary' => 'required|string',
        ]);

        $user = $request->user();
        $token = PasswordResetToken::getToken($user->email);

        if (!$token || $request->verify_code_secondary !== $token->token || $token->isExpired()) {
            return back()->withErrors(['verify_code_secondary' => 'The provided verification code is invalid or expired.']);
        }

        $token->deleteToken();

        if (SecondaryPassword::where('UserJID', $user->tbUser->JID)->delete()) {
            return back()->with('passcode_success', 'Your secondary password has been reset successfully!');
        }

        return back()->with('passcode_error', 'No secondary password was found for your account.');
    }

    public function sendVerifyCode(Request $request)
    {
        $request->validate([
            'context' => 'required|string',
        ]);

        $user = $request->user();
        $code = random_int(100000, 999999);

        PasswordResetToken::setToken($user->email, $code);

        $user->notify(new SendVerifyCode($code));

        return back()->with('verify_code_sent', $request->input('context'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->except(['_token']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
        }

        return back()->with('success', 'Settings updated!');
    }

    public function silkHistory(Request $request): View
    {
        $page = $request->get('page', 1);
        if (config('global.server.version') === 'vSRO') {
            $data = SkSilkBuyList::getSilkHistory($request->user()->jid, 25, $page);
        }else {
            $data = AphChangedSilk::getSilkHistory($request->user()->jid, 25, $page);
        }

        return view('profile.silk-history', [
            'user' => $request->user(),
            'data' => $data,
        ]);
    }

    public function vouchers(Request $request)
    {
        $data = Voucher::where('jid', $request->user()->jid)->get();

        return view('profile.voucher', [
            'data' => $data,
        ]);
    }

    public function redeemVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $voucher = Voucher::where('code', $request->voucher_code)->first();

        if (!$voucher || $voucher->status == 'Disabled') {
            return redirect()->back()->with('error', 'Invalid voucher code.');
        }

        if ($voucher->status == 'Used') {
            return redirect()->back()->with('error', 'This voucher has already been used.');
        }

        if ($voucher->valid_date && Carbon::now()->greaterThan($voucher->valid_date)) {
            return redirect()->back()->with('error', 'This voucher has expired.');
        }

        $user = $request->user();

        $user->tbUser->giveSilk($voucher->type, $voucher->amount);

        DonateLog::setDonateLog([
            'method' => 'Voucher',
            'amount' => $voucher->amount,
            'jid' => $user->jid,
        ]);

        $voucher->update(['jid' => $user->jid, 'status' => 'Used']);

        return redirect()->back()->with('success', 'Voucher redeemed successfully!');
    }

    public function referral(Request $request): View
    {
        $user = $request->user();

        $fingerprint = $request->query('fingerprint') ?? session('fingerprint');
        if ($fingerprint && session('fingerprint') !== $fingerprint) {
            session(['fingerprint' => $fingerprint]);
        }

        $invite = Referral::createReferral($user, session('fingerprint'));

        $totalPoints = $user->invitesCreated()->whereNotNull('invited_jid')->sum('points');
        $usedInvites = $user->invitesCreated()->whereNotNull('invited_jid')->with('invitedUser')->get();
        $minimumRedeem = config('global.referral.minimum_redeem', 25);

        return view('profile.referral', [
            'invite' => $invite,
            'usedInvites' => $usedInvites,
            'totalPoints' => $totalPoints,
            'minimumRedeem' => $minimumRedeem,
        ]);
    }

    public function redeemReferral(Request $request)
    {
        $user = $request->user();
        $minimumRedeem = config('global.referral.minimum_redeem', 25);
        $invites = $user->invitesCreated()->whereNotNull('invited_jid')->get();

        if(!config('global.referral.enabled', true)) {
            return back()->with('error', "Redeemed invites disabled.");
        }
        if ($invites->sum('points') < $minimumRedeem) {
            return back()->with('error', "You need at least {$minimumRedeem} points to redeem.");
        }

        $user->tbUser->giveSilk(3, $invites->sum('points'));

        DonateLog::setDonateLog([
            'method' => 'Voucher',
            'amount' => $invites->sum('points'),
            'jid' => $user->jid,
        ]);

        $user->invitesCreated()->whereNotNull('invited_jid')->update(['points' => 0]);

        return back()->with('success', "{$invites->sum('points')} Silk has been added to your account!");
    }
}
