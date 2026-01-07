<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data = $user->tickets()->paginate(20);

        return view('profile.tickets.index', compact('data'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('replies');

        if ($ticket->user_id != auth()->id()) {
            abort(403, 'Ticket not yours');
        }

        return view('profile.tickets.show', compact('ticket'));
    }

    public function create()
    {
        $config = config('global.tickets.categories');

        return view('profile.tickets.create', compact('config'));
    }

    public function send(Request $request)
    {
        $config = array_keys(config('global.tickets.categories'));

        $validated = $request->validate([
            'subject' => 'required_without:parent_id|string|max:255',
            'message' => 'required|string',
            'category' => 'required_without:parent_id|in:' . implode(',', $config),
            'parent_id' => 'nullable|integer',
        ]);

        if ($request->filled('parent_id')) {
            $parentTicket = Ticket::findOrFail($request->parent_id);

            if ($parentTicket->user_id != auth()->id()) {
                abort(403, 'Ticket not yours');
            }

            Ticket::createTicket([
                'parent_id'=> $parentTicket->id,
                'message'  => $validated['message'],
            ]);

            return back()->with('success', 'Reply sent!');
        }

        Ticket::createTicket([
            'subject'  => $validated['subject'],
            'category' => $validated['category'],
            'message'  => $validated['message'],
        ]);

        return redirect()->route('profile.tickets')->with('success', 'Ticket created!');
    }
}
