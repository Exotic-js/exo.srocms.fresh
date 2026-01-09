@extends('layouts.app')
@section('title', __('Voucher'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="mb-3">
                    <form method="POST" action="{{ route('profile.voucher.redeem') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="voucher_code" class="form-label">{{ __('Voucher Code:') }}</label>

                            <div class="input-group">
                                <input id="voucher_code" type="text" class="form-control @error('voucher_code') is-invalid @enderror" name="voucher_code" placeholder="Enter Voucher Code" required>
                                <button type="submit" class="btn btn-primary">{{ __('Redeem') }}</button>
                            </div>

                            @error('voucher_code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </form>
                </div>

                <div class="mt-5">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($data as $i => $value)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $value->code }}</td>
                                        <td>{{ $value->amount }}</td>
                                        <td>{{ $value->type == 0 ? 'Normal' : 'Premium' }}</td>
                                        <td>{{ $value->updated_at ? $value->updated_at->format('Y-m-d H:i:s') : 'N/N' }}</td>
                                        <td>
                                            @if($value->status)
                                                <span class="text-success">Success<span>
                                            @else
                                                <span class="text-danger">Failed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="6">No Voucher card used.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
