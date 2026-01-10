<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\News;
use App\Models\SRO\Account\ShardCurrentUser;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Services\ScheduleService;

class PageController extends Controller
{
    public function news()
    {
        $data = News::getPosts();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function download()
    {
        $data = Download::getDownloads();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function timers(ScheduleService $scheduleService)
    {
        $data = $scheduleService->getEventSchedules();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function uniques()
    {
        $data = LogInstanceWorldInfo::getUniquesKill();

        $config = (object) [
            'uniqueList' => config('ranking.uniques'),
            'characterRace' => config('ranking.character_race'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function onlineCounter()
    {
        $onlinePlayer = ShardCurrentUser::getOnlineCounter();
        $maxPlayer = (int)config('settings.max_player', 1000);
        $fakePlayer = (int)config('settings.fake_player', 0);

        return response()->json([
            'status' => 'success',
            'onlinePlayer' => $onlinePlayer,
            'maxPlayer' => $maxPlayer,
            'fakePlayer' => $fakePlayer,
        ]);
    }
}
