<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    public $timestamps = false;

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    public static function setToken(string $email, string|int $token): self
    {
        return self::updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );
    }

    public static function getToken(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    public function isExpired(int $minutes = 30): bool
    {
        return Carbon::parse($this->created_at)
            ->addMinutes($minutes)
            ->isPast();
    }

    public function deleteToken(): bool
    {
        return $this->delete();
    }
}
