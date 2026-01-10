<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TradeEquipInventory extends Model
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
    protected $table = 'dbo._TradeEquipInventory';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'CharID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CharID',
        'slot',
        'ItemID'
    ];

    public static function getInventoryForJob($CharID)
    {
        return Cache::remember("character_info_inventory_job_{$CharID}", config('global.cache.character_info', 86400), static function () use ($CharID) {
            return self::join('_Items', '_Items.ID64', '_TradeEquipInventory.ItemID')
            ->join('_RefObjCommon', '_Items.RefItemId', '_RefObjCommon.ID')
            ->join('_RefObjItem', '_RefObjCommon.Link', '_RefObjItem.ID')
            ->leftJoin('_BindingOptionWithItem', static function ($join) {
                $join->on('_BindingOptionWithItem.nItemDBID', '_Items.ID64');
            })
            ->where('CharID', '=', $CharID)
            ->where('Slot', '<=', 12)
            ->where('Slot', '>=', 0)
            ->where('ItemID', '!=', 0)
            ->get();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getChar()
    {
        return $this->hasMany(Char::class, 'CharID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getItem()
    {
        return $this->belongsTo(Items::class, 'ItemID', 'ID64');
    }
}
