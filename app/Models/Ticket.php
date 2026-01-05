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

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
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
