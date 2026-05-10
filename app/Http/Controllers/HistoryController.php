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
        abort_if(!config('global.history.event_schedule', true), 404);
        $data = $scheduleService->getEventSchedules();
        return view('history.schedule', compact('data'));
    }

    public function unique()
    {
        abort_if(!config('global.history.unique_tracker', true), 404);
        $data = LogInstanceWorldInfo::getUniquesKill();
        return view('history.unique', compact('data'));
    }

    public function uniqueAdvanced()
    {
        abort_if(!config('global.history.advanced_unique_tracker', false), 404);
        $data = LogInstanceWorldInfo::getUniquesAdvanced(5);
        return view('history.unique-advanced', compact('data'));
    }

    public function fortress()
    {
        abort_if(!config('global.history.fortress_history', true), 404);
        $data = LogEventSiegeFortress::getFortressHistory(25);
        return view('history.fortress', compact('data'));
    }

    public function global()
    {
        abort_if(!config('global.history.global_history', true), 404);
        $data = LogChatMessage::getGlobalsHistory(25);
        return view('history.global', compact('data'));
    }

    public function itemPlus()
    {
        abort_if(!config('global.history.item_plus_logs', false), 404);
        $data = LogEventItem::getLogEventItem('plus', 8, 8, 'Seal of Sun', null, 25);
        return view('history.item-plus', compact('data'));
    }

    public function itemDrop()
    {
        abort_if(!config('global.history.item_drop_logs', false), 404);
        $data = LogEventItem::getLogEventItem('drop', null, 8, 'Seal of Sun', null, 25);
        return view('history.item-drop', compact('data'));
    }

    public function pvpKill()
    {
        abort_if(!config('global.history.pvp_kill_logs', false), 404);
        $data = LogEventChar::getKillLogs('pvp', 25);
        return view('history.pvp-kill', compact('data'));
    }

    public function jobKill()
    {
        abort_if(!config('global.history.job_kill_logs', false), 404);
        $data = LogEventChar::getKillLogs('job', 25);
        return view('history.job-kill', compact('data'));
    }
}
