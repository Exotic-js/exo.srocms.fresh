<?php

namespace App\Models\SRO\Shard;

use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Log\LogEventChar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Char extends Model
{
    use HasFactory;

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
    protected $table = 'dbo._Char';

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
        'Deleted',
        'RefObjID',
        'CharName16',
        'NickName16',
        'LastLogout',
        'RemainGold'
    ];

    protected $dates = [
        'LastLogout'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public static function getPlayerRanking($limit = 25, $CharID = 0, $CharName = '')
    {
        $minutes = config('global.cache.ranking_player', 60);
        $CharName = substr(preg_replace('/[^a-zA-Z0-9_]/', '', $CharName), 0, 50);

        return Cache::remember("ranking_player_{$limit}_{$CharID}_{$CharName}", now()->addMinutes($minutes), function () use ($CharName, $CharID, $limit) {
            $query = self::select(
                '_Char.CharID',
                '_Char.CharName16',
                '_Char.CurLevel',
                '_Char.RefObjID',
                '_Guild.ID',
                '_Guild.Name',
                '_Char.NickName16',
                '_Char.HwanLevel',
                '_Char.HP',
                '_Char.MP',
                '_Char.Strength',
                '_Char.Intellect',
                DB::raw("ISNULL((
                    SUM(ISNULL(_BindingOptionWithItem.nOptValue, 0)) +
                    SUM(ISNULL(_Items.OptLevel, 0)) +
                    SUM(ISNULL(_RefObjCommon.ReqLevel1, 0)) +
                    SUM(ISNULL(CASE WHEN _RefObjCommon.CodeName128 LIKE '%_A_RARE%' THEN 5 ELSE 0 END, 0)) +
                    SUM(ISNULL(CASE WHEN _RefObjCommon.CodeName128 LIKE '%_B_RARE%' THEN 10 ELSE 0 END, 0)) +
                    SUM(ISNULL(CASE WHEN _RefObjCommon.CodeName128 LIKE '%_C_RARE%' THEN 15 ELSE 0 END, 0))
                ), 0) AS ItemPoints")
            );

            if (config('global.server.version') === 'vSRO') {
                $query->addSelect('_CharTrijob.JobType', '_CharTrijob.Level')
                    ->leftJoin('_CharTrijob', '_Char.CharID', '=', '_CharTrijob.CharID');
            } else {
                $query->addSelect('_UserTradeConflictJob.JobType', '_CharTradeConflictJob.JobLevel')
                    ->join('_User', '_User.CharID', '=', '_Char.CharID')
                    ->join('_UserTradeConflictJob', '_UserTradeConflictJob.UserJID', '=', '_User.UserJID')
                    ->leftJoin('_CharTradeConflictJob', '_CharTradeConflictJob.CharID', '=', '_Char.CharID');
            }

            $query->leftJoin('_Guild', '_Char.GuildID', '=', '_Guild.ID')
                ->join('_Inventory', '_Inventory.CharID', '=', '_Char.CharID')
                ->join('_Items', '_Items.ID64', '=', '_Inventory.ItemID')
                ->join('_RefObjCommon', '_RefObjCommon.ID', '=', '_Items.RefItemID')
                ->leftJoin('_BindingOptionWithItem', function ($join) {
                    $join->on('_BindingOptionWithItem.nItemDBID', '=', '_Items.ID64')
                        ->where('_BindingOptionWithItem.nOptValue', '>', 0)
                        ->where('_BindingOptionWithItem.bOptType', '=', 2);
                })
                ->where('_Inventory.Slot', '<', 13)
                ->whereNotIn('_Inventory.Slot', [7, 8])
                ->where('_Inventory.ItemID', '>', 0)
                ->where('_Char.deleted', '=', 0)
                ->when($CharID > 0, fn($q) => $q->where('_Char.CharID', $CharID))
                ->when(!empty($CharName), fn($q) => $q->where('_Char.CharName16', 'like', "%{$CharName}%"))
                ->whereNotIn('_Char.CharName16', config('ranking.hidden.characters'));

            $groupBy = [
                '_Char.CharID',
                '_Char.CharName16',
                '_Char.CurLevel',
                '_Char.RefObjID',
                '_Guild.ID',
                '_Guild.Name',
                '_Char.NickName16',
                '_Char.HwanLevel',
                '_Char.HP',
                '_Char.MP',
                '_Char.Strength',
                '_Char.Intellect',
            ];

            if (config('global.server.version') === 'vSRO') {
                $groupBy[] = '_CharTrijob.JobType';
                $groupBy[] = '_CharTrijob.Level';
            } else {
                $groupBy[] = '_UserTradeConflictJob.JobType';
                $groupBy[] = '_CharTradeConflictJob.JobLevel';
            }

            $query->groupBy(...$groupBy)
                ->orderByDesc('ItemPoints')
                ->orderByDesc('_Char.CurLevel')
                ->limit($limit);

            return $query->get();
        });
    }

    public static function getLevelRanking($limit = 25)
    {
        $minutes = config('global.cache.ranking_player', 60);

        return Cache::remember("ranking_level_{$limit}", now()->addMinutes($minutes), function () use ($limit) {
            $query = self::select(
                '_Char.CharID',
                '_Char.CharName16',
                '_Char.CurLevel',
                '_Char.RefObjID',
                '_Char.ExpOffset',
                '_Guild.ID',
                '_Guild.Name',
            );
            $query->join('_Guild', '_Char.GuildID', '=', '_Guild.ID')
                ->where('_Char.deleted', '=', 0)
                ->whereNotIn('_Char.CharName16', config('ranking.hidden.characters'));
            $groupBy = [
                '_Char.CharID',
                '_Char.CharName16',
                '_Char.CurLevel',
                '_Char.RefObjID',
                '_Char.ExpOffset',
                '_Guild.ID',
                '_Guild.Name',
            ];
            $query->groupBy(...$groupBy)
                ->orderByDesc('_Char.CurLevel')
                ->orderByDesc('_Char.ExpOffset')
                ->limit($limit);
            return $query->get();
        });
    }

    public function setCharUnstuckPosition()
    {
        return $this->update([
            'LatestRegion' => 25000,
            'PosX' => 969,
            'PosY' => 0,
            'PosZ' => 1369,
            'AppointedTeleport' => 2094,
            'TelPosX' => 0,
            'TelPosY' => 0,
            'TelPosZ' => 0,
            'DiedPosX' => 0,
            'DiedPosY' => 0,
            'DiedPosZ' => 0,
            'WorldID' => 1,
            'TelWorldID' => 1,
            'DiedWorldID' => 1,
        ]);
    }

    public function getCharStatus()
    {
        return $this->hasMany(LogEventChar::class, 'CharID', 'CharID')
            ->whereIn('EventID', [4, 6])
            ->orderByDesc('EventTime');
    }

    public function isOnline(): bool
    {
        return optional($this->getCharStatus()->first())->EventID == 4;
    }

    public function isOffline(): bool
    {
        return optional($this->getCharStatus()->first())->EventID == 6;
    }

    public function hasJobSuit(): bool
    {
        return (bool) Inventory::getInventorySlot($this->CharID, 8);
    }

    public function getPetNames()
    {
        return InvCOS::getPetNames($this->CharID);
    }

    public static function getCharIDByName($CharName)
    {
        $minutes = config('global.cache.character_info', 1440);

        return Cache::remember("character_info_name_{$CharName}", now()->addMinutes($minutes), function () use ($CharName) {
            return self::select('CharID')->where('CharName16', $CharName)->first()->CharID ?? null;
        });
    }

    public static function getCharCount()
    {
        $minutes = config('global.cache.character_info', 1440);

        return Cache::remember('character_info_count', now()->addMinutes($minutes), function () {
            return self::count();
        });
    }

    public static function getGoldSum()
    {
        $minutes = config('global.cache.character_info', 1440);

        return Cache::remember('character_info_gold', now()->addMinutes($minutes), function () {
            return self::all()->sum('RemainGold');
        });
    }

    public function getGuildMemberUser()
    {
        return $this->hasOne(GuildMember::class, 'CharID', 'CharID');
    }

    public function getGuildUser()
    {
        $query = $this->hasOne(Guild::class, 'ID', 'GuildID');
        $query->where('ID', '!=', 0);
        return $query;
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'CharID', 'CharID');
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class, 'GuildID', 'ID');
    }
}
