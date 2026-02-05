<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donate;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Portal\AphChangedSilk;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = TbUser::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('StrUserID', 'like', "%{$request->search}%");
            })
            ->paginate(20);

        return view('admin.users.index', compact('data'));
    }

    public function view(TbUser $user)
    {
        return view('admin.users.view', ['data' => $user]);
    }

    public function update()
    {
        return back()->with('success', 'Test!');
    }

    public function silk(Request $request, TbUser $user)
    {
        $validated = $request->validate([
            'type' => 'required',
            'amount' => 'required|numeric',
        ]);

        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($user->JID, $validated['type'], $validated['amount']);
        } else {
            AphChangedSilk::setChangedSilk($user->PortalJID, $validated['type'], $validated['amount']);
        }

        Donate::DonateLog([
            'method' => 'AdminPanel',
            'value' => $validated['amount'],
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
