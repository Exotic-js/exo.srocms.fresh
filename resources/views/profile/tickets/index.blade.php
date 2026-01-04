@extends('layouts.app')
@section('title', __('Tickets'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <a href="{{ route('profile.ticket.create') }}" class="btn btn-primary mb-3">New Ticket</a>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">Ticket ID</th>
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
                            <td>{{ config('ticket.categories')[$ticket->category] ?? $ticket->category }}</td>
                            <td>
                                @if($ticket->status)
                                    @if($ticket->lastReplyType() === 'admin')
                                        <span class="badge bg-success">Admin replied</span>
                                    @else
                                        <span class="badge bg-secondary">Waiting support</span>
                                    @endif
                                @else
                                    <span class="badge bg-danger">Closed</span>
                                @endif
                            </td>
                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('profile.ticket.show', $ticket->ticket_id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No tickets yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
