<?php

namespace App\Http\Controllers;

use App\Models\Donate;
use App\Models\Referral;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Account\SkSilkBuyList;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\Ticket;
use App\Models\Vote;
use App\Models\Voucher;
use App\Services\VoteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $data = Voucher::getUserVoucher($request->user()->jid);

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

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, $voucher->type, $voucher->amount);
        } else {
            AphChangedSilk::setChangedSilk($user->jid, $voucher->type, $voucher->amount);
        }

        Donate::DonateLog([
            'method' => 'Voucher',
            'value' => $voucher->amount,
            'jid' => $user->jid,
        ]);

        $voucher->update(['jid' => $user->jid, 'status' => 'Used']);

        return redirect()->back()->with('success', 'Voucher redeemed successfully!');
    }

    public function referral(Request $request)
    {
        $user = $request->user();

        $fingerprint = $request->query('fingerprint');

        $invite = Referral::createReferral($user, $fingerprint);

        if ($fingerprint && is_null($invite->fingerprint)) {
            $invite->update(['fingerprint' => $fingerprint]);
            Cache::forget("user_invites_created_{$user->jid}");
        }

        $totalPoints = $user->getInvitesCreated()->whereNotNull('invited_jid')->sum('points');
        $usedInvites = $user->getInvitesCreated()->whereNotNull('invited_jid')->load('invitedUser');
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
        $invites = $user->InvitesCreated()->whereNotNull('invited_jid')->get();

        if(!config('global.referral.enabled', true)) {
            return back()->with('error', "Redeemed invites disabled.");
        }
        if ($invites->sum('points') < $minimumRedeem) {
            return back()->with('error', "You need at least {$minimumRedeem} points to redeem.");
        }

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->jid, 0, $invites->sum('points'));
        } else {
            AphChangedSilk::setChangedSilk($user->jid, 3, $invites->sum('points'));
        }

        Donate::DonateLog([
            'method' => 'Voucher',
            'value' => $invites->sum('points'),
            'jid' => $user->jid,
        ]);

        $user->invitesCreated()->whereNotNull('invited_jid')->update(['points' => 0]);

        return back()->with('success', "{$invites->sum('points')} Silk has been added to your account!");
    }

    public function ticket()
    {
        $data = Ticket::getUserTickets(auth()->id(), 20);

        return view('profile.panel.ticket', compact('data'));
    }

    public function showTicket(int $id)
    {
        $ticket = Ticket::getUserTicket($id, auth()->id());

        abort_if(!$ticket, 403, 'Ticket not yours');

        $replies = Ticket::getReplies($ticket->id);

        return view('profile.panel.ticket-show', [
            'data'    => $ticket,
            'replies' => $replies,
        ]);
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
            'subject'   => 'required_without:parent_id|string|max:255',
            'message'   => 'required|string',
            'category'  => 'required_without:parent_id|in:' . implode(',', $config),
            'parent_id' => 'nullable|integer',
        ]);

        if ($request->filled('parent_id')) {

            $parent = Ticket::getUserTicket($validated['parent_id'], auth()->id());
            abort_if(!$parent || !$parent->status, 403, 'Ticket closed');

            Ticket::replyTo($parent, [
                'message' => $validated['message'],
                'type'    => 'player',
            ]);

            return back()->with('success', 'Reply sent!');
        }

        Ticket::open($validated);

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
            return response('Vote Site not found or disabled.', 403);
        }

        $methodName = "postback" . ucfirst($site);
        if (!method_exists($voteService, $methodName)) {
            return response('Invalid postback method.', 403);
        }

        return $voteService->$methodName($request);
    }
}
