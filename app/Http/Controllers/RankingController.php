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
            'search' => ['nullable', 'string', 'max:64', 'regex:/^[\[\]a-zA-Z0-9_ ]{1,64}$/'],
        ]);

        $type = $validated['type'] ?? 'player';
        $search = $validated['search'] ?? '';

        $data = Char::getPlayerRanking(25, 0, $search);

        if ($type === 'guild') {
            $data = Guild::getGuildRanking(25, 0, $search);
        }

        $config = (object) collect(config('ranking.menu'))->merge(config('ranking.custom'))->map(fn ($item) => (object) $item)->values();

        return view('ranking.index', [
            'data' => $data,
            'config' => $config,
            'type' => $type,
        ]);
    }

    public function playerRanking()
    {
        abort_if(!config('ranking.menu.ranking_player.enabled', false), 404);
        $data = Char::getPlayerRanking();

        return view('ranking.ranking.player', compact('data'));
    }

    public function guildRanking()
    {
        abort_if(!config('ranking.menu.ranking_guild.enabled', false), 404);
        $data = Guild::getGuildRanking();

        return view('ranking.ranking.guild', compact('data'));
    }

    public function uniqueRanking()
    {
        abort_if(!config('ranking.menu.ranking_unique.enabled', false), 404);
        $data = LogInstanceWorldInfo::getUniqueRanking();

        return view('ranking.ranking.unique', compact('data'));
    }

    public function uniqueMonthlyRanking()
    {
        abort_if(!config('ranking.menu.ranking_unique_monthly.enabled', false), 404);
        $data = LogInstanceWorldInfo::getUniqueRanking(25, 1);

        return view('ranking.ranking.unique-monthly', compact('data'));
    }

    public function fortressPlayerRanking()
    {
        abort_if(!config('ranking.menu.ranking_fortress_player.enabled', false), 404);
        $data = GuildMember::getFortressPlayerRanking();

        return view('ranking.ranking.fortress-player', compact('data'));
    }

    public function fortressGuildRanking()
    {
        abort_if(!config('ranking.menu.ranking_fortress_guild.enabled', false), 404);
        $data = Guild::getFortressGuildRanking();

        return view('ranking.ranking.fortress-guild', compact('data'));
    }

    public function honorRanking()
    {
        abort_if(!config('ranking.menu.ranking_honor.enabled', false), 404);
        $data = TrainingCampHonorRank::getHonorRanking();

        return view('ranking.ranking.honor', compact('data'));
    }

    public function jobRanking()
    {
        abort_if(!config('ranking.menu.ranking_job.enabled', false), 404);
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking();
        } else {
            $data = CharTradeConflictJob::getJobRanking();
        }

        $config = (object) collect(config('ranking.job_menu'))->map(fn($item) => (object)$item)->values();

        return view('ranking.ranking.job', [
            'data' => $data,
            'config' => $config,
        ]);
    }

    public function jobAllRanking()
    {
        abort_if(!config('ranking.job_menu.ranking_job_all.enabled', false), 404);
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking();
        } else {
            $data = CharTradeConflictJob::getJobRanking();
        }

        return view('ranking.ranking.job-all', compact('data'));
    }

    public function jobHunterRanking()
    {
        abort_if(!config('ranking.job_menu.ranking_job_hunters.enabled', false), 404);
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 3);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 1);
        }

        return view('ranking.ranking.job-hunter', compact('data'));
    }

    public function jobThieveRanking()
    {
        abort_if(!config('ranking.job_menu.ranking_job_thieves.enabled', false), 404);
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 2);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 2);
        }

        return view('ranking.ranking.job-thieve', compact('data'));
    }

    public function jobTraderRanking()
    {
        abort_if(!config('ranking.job_menu.ranking_job_traders.enabled', false), 404);
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 1);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 3);
        }

        return view('ranking.ranking.job-trader', compact('data'));
    }

    public function pvpKDRanking()
    {
        abort_if(!config('ranking.menu.ranking_pvp_kd.enabled', false), 404);
        $data = LogEventChar::getKillDeathRanking('pvp', 25);

        return view('ranking.ranking.pvp-kd', compact('data'));
    }

    public function jobKDRanking()
    {
        abort_if(!config('ranking.menu.ranking_job_kd.enabled', false), 404);
        $data = LogEventChar::getKillDeathRanking('job', 25);

        return view('ranking.ranking.job-kd', compact('data'));
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

        return view('ranking.ranking.custom', [
            'data' => $data,
        ]);
    }

    public function characterView($name)
    {
        $data = Char::getCharByName($name);

        return view('ranking.character.index', compact('data'));
    }

    public function guildView($name)
    {
        $data = Guild::getGuildByName($name);

        return view('ranking.guild.index', compact('data'));
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
