<?php

namespace App\Http\Controllers\Auth;

use App\Models\PasswordResetToken;
use App\Models\User;
use App\Notifications\SendVerifyCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
            'g-recaptcha-response' => [Rule::requiredIf(function () {return env('NOCAPTCHA_ENABLE', false);}), 'captcha'],
        ]);

        $request->authenticate();

        $response = $this->sendVerify();

        if ($response) {
            return $response;
        }

        $request->session()->regenerate();

        return redirect()->intended(route('profile', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function show()
    {
        if (!session('login_verify_user')) {
            return redirect()->route('login');
        }

        return view('auth.login-verify');
    }

    protected function sendVerify(): ?RedirectResponse
    {
        $user = Auth::user();

        if (! config("settings.verify_jid_{$user->tbUser?->JID}")) {
            return null;
        }

        $code = random_int(100000, 999999);

        PasswordResetToken::setToken($user->email, $code);

        $user->notify(new SendVerifyCode($code));

        session([
            'login_verify_user' => $user->id,
            'login_verify_time' => now(),
        ]);

        return redirect()->route('login.verify');
    }

    public function resendVerify(Request $request)
    {

        $userId = session('login_verify_user');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        $token = PasswordResetToken::getToken($user->email);

        if ($token && !$token->isExpired(1)) {
            return back()->withErrors(['code' => 'Please wait before requesting a new code.']);
        }

        $code = random_int(100000, 999999);

        PasswordResetToken::setToken($user->email, $code);

        $user->notify(new SendVerifyCode($code));

        return back()->with('status', 'Verification code sent again');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('login_verify_user');
        $user = User::findOrFail($userId);

        $token = PasswordResetToken::getToken($user->email);

        if (!$token || $token->isExpired() || $token->token !== $request->code) {
            return back()->withErrors(['code' => 'Invalid or expired verification code']);
        }

        $token->deleteToken();

        Auth::login($user);

        session()->forget(['login_verify_user', 'login_verify_time']);

        return redirect()->intended(route('profile', absolute: false));
    }
}
