<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\DonateLog;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Account\SkSilkBuyList;
use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\SRO\Portal\MuEmail;
use App\Models\SRO\Portal\MuhAlteredInfo;
use App\Models\Ticket;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $characterRace = config('ranking.character_race');
        $vipLevel = config('ranking.vip_level');

        if (config('global.server.version') === 'vSRO') {
            $characterImage = config('ranking.character_image_vsro');
        }else {
            $characterImage = config('ranking.character_image');
        }

        return view('profile.index', [
            'user' => $request->user(),
            'characterImage' => $characterImage,
            'characterRace' => $characterRace,
            'vipLevel' => $vipLevel,
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
        if (config('settings.update_type') == 'verify_code') {
            $email = $request->input('new_email');
            $codeRecord = DB::table('password_reset_tokens')->where('email', $request->user()->email)->first();

            if (!$codeRecord || !($request->input('verify_code_email') === $codeRecord->token) || Carbon::parse($codeRecord->created_at)->addMinutes(30)->isPast()) {
                return back()->withErrors(['verify_code_email' => 'The provided verification code is invalid or expired.']);
            }

            $request->user()->email = $email;
            $request->user()->email_verified_at = null;
            $request->user()->save();
        }else {
            $email = $request->input('email');
            $request->user()->fill($request->validated());

            if ($request->user()->isDirty('email')) {
                $request->user()->email_verified_at = null;
            }

            $request->user()->save();
        }

        DB::beginTransaction();
        try {

            if (config('global.server.version') === 'vSRO') {
                TbUser::where('JID', $request->user()->jid)->update(['Email' => $email]);
            }else {
                MuEmail::where('JID', $request->user()->jid)->update(['EmailAddr' => $email]);

                if (config('settings.register_confirm')) {
                    MuhAlteredInfo::where('JID', $request->user()->jid)->update(['EmailAddr' => $email, 'EmailReceptionStatus' => 'N', 'EmailCertificationStatus' => 'N']);
                } else {
                    MuhAlteredInfo::where('JID', $request->user()->jid)->update(['EmailAddr' => $email, 'EmailReceptionStatus' => 'Y', 'EmailCertificationStatus' => 'Y']);
                }
            }

            if (config('settings.update_type') == 'verify_code') {
                DB::table('password_reset_tokens')->where('email', $request->user()->email)->delete();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['email' => ["Something went wrong, Please try again later."]]);
        }

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

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings updated!');
    }

    public function resendVerifyCode(Request $request)
    {
        $user = $request->user();
        $code = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $code, 'created_at' => now()]
        );

        Mail::raw("Your verification code is: $code", function ($message) use ($user) {
            $message->to($user->email)->subject('Email Change Verification Code');
        });

        return back()->with('status', $request->input('send-verify-code-name'));
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

    public function secondaryPasswordReset(Request $request): RedirectResponse
    {
        if (config('settings.update_type') == 'verify_code') {
            $request->validate([
                'verify_code_secondary' => 'required|string',
            ]);

            $codeRecord = DB::table('password_reset_tokens')->where('email', $request->user()->email)->first();

            if (!$codeRecord || !($request->input('verify_code_secondary') === $codeRecord->token) || Carbon::parse($codeRecord->created_at)->addMinutes(30)->isPast()) {
                return back()->withErrors(['verify_code_secondary' => 'The provided verification code is invalid or expired.']);
            }

            $tbUser = $request->user()->tbUser;
            $deleted = DB::connection('account')->table('_SecondaryPassword')->where('UserJID', $tbUser->JID)->delete();

            DB::table('password_reset_tokens')->where('email', $request->user()->email)->delete();

            if ($deleted) {
                return back()->with('passcode_success', 'Your secondary password has been reset successfully!');
            } else {
                return back()->with('passcode_error', 'No secondary password was found for your account.');
            }
        } else {
            $request->validate([
                'password' => 'required|string',
            ]);

            $tbUser = TbUser::where('password', md5($request->password))->first();
            if ($tbUser) {
                $deleted = DB::connection('account')->table('_SecondaryPassword')->where('UserJID', $tbUser->JID)->delete();
                if ($deleted) {
                    return back()->with('passcode_success', 'Your secondary password has been reset successfully!');
                } else {
                    return back()->with('passcode_error', 'No secondary password was found for your account.');
                }
            }
        }

        return redirect()->back()->with('passcode_error', 'Invalid password provided. Please try again.');
    }

    public function voucher()
    {
        $data = Voucher::where('jid', Auth::user()->jid)->get();

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

        $user = Auth::user();
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, $voucher->type, $voucher->amount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, $voucher->type, $voucher->amount);
        }

        DonateLog::setDonateLog(
            'Voucher',
            (string) Str::uuid(),
            'true',
            0,
            $voucher->amount,
            "User:{$user->username} Has Redeemed:{$voucher->code}",
            $user->jid,
            $request->ip()
        );

        $voucher->update(['jid' => $user->jid, 'status' => 'Used']);

        return redirect()->back()->with('success', 'Voucher redeemed successfully!');
    }

    public function referral()
    {
        $invite = auth()->user()->invitesCreated()->first();
        $usedInvites = auth()->user()->invitesCreated()->whereNotNull('invited_jid')->with('invitedUser')->get();
        $totalPoints = $usedInvites->sum('points');
        $minimumRedeem = config('global.referral.minimum_redeem', 25);

        return view('profile.referral', [
            'invite' => $invite,
            'usedInvites' => $usedInvites,
            'totalPoints' => $totalPoints,
            'minimumRedeem' => $minimumRedeem,
        ]);
    }

    public function fingerprintReferral(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string|max:255',
        ]);
        $fingerprint = $request->input('fingerprint');
        $ip = $request->ip();

        session(['fingerprint' => $fingerprint]);

        $invite = Auth::user()->invitesCreated()->first();
        if (!$invite) {
            try {
                do {
                    $code = strtoupper(Str::random(8));
                } while (Referral::where('code', $code)->exists());

                Referral::create([
                    'code' => $code,
                    'name' => Auth::user()->username,
                    'jid' => Auth::user()->jid,
                    'ip' => $ip,
                    'fingerprint' => $fingerprint,
                ]);

                return response()->json(['status' => 'ok']);
            } catch (\Exception $e) {

            }
        }

        return response()->json(['status' => 'error']);
    }

    public function redeemReferral()
    {
        $user = Auth::user();
        $minimumRedeem = config('global.referral.minimum_redeem', 25);
        $invites = $user->invitesCreated()->whereNotNull('invited_jid')->get();

        if(!config('global.referral.enabled', true)) {
            return back()->with('error', "Redeemed invites disabled.");
        }
        if ($invites->sum('points') < $minimumRedeem) {
            return back()->with('error', "You need at least {$minimumRedeem} points to redeem.");
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 3, $invites->sum('points'));
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $invites->sum('points'));
        }

        DonateLog::setDonateLog(
            'Referral',
            Str::uuid(),
            'true',
            0,
            $invites->sum('points'),
            "AdminJID:{$user->jid} has sent:{$invites->sum('points')} silk",
            $user->jid,
            '127.0.0.1',
        );

        foreach ($invites as $invite) {
            $invite->update(['points' => 0]);
        }

        return back()->with('success', "{$invites->sum('points')} Silk has been added to your account!");
    }

    public function tickets()
    {
        $data = Ticket::where('user_id', auth()->id())->whereNull('parent_id')->latest()->paginate(20);

        return view('profile.tickets.index', compact('data'));
    }

    public function createTicket()
    {
        $config = config('global.tickets.categories');

        return view('profile.tickets.create', compact('config'));
    }

    public function showTicket(Ticket $ticket)
    {
        $ticket->load('replies');

        return view('profile.tickets.show', compact('ticket'));
    }

    public function sendTicket(Request $request)
    {
        $config = array_keys(config('global.tickets.categories'));

        $validated = $request->validate([
            'subject'   => 'required_without:parent_id|string|max:255',
            'message'   => 'required|string',
            'category'  => 'required_without:parent_id|in:' . implode(',', $config),
            'parent_id' => 'nullable|integer',
        ]);

        if ($request->filled('parent_id')) {
            $parentTicket = Ticket::findOrFail($request->parent_id);

            Ticket::create([
                'parent_id' => $parentTicket->id,
                'user_id'   => auth()->id(),
                'subject'   => $parentTicket->subject,
                'category'  => $parentTicket->category,
                'message'   => $validated['message'],
                'type'      => 'player',
                'status'    => true,
            ]);

            return back()->with('success', 'Reply sent!');
        }

        Ticket::create([
            'user_id'  => auth()->id(),
            'subject'  => $validated['subject'],
            'category' => $validated['category'],
            'message'  => $validated['message'],
            'type'     => 'player',
            'status'   => true,
        ]);

        return redirect()->route('profile.tickets')->with('success', 'Ticket created!');
    }
}
