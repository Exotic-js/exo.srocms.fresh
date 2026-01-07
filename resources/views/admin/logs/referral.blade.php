@extends('admin.layouts.app')
@section('title', __('Referral Logs'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Referral Logs</h1>
        </div>

        <div class="table-responsive small">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th>Rank</th>
                    <th>Invite Code</th>
                    <th>IP</th>
                    <th>Username</th>
                    <th>JID</th>
                    <th>Total Points</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $i => $value)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $value->code }}</td>
                        <td>{{ $value->ip }}</td>
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->jid }}</td>
                        <td>{{ $value->total_points }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
