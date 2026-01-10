<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Models\SRO\Shard\Char;
use App\Models\SRO\Shard\Guild;
use App\Models\SRO\Shard\GuildMember;

class RankingController extends Controller
{
    public function player()
    {
        $data = Char::getPlayerRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function guild()
    {
        $data = Guild::getGuildRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function unique()
    {
        $data = LogInstanceWorldInfo::getUniqueRanking();

        $config = (object) [
            'uniqueList' => config('ranking.uniques'),
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function level()
    {
        $data = Char::getLevelRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function fortress_player()
    {
        $data = GuildMember::getFortressPlayerRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function fortress_guild()
    {
        $data = Guild::getFortressGuildRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'config' => $config,
        ]);
    }
}
