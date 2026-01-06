<?php

namespace App\Models\SRO\Account;

use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = 'account';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'UserJID';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dbo._BlockedUser';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UserJID',
        'UserID',
        'Type',
        'SerialNo',
        'timeBegin',
        'timeEnd'
    ];

    public static function setBlockedUser($jid, $username, $serial, $now, $end)
    {
        return self::create([
            'UserJID' => $jid,
            'UserID' => $username,
            'Type' => 1,
            'SerialNo' => $serial,
            'timeBegin' => $now,
            'timeEnd' => $end,
        ]);
    }

    public function punishment()
    {
        return $this->belongsTo(Punishment::class, 'SerialNo', 'SerialNo');
    }
}
