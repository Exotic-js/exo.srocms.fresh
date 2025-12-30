<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'g-recaptcha-response' => [
                Rule::requiredIf(function () {
                    return env('NOCAPTCHA_ENABLE', false);
                }),
                'captcha'
            ],
        ]);

        $request->authenticate();

        $user = User::where('username', $request->username)->first();

        if (config("settings.otp_verify_jid_{$user->tbUser->JID}") == 1) {
            $code = random_int(100000, 999999);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($code),
                    'created_at' => now()
                ]
            );

            Mail::raw("Your OTP code is: $code", function ($message) use ($user) {
                $message->to($user->email)->subject('Login Verification Code');
            });

            session(['otp_email' => $user->email]);

            return redirect()->route('otp.show');
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

    public function showOTP()
    {
        abort_unless(session('otp_email'), 403);
        return view('auth.verify-otp');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $email = session('otp_email');

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($request->otp, $record->token) || now()->diffInMinutes($record->created_at) > 5) {
            return back()->withErrors(['otp' => 'Invalid or expired code']);
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget('otp_email');

        $user = User::where('email', $email)->first();
        Auth::login($user);

        return redirect()->intended(route('profile', absolute: false));
    }

    public function resendOTP(Request $request)
    {
        $email = session('otp_email');
        abort_unless($email, 403);

        $user = User::where('email', $email)->first();
        $code = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($code),
                'created_at' => now()
            ]
        );

        Mail::raw("Your OTP code is: $code", function ($message) use ($user) {
            $message->to($user->email)->subject('Login Verification Code');
        });

        return back()->with('status', 'Code sent again');
    }
}
