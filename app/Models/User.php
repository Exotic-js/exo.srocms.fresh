<?php

namespace App\Models;

use App\Models\SRO\Account\SkSilk;
use App\Models\SRO\Account\TbUser;
use App\Models\SRO\Portal\AphChangedSilk;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\SRO\Portal\MuUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'jid',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function updateGameEmail(): void
    {
        DB::transaction(function () {
            if (config('global.server.version') === 'vSRO') {
                $this->tbUser?->update(['Email' => $this->email,]);
            } else {
                $this->muUser?->muEmail?->update(['EmailAddr' => $this->email,]);

                $this->muUser?->muAlteredInfo?->update([
                    'EmailAddr' => $this->email,
                    'EmailReceptionStatus' => config('settings.register_confirm') ? 'N' : 'Y',
                    'EmailCertificationStatus' => config('settings.register_confirm') ? 'N' : 'Y',
                ]);
            }
        });
    }

    public function updateGamePassword(string $password): void
    {
        DB::transaction(function () use ($password) {
            if (config('global.server.version') === 'vSRO') {
                $this->tbUser?->update(['password' => md5($password)]);
            } else {
                $this->muUser?->update(['UserPwd' => md5($password)]);
                $this->tbUser?->update(['password' => md5($password)]);
            }
        });
    }

    public function giveSilk(string $type, float $amount)
    {
        if (config('global.server.version') === 'vSRO') {
            SkSilk::setSkSilk($this->jid, $type, $amount);
        } else {
            AphChangedSilk::setChangedSilk($this->tbUser?->PortalJID, $type, $amount);
        }
    }

    public function tbUser()
    {
        if (config('global.server.version') === 'vSRO') {
            return $this->hasOne(TbUser::class, 'JID', 'jid');
        } else{
            return $this->hasOne(TbUser::class, 'PortalJID', 'jid');
        }
    }

    public function role()
    {
        return $this->hasOne(UserRole::class);
    }

    public function muUser()
    {
        return $this->hasOne(MuUser::class, 'JID', 'jid');
    }

    public function getTbUserAttribute()
    {
        return cache()->remember( "user_tbUser_{$this->jid}", 600, fn () => $this->tbUser()->first());
    }

    public function getMuUserAttribute()
    {
        return cache()->remember( "user_muUser_{$this->jid}", 600, fn () => $this->muUser()->first());
    }

    public function getRoleAttribute()
    {
        return cache()->remember( "user_role_{$this->jid}", 600, fn () => $this->role()->first());
    }

    public static function getUserCount()
    {
        return Cache::remember('user_count', 600, function () {
            return self::count();
        });
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id')->whereNull('parent_id')->latest();
    }

    public function getTickets(int $limit = 10)
    {
        return Cache::remember("user:{$this->id}:tickets:latest", 600, fn () => $this->tickets()->limit($limit)->get());
    }

    public function getInvitesCreated()
    {
        return cache()->remember( "user_invites_created_{$this->jid}", 600, fn () => $this->invitesCreated()->get());
    }

    public function clearInvitesCache(): void
    {
        Cache::forget("user_invites_created_{$this->jid}");
    }

    public function invitesCreated()
    {
        return $this->hasMany(Referral::class, 'jid', 'jid');
    }

    public function invitesUsed()
    {
        return $this->hasMany(Referral::class, 'invited_jid', 'jid');
    }
}
