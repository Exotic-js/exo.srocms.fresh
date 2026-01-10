<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InvCOS extends Model
{
    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = 'shard';

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
    protected $table = 'dbo._InvCOS';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'COSID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'COSID',
        'slot',
        'ItemID'
    ];

    public static function getPetNames($CharID)
    {
        return Cache::remember("character_info_pet_names_{$CharID}", config('global.cache.character_info', 86400), static function () use ($CharID) {
            return self::join('_Items', '_Items.ID64', '=', '_InvCOS.ItemID')
                ->join('_RefObjCommon', '_RefObjCommon.ID', '=', '_Items.RefItemID')
                ->join('_CharCOS', '_CharCOS.ID', '=', '_InvCOS.COSID')
                ->join('_Items AS char_item', function ($join) {
                    $join->on('char_item.Data', '=', '_CharCOS.ID')
                        ->whereIn('char_item.RefItemID', function ($q) {
                            $q->select('ID')
                                ->from('_RefObjCommon')
                                ->where('Service', 1)
                                ->where('TypeID1', 3)
                                ->where('TypeID2', 2)
                                ->where('TypeID3', 1)
                                ->where('TypeID4', 2);
                        });
                })
                ->join('_RefObjCommon AS char_common', 'char_common.ID', '=', 'char_item.RefItemID')
                ->join('_Inventory', '_Inventory.ItemID', '=', DB::raw('ISNULL(char_item.ID64, 0)'))
                ->join('_Char', '_Char.CharID', '=', '_Inventory.CharID')
                ->select([
                    '_CharCOS.ID',
                    '_CharCOS.CharName',
                ])
                ->where('_Char.CharID', $CharID)
                ->distinct()
                ->get();
        });
    }

    public static function getPetItems($CharID, $PetID, $max, $min)
    {
        return Cache::remember("character_info_pet_items_{$CharID}_{$PetID}_{$min}_{$max}", config('global.cache.character_info', 86400), static function () use ($CharID, $PetID, $max, $min) {
            return self::join('_Items', '_Items.ID64', '=', '_InvCOS.ItemID')
                ->join('_RefObjCommon', '_RefObjCommon.ID', '=', '_Items.RefItemID')
                ->join('_RefObjItem', '_RefObjItem.ID', '=', '_RefObjCommon.Link')
                ->join('_CharCOS', '_CharCOS.ID', '=', '_InvCOS.COSID')
                ->join('_Items AS char_item', function ($join) {
                    $join->on('char_item.Data', '=', '_CharCOS.ID')
                        ->whereIn('char_item.RefItemID', function ($q) {
                            $q->select('ID')
                                ->from('_RefObjCommon')
                                ->where('Service', 1)
                                ->where('TypeID1', 3)
                                ->where('TypeID2', 2)
                                ->where('TypeID3', 1)
                                ->where('TypeID4', 2);
                        });
                })
                ->join('_RefObjCommon AS char_common', 'char_common.ID', '=', 'char_item.RefItemID')
                ->join('_Inventory', '_Inventory.ItemID', '=', DB::raw('ISNULL(char_item.ID64, 0)'))
                ->join('_Char', '_Char.CharID', '=', '_Inventory.CharID')
                ->select([
                    '_InvCOS.ItemID',
                    '_InvCOS.Slot',
                    '_Items.*',
                    '_RefObjCommon.*',
                    '_RefObjItem.*',
                ])
                ->where('_Char.CharID', $CharID)
                ->where('_CharCOS.ID', '=', $PetID)
                ->where('_InvCOS.Slot', '<=', $max)
                ->where('_InvCOS.Slot', '>=', $min)
                ->where('_InvCOS.ItemID', '!=', 0)
                ->orderBy('_InvCOS.Slot', 'asc')
                ->get();
        });
    }
}
