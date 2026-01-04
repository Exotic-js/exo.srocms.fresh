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
        $tickets = Ticket::whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('tickets')
                    ->groupBy('ticket_id');
            })
            ->latest()
            ->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show($ticket_id)
    {
        $messages = Ticket::where('ticket_id', $ticket_id)->get();
        return view('admin.tickets.show', compact('messages','ticket_id'));
    }

    public function reply(Request $request, $ticket_id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::where('ticket_id',$ticket_id)->first();

        Ticket::create([
            'ticket_id' => $ticket_id,
            'user_id' => $ticket->user_id,
            'admin_id' => Auth::id(),
            'category' => $ticket->category,
            'type' => 'admin',
            'message' => $request->message,
            'status' => true,
        ]);

        return back()->with('success','Reply sent!');
    }

    public function close($ticket_id)
    {
        Ticket::where('ticket_id',$ticket_id)->update(['status'=>false]);
        return back()->with('success','Ticket closed!');
    }
}
