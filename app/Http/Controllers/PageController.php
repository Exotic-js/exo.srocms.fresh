<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\News;
use App\Models\Pages;
use App\Models\SRO\Account\WebItemCertifyKey;
use App\Models\SRO\Log\LogChatMessage;
use App\Models\SRO\Log\LogEventChar;
use App\Models\SRO\Log\LogEventItem;
use App\Models\SRO\Log\LogEventSiegeFortress;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Services\ScheduleService;
use Illuminate\Http\Request;

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
        return view('pages.timers', compact('data'));
    }

    public function uniques()
    {
        $data = LogInstanceWorldInfo::getUniquesKill();
        $config = config('ranking.uniques');
        $characterRace = config('ranking.character_race');

        return view('pages.uniques', [
            'data' => $data,
            'config' => $config,
            'characterRace' => $characterRace,
        ]);
    }

    public function uniquesAdvanced()
    {
        $config = config('ranking.uniques');

        if (!config('ranking.extra.advanced_unique_ranking')) {
            $data = collect();
        } else {
            $data = LogInstanceWorldInfo::getUniquesTop(5);
        }

        return view('pages.uniques-advanced', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function fortress()
    {
        $data = LogEventSiegeFortress::getFortressHistory(25);
        $config = config('widgets.fortress_war');

        return view('pages.fortress', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function globals()
    {
        $data = LogChatMessage::getGlobalsHistory(25);
        return view('pages.globals', compact('data'));
    }

    public function sox_plus()
    {
        $config = config('ranking.extra.item_logs.plus');
        if ($config['enabled']) {
            $data = LogEventItem::getLogEventItem('plus', $config['plus'], $config['degree'], $config['type'], null, 25);
        } else {
            $data = collect();
        }
        return view('pages.sox-plus', compact('data'));
    }

    public function sox_drop()
    {
        $config = config('ranking.extra.item_logs.drop');
        if ($config['enabled']) {
            $data = LogEventItem::getLogEventItem('drop', null, $config['degree'], $config['type'], null, 25);
        } else {
            $data = collect();
        }
        return view('pages.sox-drop', compact('data'));
    }

    public function pvp_kills()
    {
        if (config('ranking.extra.kill_logs.pvp')) {
            $data = LogEventChar::getKillLogs('pvp', 25);
        } else {
            $data = collect();
        }
        return view('pages.pvp-kills', compact('data'));
    }

    public function job_kills()
    {
        if (config('ranking.extra.kill_logs.job')) {
            $data = LogEventChar::getKillLogs('job', 25);
        } else {
            $data = collect();
        }
        return view('pages.job-kills', compact('data'));
    }

    public function gateway(Request $request)
    {
        $user = $request->user();
        $data = WebItemCertifyKey::getCertifyKey($user->tbUser->JID);
        $config = config('global.server');
        $key = strtoupper(md5($data->UserJID.$data->Certifykey.$config['saltKey']));
        $data = "{$config['WebMallAddr']}?jid={$data->UserJID}&key={$key}&loc=us";

        return view('pages.gateway', [
            'data' => $data,
        ]);
    }
}
