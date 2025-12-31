<?php

namespace App\Http\Controllers;

use App\Models\SRO\Shard\TimedJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SRO\Log\LogChatMessage;
use App\Models\SRO\Log\LogEventChar;
use App\Models\SRO\Log\LogInstanceWorldInfo;
use App\Models\SRO\Shard\Char;
use App\Models\SRO\Shard\CharSkillMastery;
use App\Models\SRO\Shard\CharTradeConflictJob;
use App\Models\SRO\Shard\CharTrijob;
use App\Models\SRO\Shard\Guild;
use App\Models\SRO\Shard\GuildMember;
use App\Models\SRO\Shard\TrainingCampHonorRank;
use App\Models\SRO\Shard\User;
use App\Services\CrestService;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:player,guild',
            'search' => 'nullable|string|max:255',
        ]);
        if ($request->input('type') == 'player' && $request->filled('search')) {
            $data = Char::getPlayerRanking(25, 0, $request->search);
        }elseif ($request->input('type') == 'guild' && $request->filled('search')) {
            $data = Guild::getGuildRanking(25, 0, $request->search);
        }else {
            $data = Char::getPlayerRanking();
        }
        $config = array_merge(config('ranking.menu', []), config('ranking.custom', []));
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.index', [
            'data' => $data,
            'config' => $config,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
            'type' => $request->input('type'),
        ]);
    }

    public function player()
    {
        $data = Char::getPlayerRanking();
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.player', [
            'data' => $data,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
        ]);
    }

    public function guild()
    {
        $data = Guild::getGuildRanking();
        $topImage = config('ranking.top_image');

        return view('ranking.ranking.guild', [
            'data' => $data,
            'topImage' => $topImage,
        ]);
    }

    public function unique()
    {
        $data = LogInstanceWorldInfo::getUniqueRanking();
        $uniqueList = config('ranking.uniques');
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.unique', [
            'data' => $data,
            'uniqueList' => $uniqueList,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
        ]);
    }

    public function unique_monthly()
    {
        $data = LogInstanceWorldInfo::getUniqueRanking(25, 1);
        $uniqueList = config('ranking.uniques');
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.unique-monthly', [
            'data' => $data,
            'uniqueList' => $uniqueList,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
        ]);
    }

    public function fortress_player()
    {
        $data = GuildMember::getFortressPlayerRanking();
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.fortress-player', [
            'data' => $data,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
        ]);
    }

    public function fortress_guild()
    {
        $data = Guild::getFortressGuildRanking();
        $topImage = config('ranking.top_image');

        return view('ranking.ranking.fortress-guild', [
            'data' => $data,
            'topImage' => $topImage,
        ]);
    }

    public function honor()
    {
        $data = TrainingCampHonorRank::getHonorRanking();
        $honorLevel = config('ranking.honor_level');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.honor', [
            'data' => $data,
            'honorLevel' => $honorLevel,
            'characterRace' => $characterRace,
        ]);
    }

    public function job()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking();
        } else {
            $data = CharTradeConflictJob::getJobRanking();
        }
        $config = config('ranking.job_menu');
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');
        $jobType = config('ranking.job_type');

        return view('ranking.ranking.job', [
            'data' => $data,
            'config' => $config,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
            'jobType' => $jobType,
        ]);
    }

    public function job_all()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking();
            $jobType = config('ranking.job_type_vsro');
        } else {
            $data = CharTradeConflictJob::getJobRanking();
            $jobType = config('ranking.job_type');
        }
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.job-all', [
            'data' => $data,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
            'jobType' => $jobType,
        ]);
    }

    public function job_hunter()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 3);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 1);
        }
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.job-hunter', [
            'data' => $data,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
        ]);
    }

    public function job_thieve()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 2);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 2);
        }
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.job-thieve', [
            'data' => $data,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
        ]);
    }

    public function job_trader()
    {
        if (config('global.server.version') === 'vSRO') {
            $data = CharTrijob::getJobRanking(25, 1);
        } else {
            $data = CharTradeConflictJob::getJobRanking(25, 3);
        }
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.job-trader', [
            'data' => $data,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
        ]);
    }

    public function pvp_kd()
    {
        if (config('ranking.extra.kill_logs.pvp')) {
            $data = LogEventChar::getKillDeathRanking('pvp', 25);
        } else {
            $data = [];
        }
        $topImage = config('ranking.top_image');

        return view('ranking.ranking.pvp-kd', [
            'data' => $data,
            'topImage' => $topImage,
        ]);
    }

    public function job_kd()
    {
        if (config('ranking.extra.kill_logs.job')) {
            $data = LogEventChar::getKillDeathRanking('job', 25);
        } else {
            $data = [];
        }
        $topImage = config('ranking.top_image');

        return view('ranking.ranking.job-kd', [
            'data' => $data,
            'topImage' => $topImage,
        ]);
    }

    public function custom(string $type)
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

        $config = config('ranking.menu');
        $topImage = config('ranking.top_image');
        $characterRace = config('ranking.character_race');

        return view('ranking.ranking.custom', [
            'data' => $data,
            'config' => $config,
            'topImage' => $topImage,
            'characterRace' => $characterRace,
            'type' => $type,
        ]);
    }

    public function character_view($name, InventoryService $inventoryService)
    {
        $charID = Char::getCharIDByName($name);
        if ($charID > 0) {

            $uniqueList = config('ranking.uniques');
            $skillMastery = config('ranking.skill_mastery');
            $characterRace = config('ranking.character_race');
            $hwanLevel = config('ranking.hwan_level');

            if (config('global.server.version') === 'vSRO') {
                $characterImage = config('ranking.character_image_vsro');
                $jobType = config('ranking.job_type_vsro');
            }else {
                $characterImage = config('ranking.character_image');
                $jobType = config('ranking.job_type');
            }

            $data = Char::getPlayerRanking(1, $charID)->first();
            $build = CharSkillMastery::getCharBuildInfo($charID);
            $buff = TimedJob::getCharBuffInfo($charID);

            $inventorySet = $inventoryService->getInventorySet($charID, 12, 0, 8);
            $inventoryAvatar = $inventoryService->getInventoryAvatar($charID);

            if (config('global.server.version') !== 'vSRO') {
                $inventoryJob = $inventoryService->getInventoryJob($charID) ?? [];
            }

            if (config('widgets.unique_history.enabled')) {
                $uniqueHistory = LogInstanceWorldInfo::getUniquesKill(5, $charID);
            }
            if (config('widgets.globals_history.enabled')) {
                $globalsHistory = LogChatMessage::getGlobalsHistory(5, $name);
            }

            if (config('ranking.extra.kill_logs.pvp')) {
                $pvpKill = LogEventChar::getKillDeathRanking('pvp', 1, $charID)->first();
            }
            if (config('ranking.extra.kill_logs.job')) {
                $jobKill = LogEventChar::getKillDeathRanking('job', 1, $charID)->first();
            }
            if (config('ranking.extra.character_status')) {
                $status = LogEventChar::getCharStatus($charID)->first();
            }

            if ($data) {
                return view('ranking.character.index', [
                    'data' => $data,
                    'build' => $build,
                    'buff' => $buff,
                    'inventorySet' => $inventorySet,
                    'inventoryAvatar' => $inventoryAvatar,
                    'inventoryJob' => $inventoryJob ?? null,
                    'uniqueList' => $uniqueList,
                    'characterImage' => $characterImage,
                    'skillMastery' => $skillMastery,
                    'jobType' => $jobType,
                    'characterRace' => $characterRace,
                    'hwanLevel' => $hwanLevel,
                    'uniqueHistory' => $uniqueHistory ?? [],
                    'globalsHistory' => $globalsHistory ?? [],
                    'status' => $status ?? null,
                    'pvpKill' => $pvpKill ?? null,
                    'jobKill' => $jobKill ?? null,
                ]);
            }
        }
        return redirect()->back();
    }

    public function guild_view($name)
    {
        $guildID = Guild::getGuildIDByName($name);
        if ($guildID > 0) {

            $data = Guild::getGuildRanking(1, $guildID)->first();
            $members = GuildMember::getGuildInfoMembers($guildID);
            $alliances = Guild::getGuildInfoAlliance($guildID);

            $characterRace = config('ranking.character_race');
            $guildAuthority = config('ranking.guild_authority');

            if ($data) {
                return view('ranking.guild.index', [
                    'data' => $data,
                    'members' => $members,
                    'alliances' => $alliances,
                    'characterRace' => $characterRace,
                    'guildAuthority' => $guildAuthority,
                ]);
            }
        }

        return redirect()->back();
    }

    public function guild_crest($hex)
    {
        if (!preg_match('/^[a-fA-F0-9]+$/', $hex)) {
            abort(400, 'Invalid crest data.');
        }

        $img = CrestService::generateGuildCrest($hex);

        return response()->stream(function () use ($img) {
            header('Content-Type: image/png');
            imagepng($img);
            imagedestroy($img);
        });
    }
}
