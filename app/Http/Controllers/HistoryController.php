<?php

namespace App\Http\Controllers;

use App\Models\SRO\Log\LogChatMessage;
use App\Models\SRO\Log\LogEventChar;
use App\Models\SRO\Log\LogEventItem;
use App\Models\SRO\Log\LogEventSiegeFortress;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Services\ScheduleService;

class HistoryController extends Controller
{
    public function index()
    {
        $data = "Test";
        return view('history.index', compact('data'));
    }

    public function schedule(ScheduleService $scheduleService)
    {
        $data = $scheduleService->getEventSchedules();
        return view('history.schedule', compact('data'));
    }

    public function unique()
    {
        $data = LogInstanceWorldInfo::getUniquesKill();

        $config = (object) [
            'uniqueList' => config('ranking.uniques'),
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('history.unique', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function uniqueAdvanced()
    {
        if (config('ranking.extra.advanced_unique_ranking')) {
            $data = LogInstanceWorldInfo::getUniquesAdvanced(5);
        } else {
            $data = collect();
        }

        $config = (object) [
            'uniqueList' => config('ranking.uniques'),
        ];

        return view('history.unique-advanced', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function fortress()
    {
        $data = LogEventSiegeFortress::getFortressHistory(25);

        $config = (object) [
            'fortressList' => config('widgets.fortress_war'),
        ];

        return view('history.fortress', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function global()
    {
        $data = LogChatMessage::getGlobalsHistory(25);
        return view('history.global', compact('data'));
    }

    public function itemPlus()
    {
        if (config('ranking.extra.item_plus_logs')) {
            $data = LogEventItem::getLogEventItem('plus', 8, 8, 'Seal of Sun', null, 25);
        } else {
            $data = collect();
        }

        return view('history.item-plus', compact('data'));
    }

    public function itemDrop()
    {
        if (config('ranking.extra.item_drop_logs')) {
            $data = LogEventItem::getLogEventItem('drop', null, 8, 'Seal of Sun', null, 25);
        } else {
            $data = collect();
        }

        return view('history.item-drop', compact('data'));
    }

    public function pvpKill()
    {
        if (config('ranking.extra.kill_logs.pvp')) {
            $data = LogEventChar::getKillLogs('pvp', 25);
        } else {
            $data = collect();
        }

        return view('history.pvp-kill', compact('data'));
    }

    public function jobKill()
    {
        if (config('ranking.extra.kill_logs.job')) {
            $data = LogEventChar::getKillLogs('job', 25);
        } else {
            $data = collect();
        }

        return view('history.job-kill', compact('data'));
    }
}
