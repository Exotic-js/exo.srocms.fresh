@extends('admin.layouts.app')
@section('title', __('SMC Logs'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">SMC Logs</h1>

            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <form method="GET" action="{{ route('admin.smc.logs') }}" class="mb-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="form-control d-inline w-auto">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive small">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th scope="col">UserJID</th>
                    <th scope="col">Category</th>
                    <th scope="col">Log</th>
                    <th scope="col">Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $value)
                    <tr>
                        <td>{{ $value->szUserID }}</td>
                        <td>{{ $value->Catagory }}</td>
                        <td>{{ $value->szLog }}</td>
                        <td>{{ $value->dLogDate }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No Records Found!</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
