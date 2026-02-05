<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\PasswordResetToken;
use App\Models\Setting;
use App\Models\SRO\Account\SecondaryPassword;
use App\Models\SRO\Account\TbUser;
use App\Notifications\SendVerifyCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
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

        $user->updateGameEmail();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    protected function updateEmailByCode(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->validate([
            'verify_code_email' => 'required|string',
            'new_email' => 'nullable|email',
            'verify_login' => 'nullable|in:0,1',
        ]);

        $user  = $request->user();
        $token = PasswordResetToken::getToken($user->email);

        if (!$token || $request->verify_code_email !== $token->token || $token->isExpired()) {
            return back()->withErrors(['verify_code_email' => 'The provided verification code is invalid or expired.',]);
        }

        if ($request->filled('new_email')) {
            $user->email = $request->new_email;
            $user->email_verified_at = null;
            $user->save();

            $user->updateGameEmail();
        }

        if ($request->has('verify_login')) {
            $verifyLogin = $request->input('verify_login');
            Setting::set("verify_jid_{$user->tbUser->JID}", $verifyLogin);
        }

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

        $user = $request->user();

        Auth::logout();

        //$user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
}
