<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SRO\Log\LogEventChar;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Models\SRO\Shard\Char;
use App\Models\SRO\Shard\CharTradeConflictJob;
use App\Models\SRO\Shard\CharTrijob;
use App\Models\SRO\Shard\Guild;
use App\Models\SRO\Shard\GuildMember;
use App\Models\SRO\Shard\TrainingCampHonorRank;
use App\Services\CrestService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|in:player,guild',
            'search' => 'nullable|string|max:255',
        ]);

        $type = $validated['type'] ?? 'player';
        $search = $validated['search'] ?? null;

        $data = Char::getPlayerRanking(25, 0, $search);

        if ($type === 'guild') {
            $data = Guild::getGuildRanking(25, 0, $search);
        }

        $config = (object) [
            'menu' => collect(config('ranking.menu'))->merge(config('ranking.custom'))->map(fn ($item) => (object) $item)->values(),
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.index', [
            'data' => $data,
            'config' => $config,
            'type' => $type,
        ]);
    }

    public function playerRanking()
    {
        $data = Char::getPlayerRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.player', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function guildRanking()
    {
        $data = Guild::getGuildRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
        ];

        return view('ranking.ranking.guild', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function uniqueRanking()
    {
        $data = LogInstanceWorldInfo::getUniqueRanking();

        $config = (object) [
            'uniqueList' => config('ranking.uniques'),
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.unique', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function uniqueMonthlyRanking()
    {
        $data = LogInstanceWorldInfo::getUniqueRanking(25, 1);

        $config = (object) [
            'uniqueList' => config('ranking.uniques'),
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.unique-monthly', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function fortressPlayerRanking()
    {
        $data = GuildMember::getFortressPlayerRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.fortress-player', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function fortressGuildRanking()
    {
        $data = Guild::getFortressGuildRanking();

        $config = (object) [
            'topImage' => config('ranking.top_image'),
        ];

        return view('ranking.ranking.fortress-guild', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function honorRanking()
    {
        $data = TrainingCampHonorRank::getHonorRanking();

        $config = (object) [
            'honorLevel' => config('ranking.honor_level'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.honor', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function jobRanking()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking();
        } else {
            $data = CharTradeConflictJob::getJobRanking();
        }

        $config = (object) [
            'menu' => collect(config('ranking.job_menu'))->map(fn($item) => (object)$item)->values(),
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
            'jobType' => config('ranking.job_type'),
        ];

        return view('ranking.ranking.job', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function jobAllRanking()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking();
        } else {
            $data = CharTradeConflictJob::getJobRanking();
        }

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
            'jobType' => config('ranking.job_type'),
            'jobTypeVSRO' => config('ranking.job_type_vsro'),
        ];

        return view('ranking.ranking.job-all', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function jobHunterRanking()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 3);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 1);
        }

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.job-hunter', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function jobThieveRanking()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 2);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 2);
        }

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.job-thieve', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function jobTraderRanking()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 1);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 3);
        }

        $config = (object) [
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.job-trader', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function pvpKDRanking()
    {
        if (config('ranking.extra.kill_logs.pvp')) {
            $data = LogEventChar::getKillDeathRanking('pvp', 25);
        } else {
            $data = [];
        }

        $config = (object) [
            'topImage' => config('ranking.top_image'),
        ];

        return view('ranking.ranking.pvp-kd', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function jobKDRanking()
    {
        if (config('ranking.extra.kill_logs.job')) {
            $data = LogEventChar::getKillDeathRanking('job', 25);
        } else {
            $data = [];
        }

        $config = (object) [
            'topImage' => config('ranking.top_image'),
        ];

        return view('ranking.ranking.job-kd', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function customRanking(string $type)
    {
        $ranking = config("ranking.custom.$type");
        if (!$ranking || empty($ranking['enabled'])) {
            abort(404, "Ranking type [$type] not found or disabled.");
        }

        $query = $ranking['query'];
        $data = Cache::remember("{$type}", 600, function () use ($query) {
            return collect(
                DB::connection('shard')->select($query)
            );
        });

        $config = (object) [
            'menu' => config('ranking.menu'),
            'topImage' => config('ranking.top_image'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.ranking.custom', [
            'data' => $data,
            'config' => $config,
            'type' => $type,
        ]);
    }

    public function characterView($name)
    {
        $data = Char::getCharByName($name);

        $config = (object) [
            'uniqueList' => config('ranking.uniques'),
            'skillMastery' => config('ranking.skill_mastery'),
            'characterRace' => config('ranking.character_race'),
            'hwanLevel' => config('ranking.hwan_level'),
            'characterImage' => config('ranking.character_image'),
            'characterImageVSRO' => config('ranking.character_image_vsro'),
            'jobType' => config('ranking.job_type'),
            'jobTypeVSRO' => config('ranking.job_type_vsro'),
        ];

        return view('ranking.character.index', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function guildView($name)
    {
        $data = Guild::getGuildByName($name);

        $config = (object) [
            'guildAuthority' => config('ranking.guild_authority'),
            'characterRace' => config('ranking.character_race'),
        ];

        return view('ranking.guild.index', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function guildCrest(string $bin)
    {
        abort_if(!ctype_xdigit($bin), 404, 'Invalid crest data.');

        $image = CrestService::generateGuildCrest($bin);

        return response()->stream(
            function () use ($image) {
                imagepng($image);
                imagedestroy($image);
            },
            200,
            ['Content-Type' => 'image/png']
        );
    }
}
