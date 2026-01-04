<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','admin_id','message','type','category','ticket_id','status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function admin() {
        return $this->belongsTo(User::class,'admin_id');
    }

    public function lastReplyType()
    {
        return self::where('ticket_id', $this->ticket_id)
            ->latest()
            ->value('type');
    }
}
