<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donate;
use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Portal\AphChangedSilk;
use App\Models\SRO\Shard\Char;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vote;

class DashboardController extends Controller
{
    public function index()
    {
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
            'userCount' => TbUser::getTbUserCount(),
            'charCount' => Char::getCharCount(),
            'ticketCount' => Ticket::getTicketsCount(),
            'voteCount' => Vote::getVotesCount(),
            'totalDonate' => Donate::getDonateSum(),
            'totalGold' => Char::getGoldSum(),
            'totalSilk' => config('global.server.version') === 'vSRO' ? SkSilk::getSilkSum() : AphChangedSilk::getSilkSum(),
            'systemInfo' => $systemInfo,
        ]);
    }
}
