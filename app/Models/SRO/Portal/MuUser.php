<?php

namespace App\Models\SRO\Portal;

use App\Models\SRO\Account\TbUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MuUser extends Model
{
    use HasFactory;

    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = 'portal';

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
    protected $table = 'dbo.MU_User';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'JID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UserID',
        'UserPwd',
        'Gender',
        'Birthday',
        'NickName',
        'CountryCode',
        'AbusingCount',
    ];

    protected $hidden = [
        'password'
    ];

    public static function setPortalAccount($username, $password)
    {
        return self::create([
            'UserID' => $username,
            'UserPwd' => md5($password),
            'Gender' => 'M',
            'Birthday' => now(),
            'NickName' => $username,
            'CountryCode' => 'EG',
            'AbusingCount' => 0,
        ]);
    }

    public function getJCashAttribute()
    {
        return Cache::remember("account_jcash_{$this->JID}", config('global.cache.account_info', 600), function () {
            return collect(DB::select('
                DECLARE @ReturnValue Int, @PremiumSilk Int, @Silk Int, @VipLevel Int, @UsageMonth Int, @Usage3Month Int;
                SET NOCOUNT ON;
                EXECUTE @ReturnValue = [GB_JoymaxPortal].[dbo].[B_GetJCash] ?, @PremiumSilk OUTPUT, @Silk OUTPUT, @VipLevel OUTPUT, @UsageMonth OUTPUT, @Usage3Month OUTPUT;
                SELECT @ReturnValue AS ErrorCode, @PremiumSilk AS PremiumSilk, @Silk AS Silk, @UsageMonth AS MonthUsage, @Usage3Month AS ThreeMonthUsage;
            ', [intval($this->JID)]))->first();
        });
    }

    public function getMuVIPInfoAttribute()
    {
        return cache()->remember( "user_muVIPInfo_{$this->JID}", 600, fn () => $this->muVIPInfo()->first());
    }

    public function getMuEmailAttribute()
    {
        return cache()->remember( "user_muEmail_{$this->JID}", 600, fn () => $this->muEmail()->first());
    }

    public function muEmail()
    {
        return $this->hasOne(MuEmail::class, 'JID', 'JID');
    }

    public function muAlteredInfo()
    {
        return $this->hasOne(MuhAlteredInfo::class, 'JID', 'JID');
    }

    public function muVIPInfo()
    {
        return $this->hasOne(MuVIPInfo::class, 'JID', 'JID');
    }

    public function aphChangedSilk()
    {
        return $this->hasMany(AphChangedSilk::class, 'JID', 'JID');
    }

    public function tbUser()
    {
        return $this->belongsTo(TbUser::class, 'PortalJID', 'JID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'jid', 'JID');
    }
}
