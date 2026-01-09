@extends('admin.layouts.app')
@section('title', __('Vouchers'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Vouchers</h1>

            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-sm btn-outline-secondary">+ New Voucher</a>
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
                    <th scope="col">Code</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Type</th>
                    <th scope="col">Expire Date</th>
                    <th scope="col">Used By</th>
                    <th scope="col">Status</th>
                    <th scope="col">Options</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $row)
                    <tr>
                        <td>{{ $row->code }}</td>
                        <td>{{ $row->amount }}</td>
                        <td>{{ $row->type == 0 ? 'Normal' : 'Premium' }}</td>
                        <td>{{ $row->valid_date ? $row->valid_date->format('Y-m-d H:i:s') : 'No Expiration' }}</td>
                        <td>{{ $row->user->username ?? 'None' }}</td>
                        <td>
                            @if($row->status == 'Used')
                                <span class="text-success">Used<span>
                            @elseif($row->status == 'Unused')
                                <span class="text-warning">Unused</span>
                            @else
                                <span class="text-danger">Disabled</span>
                            @endif
                        </td>
                        <td>
                            @if($row->status == 'Unused')
                                <a href="{{ route('admin.vouchers.toggle', $row->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to disable voucher?')">Disable</a>
                            @elseif($row->status == 'Disabled')
                                <a href="{{ route('admin.vouchers.toggle', $row->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to enable voucher?')">Enable</a>
                            @else
                                <button href="#" class="btn btn-danger btn-sm" disabled="">Disable</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No Records Found!</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
