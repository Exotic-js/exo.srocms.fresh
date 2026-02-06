@extends('layouts.app')
@section('title', __('Tickets'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                @if(config('global.tickets.enabled'))
                <a href="{{ route('profile.ticket.create') }}" class="btn btn-primary mb-3">New Ticket</a>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td>#{{ $row->id }}</td>
                            <td>{{ $row->subject }}</td>
                            <td>{{ config('global.tickets.categories')[$row->category] ?? $row->category }}</td>
                            <td>
                                @if(!$row->status)
                                    <span class="badge bg-danger">Closed</span>
                                @elseif($row->lastReply && $row->lastReply->type === 'admin')
                                    <span class="badge bg-success">Admin replied</span>
                                @else
                                    <span class="badge bg-secondary">Waiting support</span>
                                @endif
                            </td>
                            <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('profile.ticket.show', $row) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No tickets yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $data->links('pagination::bootstrap-5') }}
                @else
                    <div class="alert alert-danger text-center" role="alert">
                        {{ __('Ticket is disabled!') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
