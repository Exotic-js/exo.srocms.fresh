<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Guild extends Model
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
    protected $table = 'dbo._Guild';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $dates = [
        'FoundationDate'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public static function getGuildRanking(int $limit = 25, int $GuildID = 0, string $Name = '')
    {
        $Name = substr(preg_replace('/[^a-zA-Z0-9_]/', '', $Name), 0, 50);

        return Cache::remember("ranking_guild_{$limit}_{$GuildID}_{$Name}", config('global.cache.ranking_guild', 3600), function () use ($limit, $GuildID, $Name) {
            $query = self::from(DB::raw('_Guild WITH (NOLOCK)'))
            ->select(
                '_Guild.ID',
                '_Guild.Name',
                '_Guild.Lvl',
                '_Guild.GatheredSP',
                '_Guild.FoundationDate',

                DB::raw("(SELECT TOP 1 CharID FROM _GuildMember WITH (NOLOCK) WHERE GuildID = _Guild.ID AND MemberClass = 0 ) AS LeaderID"),
                DB::raw("(SELECT TOP 1 CharName FROM _GuildMember WITH (NOLOCK) WHERE GuildID = _Guild.ID AND MemberClass = 0 ) AS LeaderName"),
                DB::raw("(SELECT COUNT(*) FROM _GuildMember WITH (NOLOCK) WHERE GuildID = _Guild.ID ) AS TotalMember"),

                DB::raw("(
                SELECT
                      SUM(ISNULL(b.nOptValue,0))
                    + SUM(ISNULL(i.OptLevel,0))
                    + SUM(ISNULL(r.ReqLevel1,0))
                    + SUM(CASE WHEN r.CodeName128 LIKE '%_A_RARE%' THEN 5 ELSE 0 END)
                    + SUM(CASE WHEN r.CodeName128 LIKE '%_B_RARE%' THEN 10 ELSE 0 END)
                    + SUM(CASE WHEN r.CodeName128 LIKE '%_C_RARE%' THEN 15 ELSE 0 END)
                FROM _GuildMember gm WITH (NOLOCK)
                JOIN _Inventory inv WITH (NOLOCK) ON inv.CharID = gm.CharID
                JOIN _Items i WITH (NOLOCK) ON i.ID64 = inv.ItemID
                JOIN _RefObjCommon r WITH (NOLOCK) ON r.ID = i.RefItemID
                LEFT JOIN _BindingOptionWithItem b WITH (NOLOCK)
                    ON b.nItemDBID = i.ID64
                   AND b.bOptType = 2
                   AND b.nOptValue > 0
                WHERE gm.GuildID = _Guild.ID
                  AND inv.Slot < 13
                  AND inv.Slot NOT IN (7,8)
                  AND inv.ItemID > 0
            ) AS ItemPoints")
            );

            if (config('global.server.version') !== 'vSRO') {
                $query->addSelect(DB::raw("CONVERT(VARCHAR(MAX), _GuildCrest.CrestBinary, 2) AS CrestIcon"))
                    ->leftJoin(DB::raw('_GuildCrest WITH (NOLOCK)'), '_GuildCrest.GuildID', '=', '_Guild.ID');
            }

            $query->when($GuildID > 0, fn ($q) => $q->where('_Guild.ID', $GuildID))
                ->when($Name !== '', fn ($q) => $q->where('_Guild.Name', 'like', "%{$Name}%"))
                ->whereNotIn('_Guild.Name', config('ranking.hidden.guilds'))
                ->orderByDesc('ItemPoints')
                ->orderByDesc('_Guild.Lvl')
                ->limit($limit);

            return $query->get();
        });
    }

    public static function getFortressGuildRanking($limit = 25)
    {
        return Cache::remember("ranking_fortress_guild_{$limit}", config('global.cache.ranking_fortress_guild', 3600), function () use ($limit) {
            return self::select(
                '_Guild.ID',
                '_Guild.Name',
                DB::raw('(SELECT SUM(GuildWarKill) FROM _GuildMember WHERE GuildID = _Guild.ID) AS TotalKills'),
                DB::raw('(SELECT SUM(GuildWarKilled) FROM _GuildMember WHERE GuildID = _Guild.ID) AS TotalDeath')
            )
            ->join('_GuildMember', '_Guild.ID', '=', '_GuildMember.GuildID')
            ->where('_Guild.ID', '>', 0)
            ->groupBy('_Guild.ID', '_Guild.Name')
            ->orderByDesc('TotalKills')
            ->limit($limit)
            ->get();
        });
    }

    public static function getGuildByName($name)
    {
        return Cache::remember("guild_info_name_{$name}", config('global.cache.guild_info', 86400), function () use ($name) {
            return self::where('Name', $name)->firstOrFail();
        });
    }

    public function getAllianceAttribute()
    {
        return Cache::remember("guild_info_alliance_{$this->ID}", config('global.cache.guild_info', 86400), function () {
            return self::select('Name')
                ->where('Alliance', function ($query) {
                    $query->select('Alliance')
                        ->from('_Guild')
                        ->where('ID', $this->ID)
                        ->where('Alliance', '>', 0);
                })
                ->where('ID', '!=', $this->ID)
                ->get();
        });
    }

    public static function getGuildCount()
    {
        return Cache::remember('guild_count', 86400, function () {
            return self::count();
        });
    }

    public function getLeaderNameAttribute()
    {
        return Cache::remember("guild_leader_{$this->ID}", config('global.cache.guild_info', 86400), function () {
            return DB::connection($this->getConnectionName())
                ->table('_GuildMember')
                ->where('GuildID', $this->ID)
                ->where('MemberClass', 0)
                ->value('CharName');
        });
    }

    public function getTotalMemberAttribute()
    {
        return Cache::remember("guild_total_member_{$this->ID}", config('global.cache.guild_info', 86400), function () {
            return DB::connection($this->getConnectionName())
                ->table('_GuildMember')
                ->where('GuildID', $this->ID)
                ->count();
        });
    }

    public function getItemPointsAttribute()
    {
        return Cache::remember("guild_item_points_{$this->ID}", config('global.cache.guild_info', 86400), function () {
            return DB::connection($this->getConnectionName())
                ->table('_GuildMember as gm')
                ->join('_Inventory as inv', 'inv.CharID', '=', 'gm.CharID')
                ->join('_Items as i', 'i.ID64', '=', 'inv.ItemID')
                ->join('_RefObjCommon as r', 'r.ID', '=', 'i.RefItemID')
                ->leftJoin('_BindingOptionWithItem as b', function ($join) {
                    $join->on('b.nItemDBID', '=', 'i.ID64')
                        ->where('b.bOptType', 2)
                        ->where('b.nOptValue', '>', 0);
                })
                ->where('gm.GuildID', $this->ID)
                ->where('inv.Slot', '<', 13)
                ->whereNotIn('inv.Slot', [7, 8])
                ->where('inv.ItemID', '>', 0)
                ->selectRaw("
                      SUM(ISNULL(b.nOptValue,0))
                    + SUM(ISNULL(i.OptLevel,0))
                    + SUM(ISNULL(r.ReqLevel1,0))
                    + SUM(CASE WHEN r.CodeName128 LIKE '%_A_RARE%' THEN 5 ELSE 0 END)
                    + SUM(CASE WHEN r.CodeName128 LIKE '%_B_RARE%' THEN 10 ELSE 0 END)
                    + SUM(CASE WHEN r.CodeName128 LIKE '%_C_RARE%' THEN 15 ELSE 0 END)
                ")
                ->value(DB::raw(''));
        });
    }

    public function getCrestAttribute()
    {
        if (config('global.server.version') !== 'vSRO') {
            return Cache::remember("guild_crest_{$this->ID}", config('global.cache.guild_info', 86400), function () {
                return DB::connection($this->getConnectionName())
                    ->table('_GuildCrest')
                    ->where('GuildID', $this->ID)
                    ->value(DB::raw("CONVERT(VARCHAR(MAX), CrestBinary, 2)"));
            });
        }
        return null;
    }

    public function getMembersAttribute()
    {
        return Cache::remember("guild_members_{$this->ID}", config('global.cache.guild_info', 86400), function () {
            return $this->members()->get();
        });
    }

    public function members()
    {
        return $this->hasMany(GuildMember::class, 'GuildID', 'ID');
    }
}
