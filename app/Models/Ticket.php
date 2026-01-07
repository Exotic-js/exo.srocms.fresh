<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'user_id',
        'admin_id',
        'subject',
        'category',
        'type',
        'message',
        'status'
    ];

    public static function createTicket(array $data)
    {
        return self::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'subject' => $data['subject'] ?? '',
            'category' => $data['category'] ?? '',
            'message' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'player',
            'status' => $data['status'] ?? true,
            'parent_id' => $data['parent_id'] ?? null,
        ]);
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function lastReply()
    {
        return $this->hasOne(self::class, 'parent_id')->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
