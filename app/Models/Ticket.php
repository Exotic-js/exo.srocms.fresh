<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Ticket extends Model
{
    protected $fillable = [
        'parent_id',
        'user_id',
        'admin_id',
        'subject',
        'category',
        'type',
        'message',
        'status',
    ];

    public static function open(array $data): self
    {
        $ticket = self::create([
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'category' => $data['category'],
            'message' => self::sanitizeHtml($data['message']),
            'type' => 'player',
            'status' => true,
        ]);

        Cache::forget("user:{$ticket->user_id}:tickets:page:1");
        Cache::forget("admin:tickets:page:1");

        return $ticket;
    }

    public static function replyTo(self $parent, array $data): self
    {
        abort_if(!$parent->status, 403, 'Ticket closed');

        $reply = self::create([
            'parent_id' => $parent->id,
            'user_id' => $parent->user_id,
            'admin_id' => $data['admin_id'] ?? null,
            'subject' => $parent->subject,
            'category' => $parent->category,
            'message' => self::sanitizeHtml($data['message']),
            'type' => $data['type'],
            'status' => true,
        ]);

        Cache::forget("ticket:{$parent->id}:replies");
        Cache::forget("ticket:{$parent->id}:last_reply");
        Cache::forget("admin:tickets:page:1");

        return $reply;
    }

    private static function sanitizeHtml(string $html): string
    {
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('/on\w+="[^"]*"/i', '', $html);
        $html = preg_replace('/javascript:/i', '', $html);

        $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><a><span>';

        return strip_tags($html, $allowed);
    }

    public static function getUserTickets(int $userId, int $perPage = 20)
    {
        $page = request('page', 1);

        return Cache::remember("user:{$userId}:tickets:page:{$page}", 600, fn () =>
            self::where('user_id', $userId)
                ->whereNull('parent_id')
                ->latest()
                ->paginate($perPage)
        );
    }

    public static function getUserTicket(int $ticketId, int $userId): ?self
    {
        return Cache::remember("ticket:{$ticketId}:user:{$userId}", 600, fn () =>
            self::where('id', $ticketId)
                ->where('user_id', $userId)
                ->whereNull('parent_id')
                ->first()
        );
    }

    public static function getReplies(int $ticketId)
    {
        return Cache::remember("ticket:{$ticketId}:replies", 600, fn () =>
            self::findOrFail($ticketId)
                ->replies()
                ->with('user')
                ->get()
        );
    }

    public static function getLastReply(int $ticketId): ?self
    {
        return Cache::remember("ticket:{$ticketId}:last_reply", 600, fn () =>
            self::where('parent_id', $ticketId)
                ->latest()
                ->first()
        );
    }

    public static function getAdminTickets(int $perPage = 20)
    {
        $page = request('page', 1);

        return Cache::remember("admin:tickets:page:{$page}", 600, fn () =>
            self::whereNull('parent_id')
                ->with(['user', 'lastReply'])
                ->latest()
                ->paginate($perPage)
        );
    }

    public static function close(int $ticketId): void
    {
        self::where('id', $ticketId)->orWhere('parent_id', $ticketId)->update(['status' => false]);

        Cache::forget("ticket:{$ticketId}:replies");
        Cache::forget("ticket:{$ticketId}:last_reply");
        Cache::forget("admin:tickets:page:1");
    }

    public static function getTicketsCount()
    {
        return Cache::remember('tickets:count', 60, function () {
            return self::whereNull('parent_id')->count();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('created_at');
    }

    public function lastReply()
    {
        return $this->hasOne(self::class, 'parent_id')
            ->latestOfMany();
    }
}
