<?php

namespace App\Models\SRO\Log;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogInstanceWorldInfo extends Model
{
    use HasFactory;

    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = 'log';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dbo._LogInstanceWorldInfo';

    public static function getUniqueRanking($limit = 25, $month = 0)
    {
        $uniqueList = config('ranking.uniques');

        $case = 'SUM(CASE ';
        foreach ($uniqueList as $uniqueCode => $points) {
            $points = $points['points'];
            $case .= "WHEN _LogInstanceWorldInfo.Value = '$uniqueCode' THEN $points ";
        }
        $case .= 'ELSE 0 END) AS Points';
        $startOfMonth = Carbon::now()->startOfMonth();

        return Cache::remember("ranking_unique_{$limit}_{$month}", config('global.cache.ranking_unique', 600), function () use ($month, $startOfMonth, $uniqueList, $case, $limit) {
            return self::select(
                    '_Char.CharName16',
                    '_Char.RefObjID',
                    '_Char.CurLevel',
                    '_Guild.ID',
                    '_Guild.Name',
                    DB::raw($case)
                )
                ->join(DB::connection('shard')->getDatabaseName().'.dbo._Char', '_Char.CharID', '=', '_LogInstanceWorldInfo.CharID')
                ->join(DB::connection('shard')->getDatabaseName().'.dbo._Guild', '_Char.GuildID', '=', '_Guild.ID')
                ->whereIn('_LogInstanceWorldInfo.Value', array_keys($uniqueList))
                ->where('_LogInstanceWorldInfo.ValueCodeName128', 'KILL_UNIQUE_MONSTER')
                ->when($month == 1, function ($query) use ($startOfMonth) {
                    $query->where('_LogInstanceWorldInfo.EventTime', '>=', $startOfMonth);
                })
                ->groupBy(
                    '_Char.CharName16',
                    '_Char.RefObjID',
                    '_Char.CurLevel',
                    '_Guild.ID',
                    '_Guild.Name'
                )
                ->orderByDesc('Points')
                ->limit($limit)
                ->get();
        });
    }

	public static function getUniquesKill($limit = 25, $CharID = 0, $includeSpawns = true)
	{
		$uniqueList = array_keys(config('ranking.uniques'));

		return Cache::remember("unique_history_{$limit}_{$CharID}_{$includeSpawns}", config('global.cache.unique_history', 600), function () use ($CharID, $limit, $uniqueList, $includeSpawns) {
			return self::select([
					'_LogInstanceWorldInfo.CharID',
					'_Char.CharName16',
					'_Char.RefObjID',
					'_Char.CurLevel',
					'_LogInstanceWorldInfo.ValueCodeName128',
					'_LogInstanceWorldInfo.Value',
					'_LogInstanceWorldInfo.WorldID',
					'_RefRegion.wRegionID',
					'_RefRegion.AreaName',
					'_LogInstanceWorldInfo.EventTime'
				])
				->leftJoin(DB::connection('shard')->getDatabaseName().'.dbo._Char', '_Char.CharID', '=', '_LogInstanceWorldInfo.CharID')
				->leftJoin(DB::connection('shard')->getDatabaseName().'.dbo._RefRegion', '_RefRegion.wRegionID', '=', '_LogInstanceWorldInfo.WorldID')
				->whereIn('_LogInstanceWorldInfo.Value', $uniqueList)
				->when($includeSpawns, function ($query) {
					$query->whereIn('_LogInstanceWorldInfo.ValueCodeName128', ['KILL_UNIQUE_MONSTER', 'SPAWN_UNIQUE_MONSTER']);
				}, function ($query) {
					$query->where('_LogInstanceWorldInfo.ValueCodeName128', 'KILL_UNIQUE_MONSTER');
				})
				->when($CharID > 0, function ($query) use ($CharID) {
					$query->where('_LogInstanceWorldInfo.CharID', $CharID);
				})
				->orderByDesc('_LogInstanceWorldInfo.EventTime')
				->limit($limit)
				->get();
		});
	}

    public static function getUniquesAdvanced(int $limit = 5)
    {
        $config = config('ranking.uniques');

        return Cache::remember("uniques_advanced_top{$limit}", config('global.cache.unique_history', 600), function () use ($config, $limit) {
            $result = collect();

            foreach ($config as $value => $cfg) {
                $kills = self::select([
                    '_LogInstanceWorldInfo.CharID',
                    '_Char.CharName16',
                ])
                    ->join(DB::connection('shard')->getDatabaseName().'.dbo._Char', '_Char.CharID', '=', '_LogInstanceWorldInfo.CharID')
                    ->where('_LogInstanceWorldInfo.Value', $value)
                    ->where('_LogInstanceWorldInfo.ValueCodeName128', 'KILL_UNIQUE_MONSTER')
                    ->orderByDesc('_LogInstanceWorldInfo.EventTime')
                    ->limit(9999)
                    ->get();

                $topPlayers = $kills
                    ->groupBy(fn($kill) => strtolower($kill->CharName16))
                    ->map(fn($playerKills) => (object)[
                        'CharName16' => $playerKills->first()->CharName16 ?: 'Unknown',
                        'Points' => ($cfg['points'] ?? 0) * $playerKills->count(),
                    ])
                    ->sortByDesc('Points')
                    ->take($limit)
                    ->values();

                $result[$value] = $topPlayers;
            }

            return $result;
        });
    }
}
