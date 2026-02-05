@extends('admin.layouts.app')
@section('title', __('Tickets'))

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
                    <th scope="col">ID</th>
                    <th scope="col">User</th>
                    <th scope="col">Category</th>
                    <th scope="col">Status</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $row)
                    <tr>
                        <td>#{{ $row->id }}</td>
                        <td>{{ $row->user->username }}</td>
                        <td>{{ config('global.tickets.categories')[$row->category] ?? $row->category }}</td>
                        <td>
                            @if(!$row->status)
                                <span class="badge bg-danger">Closed</span>
                            @elseif(optional($row->lastReply)->type === 'player')
                                <span class="badge bg-warning text-dark">User replied</span>
                            @else
                                <span class="badge bg-info">Waiting user</span>
                            @endif
                        </td>
                        <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.ticket.show', $row) }}" class="btn btn-sm btn-info">View</a>
                            @if($row->status)
                                <form action="{{ route('admin.ticket.close', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to close this ticket?');">
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

            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
