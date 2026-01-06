<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonateLog;
use App\Models\SRO\Account\TbUser;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $data = TbUser::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('StrUserID', 'like', '%' . $request->search . '%');
            })
            ->paginate(20);

        return view('admin.users.index', compact('data'));
    }

    public function view(TbUser $user)
    {
        $user->load([
            'shardUser' => fn($q) => $q->orderByDesc('CurLevel'),
            'donationLogs' => fn($q) => $q->latest()->limit(10),
            'activeBlock.punishment',
        ]);

        return view('admin.users.view', [
            'user' => $user,
            'vipLevel' => config('ranking.vip_level'),
            'characters' => $user->shardUser,
            'donationLogs' => $user->donationLogs,
            'activePunishment' => $user->activeBlock?->punishment,
            'activeBlock' => $user->activeBlock,
        ]);
    }

    public function update(Request $request, TbUser $user)
    {
        return back()->with('success', 'Test!');
    }

    public function silk(Request $request, TbUser $user)
    {
        $validated = $request->validate([
            'type' => 'required',
            'amount' => 'required|numeric',
        ]);

        $user->giveSilk($validated['type'], $validated['amount']);

        DonateLog::setDonateLog([
            'method' => 'AdminPanel',
            'amount' => $validated['amount'],
            'jid' => $user->JID,
        ]);

        return back()->with('success', 'Silk have been Sent!');
    }

    public function block(Request $request, TbUser $user)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'duration' => 'required|integer|min:1',
            'custom_reason' => 'nullable|string',
        ]);

        $user->blockAccount($validated['reason'], $validated['duration'], $validated['custom_reason'] ?? null);

        return back()->with('success', 'The account has been successfully suspended.');
    }

    public function unblock(Request $request, TbUser $user)
    {
        if ($user->unblockAccount()) {
            return back()->with('success', 'The account has been successfully unblocked.');
        }

        return back()->with('error', 'No active block found.');
    }
}
