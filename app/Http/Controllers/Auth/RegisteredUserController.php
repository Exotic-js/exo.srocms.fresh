<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\SRO\Portal\AuhAgreedService;
use App\Models\SRO\Portal\MuEmail;
use App\Models\SRO\Portal\MuhAlteredInfo;
use App\Models\SRO\Portal\MuJoiningInfo;
use App\Models\SRO\Portal\MuUser;
use App\Models\SRO\Portal\MuVIPInfo;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (config('settings.disable_register')) {
            return back()->withErrors(['username' => ["Register page is disabled!"]]);
        }

        $rules = [
            'username' => ['required', 'regex:/^[A-Za-z0-9]*$/', 'min:6', 'max:16', 'unique:' . User::class],
            'email' => ['required', 'string', 'email', 'max:70'],
            'password' => ['required', 'min:6', 'max:32', 'confirmed'],
            'g-recaptcha-response' => env('NOCAPTCHA_ENABLE', false) ? ['required', 'captcha'] : ['nullable'],
            'terms' => config('settings.agree_terms', false) ? ['required', 'accepted'] : ['nullable'],
            'invite' => ['nullable', 'string'],
            'fingerprint' => ['nullable', 'string'],
        ];

        if (config('global.server.version') === 'vSRO') {
            $rules['username'][] = 'unique:' . TbUser::class . ',StrUserID';
        }elseif (config('global.server.version') === 'vSRO' && !config('settings.duplicate_email', 1)) {
            $rules['email'][] = 'unique:' . User::class . ',email';
            $rules['email'][] = 'unique:' . TbUser::class . ',Email';
        } else {
            $rules['email'][] = 'unique:' . User::class . ',email';
            $rules['username'][] = 'unique:' . MuUser::class . ',UserID';
            $rules['username'][] = 'unique:' . TbUser::class . ',StrUserID';
            $rules['email'][] = 'unique:' . MuEmail::class . ',EmailAddr';
        }

        $request->validate($rules);
        $ip = filter_var($ip = $request->ip(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : '0.0.0.0';

        DB::beginTransaction();
        try {
            if (config('global.server.version') === 'vSRO') {
                $tbUser = TbUser::setGameAccount($jid = null, $request->username, $request->password, $request->email, $ip);
                $jid = $tbUser->JID;

                SkSilk::setSkSilk($jid, 0, 0);
            } else {
                $userBinIP = ip2long($ip);

                $portalUser = MuUser::setPortalAccount($request->username, $request->password);
                $jid = $portalUser->JID;

                MuEmail::setEmail($jid, $request->email);
                MuhAlteredInfo::setAlteredInfo($jid, $request->username, $request->email, $userBinIP);
                AuhAgreedService::setAgreedService($jid, $userBinIP);
                MuJoiningInfo::setJoiningInfo($jid, $userBinIP);
                MuVIPInfo::setVIPInfo($jid);

                //type 1 = silk, type 3 = premium silk
                //AphChangedSilk::setChangedSilk($jid, 1, 0);
                //AphChangedSilk::setChangedSilk($jid, 3, 0);
                TbUser::setGameAccount($jid, $request->username, $request->password, $request->email, $request->ip());
            }

            if (config('global.referral.enabled', true)) {
                if ($request->filled('invite')) {
                    $invite = Referral::where('code', $request->invite)->first();

                    if ($invite) {
                        if ($invite->ip !== $request->ip() && $invite->fingerprint !== $request->fingerprint) {
                            Referral::create([
                                'code' => $invite->code,
                                'name' => $invite->name,
                                'jid' => $invite->jid,
                                'invited_jid' => $jid,
                                'points' => config('global.referral.reward_points', 0),
                            ]);
                        }else {
                            Referral::create([
                                'code' => $invite->code,
                                'name' => $invite->name,
                                'ip' => 'CHEATING',
                                'jid' => $invite->jid,
                                'invited_jid' => $jid,
                                'points' => 0,
                            ]);
                        }
                    }
                }
            }

            $user = User::create([
                'jid' => $jid,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['username' => [$e->getMessage()]]);
        }

        if (config('settings.register_confirm')) {
            event(new Registered($user));
        }

        Auth::login($user);

        return redirect(route('profile', absolute: false));
    }
}
