@extends('admin.layouts.app')
@section('title', __('Create Voucher'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Create Voucher</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.vouchers.store') }}">
            @csrf
            <div class="row mb-3">
                <label for="amount" class="col-md-2 col-form-label text-md-end">{{ __('Amount') }}</label>

                <div class="col-md-10">
                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>

                    @error('amount')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="type" class="col-md-2 col-form-label text-md-end">{{ __('Type') }}</label>

                <div class="col-md-10">
                    <select class="form-select @error('amount') is-invalid @enderror" name="type" aria-label="Default select example">
                        @if(config('global.server.version') === 'vSRO')
                            <option value="0">Normal</option>
                            <option value="1">Gift</option>
                            <option value="2">Point</option>
                        @else
                            <option value="0">Normal</option>
                            <option value="3">Premium</option>
                        @endif
                    </select>

                    @error('type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="valid_date" class="col-md-2 col-form-label text-md-end">{{ __('Valid Date At') }}</label>

                <div class="col-md-10">
                    <input id="valid_date" type="datetime-local" class="form-control @error('valid_date') is-invalid @enderror" name="valid_date" value="{{ old('valid_date', now()->format('Y-m-d')) }}" required>

                    @error('valid_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-0">
                <div class="col-md-10 offset-md-2">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Voucher') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')

@endpush
@push('scripts')

@endpush
