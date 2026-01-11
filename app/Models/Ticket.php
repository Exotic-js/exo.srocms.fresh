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
        'status'
    ];

    public static function createTicket(array $data): self
    {
        $ticket = self::create([
            'parent_id' => $data['parent_id'] ?? null,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'admin_id' => $data['admin_id'] ?? null,
            'subject' => $data['subject'] ?? '',
            'category' => $data['category'] ?? '',
            'type' => $data['type'] ?? 'player',
            'message' => $data['message'],
            'status' => $data['status'] ?? true,
        ]);

        Cache::forget("user_tickets_{$ticket->user_id}_page_1");

        if ($ticket->parent_id) {
            Cache::forget("ticket_replies_{$ticket->parent_id}");
        }

        Cache::forget("ticket_{$ticket->parent_id}_replies");
        Cache::forget("admin_tickets_page_1");

        return $ticket;
    }

    public static function getUserTickets(int $userId, int $perPage = 20)
    {
        $page = request('page', 1);

        return Cache::remember("user_tickets_{$userId}_page_{$page}", 600, function () use ($userId, $perPage) {
                return self::where('user_id', $userId)
                    ->whereNull('parent_id')
                    ->latest()
                    ->paginate($perPage);
            }
        );
    }

    public static function getUserTicket(int $ticketId, int $userId): ?self
    {
        return Cache::remember("ticket_{$ticketId}", 600, function () use ($ticketId, $userId) {
            return self::where('id', $ticketId)
                ->where('user_id', $userId)
                ->first();
        });
    }

    public static function getReplies(int $ticketId)
    {
        return Cache::remember("ticket_{$ticketId}_replies", 600, function () use ($ticketId) {
            return self::where('parent_id', $ticketId)
                ->orderBy('created_at')
                ->get();
        });
    }

    public static function getLastReply(int $ticketId): ?self
    {
        return Cache::remember("ticket_{$ticketId}_last_reply", 600, function () use ($ticketId) {
            return self::where('parent_id', $ticketId)
                ->latest()
                ->first();
        });
    }

    public static function getAdminTickets(int $perPage = 20)
    {
        $page = request('page', 1);

        return Cache::remember("admin_tickets_page_{$page}", 600, function () use ($perPage) {
                return self::whereNull('parent_id')
                    ->with(['user', 'lastReply'])
                    ->latest()
                    ->paginate($perPage);
            }
        );
    }

    public static function getTicketReplies(int $ticketId)
    {
        return Cache::remember("ticket_replies_{$ticketId}", 600, function () use ($ticketId) {
                return self::where('parent_id', $ticketId)
                    ->with('user')
                    ->orderBy('created_at')
                    ->get();
            }
        );
    }

    public static function closeTicket(int $ticketId): void
    {
        self::where('id', $ticketId)->orWhere('parent_id', $ticketId)->update(['status' => false]);

        Cache::forget("ticket_{$ticketId}");
        Cache::forget("ticket_{$ticketId}_replies");
        Cache::forget("ticket_{$ticketId}_last_reply");
    }

    public function lastReply() {
        return $this->hasOne(self::class, 'parent_id')->latest();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
