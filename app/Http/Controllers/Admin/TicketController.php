<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $data = Ticket::whereNull('parent_id')->with('user')->latest()->paginate(20);

        return view('admin.tickets.index', compact('data'));
    }

    public function show(Ticket $ticket)
    {
        $data = $ticket->replies()->orderBy('created_at')->get();

        return view('admin.tickets.show', compact('ticket', 'data'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        Ticket::create([
            'parent_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'admin_id' => Auth::id(),
            'subject' => $ticket->subject,
            'category' => $ticket->category,
            'type' => 'admin',
            'message' => $request->message,
            'status' => true,
        ]);

        return back()->with('success', 'Reply sent!');
    }

    public function close(Ticket $ticket)
    {
        $ticket->replies()->update(['status' => false]);
        $ticket->update(['status' => false]);

        return back()->with('success', 'Ticket closed!');
    }
}
