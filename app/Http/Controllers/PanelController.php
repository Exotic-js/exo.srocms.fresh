<?php

namespace App\Http\Controllers;

use App\Models\Donate;
use App\Models\Referral;
use App\Models\SRO\Account\SkSilkBuyList;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\Ticket;
use App\Models\Vote;
use App\Models\Voucher;
use App\Services\VoteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanelController extends Controller
{

    public function silkHistory(Request $request): View
    {
        $page = $request->get('page', 1);
        if (config('global.server.version') === 'vSRO') {
            $data = SkSilkBuyList::getSilkHistory($request->user()->jid, 25, $page);
        }else {
            $data = AphChangedSilk::getSilkHistory($request->user()->jid, 25, $page);
        }

        return view('profile.panel.silk-history', [
            'user' => $request->user(),
            'data' => $data,
        ]);
    }

    public function vouchers(Request $request)
    {
        $data = Voucher::where('jid', $request->user()->jid)->get();

        return view('profile.panel.voucher', [
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

        Donate::DonateLog([
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

        $fingerprint = $request->cookie('fingerprint') ?? session('fingerprint');
        if ($fingerprint && session('fingerprint') !== $fingerprint) {
            session(['fingerprint' => $fingerprint]);
        }

        $invite = Referral::createReferral($user, session('fingerprint'));

        $totalPoints = $user->invitesCreated()->whereNotNull('invited_jid')->sum('points');
        $usedInvites = $user->invitesCreated()->whereNotNull('invited_jid')->with('invitedUser')->get();
        $minimumRedeem = config('global.referral.minimum_redeem', 25);

        return view('profile.panel.referral', [
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

        Donate::DonateLog([
            'method' => 'Voucher',
            'amount' => $invites->sum('points'),
            'jid' => $user->jid,
        ]);

        $user->invitesCreated()->whereNotNull('invited_jid')->update(['points' => 0]);

        return back()->with('success', "{$invites->sum('points')} Silk has been added to your account!");
    }

    public function ticket()
    {
        $data = auth()->user()->tickets()->paginate(20);

        return view('profile.panel.ticket', compact('data'));
    }

    public function showTicket(Ticket $ticket)
    {
        $data = $ticket->load('replies');

        if ($ticket->user_id != auth()->id()) {
            abort(403, 'Ticket not yours');
        }

        return view('profile.panel.ticket-show', compact('data'));
    }

    public function createTicket()
    {
        $data = config('global.tickets.categories');

        return view('profile.panel.ticket-create', compact('data'));
    }

    public function sendTicket(Request $request)
    {
        $config = array_keys(config('global.tickets.categories'));

        $validated = $request->validate([
            'subject' => 'required_without:parent_id|string|max:255',
            'message' => 'required|string',
            'category' => 'required_without:parent_id|in:' . implode(',', $config),
            'parent_id' => 'nullable|integer',
        ]);

        if ($request->filled('parent_id')) {
            $parentTicket = Ticket::findOrFail($request->parent_id);

            if ($parentTicket->user_id != auth()->id()) {
                abort(403, 'Ticket not yours');
            }

            Ticket::createTicket([
                'parent_id'=> $parentTicket->id,
                'message'  => $validated['message'],
            ]);

            return back()->with('success', 'Reply sent!');
        }

        Ticket::createTicket([
            'subject'  => $validated['subject'],
            'category' => $validated['category'],
            'message'  => $validated['message'],
        ]);

        return redirect()->route('profile.tickets')->with('success', 'Ticket created!');
    }

    public function vote(Request $request)
    {
        $data = Vote::getVotes($request, session('fingerprint'));

        return view('profile.panel.vote', compact('data'));
    }

    public function voting(string $site, Request $request)
    {
        $config = config("vote.$site");
        abort_if(!$config || !$config['enabled'], 404);

        $user = $request->user();

        $fingerprint = $request->input('fingerprint') ?? session('fingerprint');

        if (!$fingerprint) {
            return back()->with('error', 'Fingerprint not detected.');
        }

        session(['fingerprint' => $fingerprint]);

        if ($voteLog = Vote::activeVote($config['route'], $request->ip(), $fingerprint)) {
            return back()->with('error', "You have already voted. Please wait until {$voteLog->expire} to vote again for {$config['name']}.");
        }

        Vote::updateOrCreate(
            ['jid' => $user->jid, 'site' => $config['route']],
            ['ip' => $request->ip(), 'fingerprint' => $fingerprint]
        );

        $url = str_replace('{JID}', $user->jid, $config['url']);
        return redirect()->away($url);
    }

    public function postback($site, Request $request, VoteService $voteService)
    {
        $config = config("vote.{$site}");

        if (!$config || !$config['enabled']) {
            return redirect()->back()->withErrors('Vote Site not found or disabled.');
        }

        if (!method_exists($voteService, "postback" . ucfirst($site))) {
            return redirect()->back()->withErrors('Invalid postback method.');
        }

        return $voteService->{"postback" . ucfirst($site)}($request);
    }
}
