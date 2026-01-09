<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\News;
use App\Models\Pages;
use App\Models\SRO\Log\LogChatMessage;
use App\Models\SRO\Log\LogEventChar;
use App\Models\SRO\Log\LogEventItem;
use App\Models\SRO\Log\LogEventSiegeFortress;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Services\ScheduleService;

class PageController extends Controller
{
    public function index()
    {
        $data = News::getPosts();
        return view('pages.index', compact('data'));
    }

    public function news()
    {
        $data = News::getPosts();
        return view('pages.news', compact('data'));
    }

    public function locale($locale)
    {
        if (isset(config('global.languages')[$locale])) {
            session(['locale' => $locale]);
        }
        return back();
    }

    public function post($slug)
    {
        $data = News::getPost($slug);

        abort_if(!$data, 404);

        return view('pages.view', compact('data'));
    }

    public function page($slug)
    {
        $data = Pages::getPage($slug);

        abort_if(!$data, 404);

        return view('pages.page', compact('data'));
    }

    public function download()
    {
        $data = Download::getDownloads();
        return view('pages.download', compact('data'));
    }

    public function timers(ScheduleService $scheduleService)
    {
        $data = $scheduleService->getEventSchedules();
        return view('history.schedule', compact('data'));
    }

    public function uniques()
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

    public function uniquesAdvanced()
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

    public function globals()
    {
        $data = LogChatMessage::getGlobalsHistory(25);
        return view('history.global', compact('data'));
    }

    public function sox_plus()
    {
        $config = config('ranking.extra.item_logs.plus');
        if ($config->enabled) {
            $data = LogEventItem::getLogEventItem('plus', $config->plus, $config->degree, $config->type, null, 25);
        } else {
            $data = collect();
        }

        return view('history.item-plus', compact('data'));
    }

    public function sox_drop()
    {
        $config = config('ranking.extra.item_logs.drop');
        if ($config->enabled) {
            $data = LogEventItem::getLogEventItem('drop', null, $config->degree, $config->type, null, 25);
        } else {
            $data = collect();
        }

        return view('history.item-drop', compact('data'));
    }

    public function pvp_kills()
    {
        if (config('ranking.extra.kill_logs.pvp')) {
            $data = LogEventChar::getKillLogs('pvp', 25);
        } else {
            $data = collect();
        }

        return view('history.pvp-kill', compact('data'));
    }

    public function job_kills()
    {
        if (config('ranking.extra.kill_logs.job')) {
            $data = LogEventChar::getKillLogs('job', 25);
        } else {
            $data = collect();
        }

        return view('history.job-kill', compact('data'));
    }
}
