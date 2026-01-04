@extends('admin.layouts.app')
@section('title', __('News'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Tickets</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive small">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th scope="col">Ticket ID</th>
                    <th scope="col">User</th>
                    <th scope="col">Category</th>
                    <th scope="col">Status</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->ticket_id }}</td>
                        <td>{{ $ticket->user->username }}</td>
                        <td>{{ config('ticket.categories')[$ticket->category] ?? $ticket->category }}</td>
                        <td>
                            @if($ticket->status)
                                @if($ticket->lastReplyType() === 'player')
                                    <span class="badge bg-warning text-dark">User replied</span>
                                @else
                                    <span class="badge bg-info">Waiting user</span>
                                @endif
                            @else
                                <span class="badge bg-danger">Closed</span>
                            @endif
                        </td>
                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.ticket.show', $ticket->ticket_id) }}" class="btn btn-sm btn-info">View</a>
                            @if($ticket->status)
                                <form action="{{ route('admin.ticket.close', $ticket->ticket_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">Close</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No tickets yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
