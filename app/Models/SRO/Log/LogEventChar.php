<?php

namespace App\Models\SRO\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogEventChar extends Model
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
    protected $table = 'dbo._LogEventChar';

    public static function getKillLogs($type = 'pvp', $limit = 100)
    {
        return Cache::remember("kill_logs_{$type}_{$limit}", 3600, function () use ($type, $limit) {
            $logTable = DB::connection('log')->getDatabaseName() . '.dbo._LogEventChar';

            $rawSub = DB::raw("(
                SELECT
                    lec.EventTime,
                    lec.CharID AS DeadCharID,
                    lec.strDesc,
                    CASE
                        WHEN CHARINDEX('] vs [Monster: ', lec.strDesc) > 0 THEN 'monster'
                        WHEN CHARINDEX('My: no job, Neutral,', lec.strDesc) > 0 AND CHARINDEX('): no job, Neutral, ', lec.strDesc) > 0 THEN 'pvp'
                        WHEN CHARINDEX(', Neutral, no freebattle team', lec.strDesc) > 0 AND CHARINDEX(', Neutral, no freebattle team', lec.strDesc, CHARINDEX(', Neutral, no freebattle team', lec.strDesc) + 1) > 0 THEN 'job'
                        ELSE NULL
                    END AS Type,
                    CASE
                        WHEN CHARINDEX('(', lec.strDesc) > 0 AND CHARINDEX(')', lec.strDesc) > CHARINDEX('(', lec.strDesc)
                            THEN SUBSTRING(
                                lec.strDesc,
                                CHARINDEX('(', lec.strDesc) + 1,
                                CHARINDEX(')', lec.strDesc) - CHARINDEX('(', lec.strDesc) - 1
                            )
                        ELSE NULL
                    END AS KillerCharName
                FROM {$logTable} lec
                WHERE lec.EventID = 20
                AND (
                    CHARINDEX('] vs [Monster: ', lec.strDesc) > 0
                    OR (CHARINDEX('My: no job, Neutral,', lec.strDesc) > 0 AND CHARINDEX('): no job, Neutral, ', lec.strDesc) > 0)
                    OR (CHARINDEX(', Neutral, no freebattle team', lec.strDesc) > 0 AND CHARINDEX(', Neutral, no freebattle team', lec.strDesc, CHARINDEX(', Neutral, no freebattle team', lec.strDesc) + 1) > 0)
                )
            ) AS LogEventChar");

            $query = DB::table($rawSub)
                ->leftJoin(DB::raw(DB::connection('shard')->getDatabaseName().'.[dbo].[_Char] AS k'), DB::raw('k.CharName16 COLLATE Korean_Wansung_CI_AS'), '=', DB::raw('LogEventChar.KillerCharName COLLATE Korean_Wansung_CI_AS'))
                ->leftJoin(DB::raw(DB::connection('shard')->getDatabaseName().'.[dbo].[_Char] AS c'), 'c.CharID', '=', 'LogEventChar.DeadCharID')
                ->select([
                    'LogEventChar.DeadCharID',
                    'c.CharName16 AS DeadCharName',
                    'k.CharID AS KillerCharID',
                    'LogEventChar.KillerCharName',
                    'LogEventChar.Type',
                    'LogEventChar.EventTime'
                ])
                ->orderBy('LogEventChar.EventTime', 'desc');

            if ($type !== null) {
                $query->where('LogEventChar.Type', $type);
            }

            if ($limit !== null) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }

    public static function getKillDeathRanking($type = null, $limit = 25, $charID = null)
    {
        $cacheKey = 'log_event_kill_death_ranking_' . ($type ?? 'all') . '_limit_' . $limit . '_char_' . ($charID ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($type, $limit, $charID) {
            $kills = DB::connection('log')->table('_LogEventChar')
                ->selectRaw("
                SUBSTRING(strDesc, CHARINDEX('(', strDesc) + 1, CHARINDEX(')', strDesc) - CHARINDEX('(', strDesc) - 1) AS CharName,
                COUNT(*) AS KillCount
            ")
                ->where('EventID', 20)
                ->whereRaw("CHARINDEX('(', strDesc) > 0 AND CHARINDEX(')', strDesc) > CHARINDEX('(', strDesc)");

            if ($charID) {
                $kills->where('CharID', $charID);
            }

            if ($type === 'pvp') {
                $kills->whereRaw("
                CHARINDEX('My: no job, Neutral,', strDesc) > 0 AND
                CHARINDEX('): no job, Neutral, ', strDesc) > 0
            ");
            } elseif ($type === 'job') {
                $kills->whereRaw("
                CHARINDEX(', Neutral, no freebattle team', strDesc) > 0 AND
                CHARINDEX(', Neutral, no freebattle team', strDesc, CHARINDEX(', Neutral, no freebattle team', strDesc) + 1) > 0
            ");
            } elseif ($type === 'monster') {
                $kills->whereRaw("CHARINDEX('] vs [Monster: ', strDesc) > 0");
            }

            $kills->groupBy(DB::raw("SUBSTRING(strDesc, CHARINDEX('(', strDesc) + 1, CHARINDEX(')', strDesc) - CHARINDEX('(', strDesc) - 1)"));

            $deaths = DB::connection('log')->table('_LogEventChar')
                ->join(DB::raw(DB::connection('shard')->getDatabaseName().".dbo._Char"), '_Char.CharID', '=', '_LogEventChar.CharID')
                ->selectRaw('_Char.CharName16 AS CharName, COUNT(*) AS DeathCount')
                ->where('_LogEventChar.EventID', 20);

            if ($charID) {
                $deaths->where('_LogEventChar.CharID', $charID);
            }

            if ($type === 'pvp') {
                $deaths->whereRaw("
                CHARINDEX('My: no job, Neutral,', strDesc) > 0 AND
                CHARINDEX('): no job, Neutral, ', strDesc) > 0
            ");
            } elseif ($type === 'job') {
                $deaths->whereRaw("
                CHARINDEX(', Neutral, no freebattle team', strDesc) > 0 AND
                CHARINDEX(', Neutral, no freebattle team', strDesc, CHARINDEX(', Neutral, no freebattle team', strDesc) + 1) > 0
            ");
            } elseif ($type === 'monster') {
                $deaths->whereRaw("CHARINDEX('] vs [Monster: ', strDesc) > 0");
            }

            $deaths->groupBy('_Char.CharName16');

            return DB::connection('log')->table(DB::raw("({$kills->toSql()}) as k"))
                ->mergeBindings($kills)
                ->leftJoinSub($deaths, 'd', 'k.CharName', '=', DB::raw('d.CharName COLLATE Latin1_General_CI_AS'))
                ->select(
                    'k.CharName',
                    'k.KillCount',
                    DB::raw('ISNULL(d.DeathCount, 0) as DeathCount')
                )
                ->orderByDesc('k.KillCount')
                ->limit($limit)
                ->get();
        });
    }

    public static function getCharStatus($charID)
    {
        return Cache::remember('char_status_' . $charID, config('global.cache.character_info', 60), function () use ($charID) {
            return self::select('EventID', 'EventTime')
                ->where('CharID', $charID)
                ->whereIn('EventID', [4, 6])
                ->orderBy('EventTime', 'desc')
                ->get();
        });
    }
}
