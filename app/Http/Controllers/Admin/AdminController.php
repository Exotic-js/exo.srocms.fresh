<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonateLog;
use App\Models\Referral;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Account\SmcLog;
use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\SRO\Shard\Char;
use App\Models\User;
use App\Models\VoteLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $userCount = TbUser::getTbUserCount();
        $charCount = Char::getCharCount();
        $totalGold = Char::getGoldSum();

        if (config('global.server.version') === 'vSRO') {
            $totalSilk = SkSilk::getSilkSum();
        } else {
            $totalSilk = AphChangedSilk::getSilkSum();
        }

        $systemInfo = (object)[
            'phpVersion' => phpversion(),
            'memoryLimit' => ini_get('memory_limit'),
            'memoryUsage' => memory_get_usage(true),
            'memoryPeak' => memory_get_peak_usage(true),
            'diskTotal' => is_readable(base_path()) ? disk_total_space(base_path()) : 0,
            'diskFree'  => is_readable(base_path()) ? disk_free_space(base_path()) : 0,
            'appDebug' => config('app.debug'),
            'adminCount' => User::whereHas('role', fn($q) => $q->where('is_admin', 1))->count(),
        ];

        return view('admin.index', [
            'userCount' => $userCount,
            'charCount' => $charCount,
            'totalGold' => $totalGold,
            'totalSilk' => $totalSilk,
            'systemInfo' => $systemInfo,
        ]);
    }

    public function donateLogs(Request $request)
    {
        $query = DonateLog::query();

        $query->when($request->filled('transaction_id'), fn($q) =>
            $q->where('transaction_id', 'like', "%{$request->transaction_id}%")
        );
        $query->when($request->filled('method_type'), fn($q) =>
            $q->where('method', $request->method_type)
        );
        $query->when($request->filled('status'), fn($q) =>
            $q->where('status', $request->status)
        );
        $query->when($request->filled('jid'), fn($q) =>
            $q->where('jid', $request->jid)
        );
        $query->when($request->filled('ip'), fn($q) =>
            $q->where('ip', 'like', "%{$request->ip}%")
        );

        $data = $query->latest()->paginate(20);

        return view('admin.logs.donate', compact('data'));
    }

    public function referralLogs()
    {
        $data = Referral::getReferralLogs(20);

        return view('admin.logs.referral', compact('data'));
    }

    public function voteLogs()
    {
        $data = VoteLog::latest()->paginate(20);

        return view('admin.logs.vote', compact('data'));
    }

    public function smcLogs(Request $request)
    {
        $query = SmcLog::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($q2) use ($search) {
                $q2->where('szUserID', 'like', "%{$search}%")
                    ->orWhere('szLog', 'like', "%{$search}%");
            });
        });

        $data = $query->latest('dLogDate')->paginate(20);

        return view('admin.logs.smc', compact('data'));
    }
}
