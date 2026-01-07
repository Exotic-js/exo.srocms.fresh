<?php

namespace App\Models\SRO\Account;

use Illuminate\Database\Eloquent\Model;

class SecondaryPassword extends Model
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
    protected $table = 'dbo._SecondaryPassword';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UserJID',
        'SecondPassword',
        'BlockedStartTime',
        'ErrorCount',
    ];

    public function user()
    {
        return $this->belongsTo(TBUser::class, 'JID', 'UserJID');
    }
}
