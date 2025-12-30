@extends('layouts.guest')
@section('title', __('Verify OTP'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mt-5">{{ __('Verify OTP') }}</h2>

                @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->has('otp'))
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('otp') }}
                    </div>
                @endif

                <p class="text-muted mb-4">
                    {{ __('We sent a 6-digit verification code to') }} <b>{{ session('otp_email') }}</b>
                </p>

                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf

                    <div class="form-group row mb-3">
                        <label for="otp" class="col-md-12 col-form-label text-md-left">{{ __('OTP Code') }}</label>
                        <div class="col-md-12">
                            <input id="otp" type="text" class="form-control @error('otp') is-invalid @enderror" name="otp" value="{{ old('otp') }}" required autofocus placeholder="Enter 6-digit code">
                            @error('otp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Verify') }}
                            </button>

                            <form method="POST" action="{{ route('otp.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link">
                                    {{ __('Resend code') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
