@extends('admin.layouts.app')
@section('title', __('Vote Logs'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Vote Logs</h1>
        </div>

        <div class="table-responsive small">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>JID</th>
                    <th>Site</th>
                    <th>IP</th>
                    <th>Date</th>
                    <th>Expire Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $key => $row)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $row->jid }}</td>
                        <td>{{ $row->site }}</td>
                        <td>{{ $row->ip }}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>{{ $row->expire }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
