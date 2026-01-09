@extends('admin.layouts.app')
@section('title', __('Characters'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Characters</h1>

            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <form method="GET" action="{{ route('admin.characters.index') }}" class="mb-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search characters..." class="form-control d-inline w-auto">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive small">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">CharID</th>
                        <th scope="col">CharName</th>
                        <th scope="col">Level</th>
                        <th scope="col">Options</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td>{{ $row->CharID }}</td>
                            <td>{{ $row->CharName16 }}</td>
                            <td>{{ $row->CurLevel }}</td>
                            <td>
                                <a href="{{ route('admin.characters.view', $row->CharID) }}" class="btn btn-secondary btn-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No Records Found!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $data->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
