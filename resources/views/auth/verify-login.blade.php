@extends('layouts.guest')
@section('title', __('Verification Code'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mt-5">{{ __('Verification Code') }}</h2>

                @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <p class="text-muted mb-4">
                    {{ __('We sent a 6-digit verification code to your email.') }}
                </p>

                <form method="POST" action="{{ route('login.verify') }}">
                    @csrf

                    <div class="form-group row mb-3">
                        <label for="code" class="col-md-12 col-form-label text-md-left">{{ __('Verification Code') }}</label>
                        <div class="col-md-12">
                            <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required autofocus placeholder="Enter 6-digit code">

                            @error('code')
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

                            <button type="submit" form="verify_resend" class="btn btn-link">
                                {{ __('Resend code') }}
                            </button>
                        </div>
                    </div>
                </form>
                <form method="POST" id="verify_resend" action="{{ route('login.resend') }}">
                    @csrf
                </form>
            </div>
        </div>
    </div>
@endsection
