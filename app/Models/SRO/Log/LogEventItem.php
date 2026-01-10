<?php

namespace App\Models\SRO\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LogEventItem extends Model
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
    protected $table = 'dbo._LogEventItem';

    public static function getLogEventItem($mode = 'plus', $plus = null, $degree = null, $type = null, $CharID = null, $limit = null)
    {
        $cacheKey = 'log_event_items_' . $mode .
            ($plus !== null ? '_plus' . $plus : '') .
            ($degree !== null ? '_deg' . $degree : '') .
            ($type !== null ? '_type' . md5($type) : '') .
            ($CharID !== null ? '_char' . $CharID : '') .
            ($limit !== null ? '_limit' . $limit : '');

        return Cache::remember($cacheKey, 600, function () use ($mode, $plus, $degree, $type, $CharID, $limit) {
            if (config('global.server.version') === 'vSRO') {
                $itemNameDescTable = DB::connection('account')->getDatabaseName() . '.dbo._ItemNameDesc';
                $itemNameDescField = '_ItemNameDesc.RealName';
                $itemNameDescJoinOn = '_ItemNameDesc.NameStrID';
            } else {
                $itemNameDescTable = DB::connection('account')->getDatabaseName() . '.dbo._Rigid_ItemNameDesc';
                $itemNameDescField = '_Rigid_ItemNameDesc.ENG';
                $itemNameDescJoinOn = '_Rigid_ItemNameDesc.StrID';
            }

            $logTable = DB::connection('log')->getDatabaseName() . '.dbo._LogEventItem';
            $query = DB::connection('log')->table(DB::raw("
                {$logTable}
                OUTER APPLY (
                    SELECT
                        CASE
                            WHEN CHARINDEX('[Opt: +', _LogEventItem.strDesc) > 0
                                 AND CHARINDEX(']', _LogEventItem.strDesc, CHARINDEX('[Opt: +', _LogEventItem.strDesc)) > 0
                            THEN
                                SUBSTRING(
                                    _LogEventItem.strDesc,
                                    CHARINDEX('[Opt: +', _LogEventItem.strDesc) + LEN('[Opt: +'),
                                    CHARINDEX(']', _LogEventItem.strDesc, CHARINDEX('[Opt: +', _LogEventItem.strDesc)) - (CHARINDEX('[Opt: +', _LogEventItem.strDesc) + LEN('[Opt: +'))
                                )
                            ELSE NULL
                        END AS PlusValue
                ) AS v
                OUTER APPLY (
                    SELECT
                        CASE
                            WHEN CHARINDEX('MOB_', _LogEventItem.strDesc) > 0
                                 AND CHARINDEX(',', _LogEventItem.strDesc + ',', CHARINDEX('MOB_', _LogEventItem.strDesc)) > CHARINDEX('MOB_', _LogEventItem.strDesc)
                            THEN
                                SUBSTRING(
                                    _LogEventItem.strDesc,
                                    CHARINDEX('MOB_', _LogEventItem.strDesc),
                                    CHARINDEX(',', _LogEventItem.strDesc + ',', CHARINDEX('MOB_', _LogEventItem.strDesc)) - CHARINDEX('MOB_', _LogEventItem.strDesc)
                                )
                            ELSE NULL
                        END AS MobCode
                ) AS m
            "))
                ->selectRaw("
                _LogEventItem.CharID,
                _LogEventItem.ItemRefID,
                _LogEventItem.Serial64,
                _LogEventItem.EventTime,
                _Char.CharName16,
                CEILING(_RefObjItem.ItemClass / 3.0) as Degree,
                REPLACE(REPLACE(_RefObjCommon.AssocFileIcon128, '\\', '/'), '.ddj', '') as AssocFileIcon128,
                {$itemNameDescField},
                v.PlusValue,
                m.MobCode,
                CASE
                    WHEN _RefObjItem.ItemClass > 30 AND
                         (_RefObjCommon.CodeName128 LIKE '%A_RARE%' OR
                          _RefObjCommon.CodeName128 LIKE '%SET_A_RARE%' OR
                          _RefObjCommon.CodeName128 LIKE '%SET_B_RARE%')
                        THEN 'Seal of Nova'
                    WHEN _RefObjCommon.CodeName128 LIKE '%A_RARE%' THEN 'Seal of Star'
                    WHEN _RefObjCommon.CodeName128 LIKE '%B_RARE%' THEN 'Seal of Moon'
                    WHEN _RefObjCommon.CodeName128 LIKE '%C_RARE%' THEN 'Seal of Sun'
                    WHEN _RefObjCommon.CodeName128 LIKE 'ITEM_ROC%' THEN 'Seal of roc'
                    ELSE 'Normal'
                END as Type
            ")
                ->leftJoin(DB::connection('shard')->getDatabaseName().'.dbo._Char', '_Char.CharID', '=', '_LogEventItem.CharID')
                ->leftJoin(DB::connection('shard')->getDatabaseName().'.dbo._RefObjCommon', '_LogEventItem.ItemRefID', '=', '_RefObjCommon.ID')
                ->leftJoin(DB::connection('shard')->getDatabaseName().'.dbo._RefObjItem', '_RefObjCommon.Link', '=', '_RefObjItem.ID')
                ->leftJoin($itemNameDescTable, $itemNameDescJoinOn, '=', '_RefObjCommon.NameStrID128');

            if ($mode === 'plus') {
                $query->whereRaw("ISNUMERIC(v.PlusValue) = 1");
                if ($plus !== null) {
                    $query->whereRaw("CAST(v.PlusValue AS INT) >= ?", [$plus]);
                }
            } elseif ($mode === 'drop') {
                $query->whereRaw("m.MobCode IS NOT NULL");
            }

            if ($type !== null) {
                $query->whereRaw("
                    CASE
                        WHEN _RefObjItem.ItemClass > 30 AND
                             (_RefObjCommon.CodeName128 LIKE '%A_RARE%' OR
                              _RefObjCommon.CodeName128 LIKE '%SET_A_RARE%' OR
                              _RefObjCommon.CodeName128 LIKE '%SET_B_RARE%')
                            THEN 'Seal of Nova'
                        WHEN _RefObjCommon.CodeName128 LIKE '%A_RARE%' THEN 'Seal of Star'
                        WHEN _RefObjCommon.CodeName128 LIKE '%B_RARE%' THEN 'Seal of Moon'
                        WHEN _RefObjCommon.CodeName128 LIKE '%C_RARE%' THEN 'Seal of Sun'
                        WHEN _RefObjCommon.CodeName128 LIKE 'ITEM_ROC%' THEN 'Seal of roc'
                        ELSE 'Normal'
                    END = ?", [$type]);
            }

            if ($degree !== null) {
                $query->whereRaw('CEILING(_RefObjItem.ItemClass / 3.0) >= ?', [$degree]);
            }

            if ($CharID !== null) {
                $query->where('_LogEventItem.CharID', $CharID);
            }

            //$query->whereNotIn('_LogEventItem.dwData', [0]);
            $query->whereIn('_LogEventItem.Operation', [30, 114]);
            $query->orderBy('_LogEventItem.EventTime', 'desc');

            if ($limit !== null) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }
}
