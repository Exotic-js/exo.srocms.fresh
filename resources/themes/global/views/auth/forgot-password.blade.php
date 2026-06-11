@extends('layouts.guest')
@section('title', __('Forgot Password'))

@section('content')
    <section class="page-login vh-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="card w-75">
                            <div class="card-header">
                                <img src="{{ asset('themes/global/assets/images/widget-icon-login.png') }}" alt="" height="31">
                                <h3>Forgot Password</h3>
                            </div>
                            <div class="card-body">

                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <div class="col-md-12">
                                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf

                                    <div class="form-group row mb-3">
                                        <label for="email" class="col-md-12 col-form-label text-md-left">{{ __('Email') }}</label>

                                        <div class="col-md-12">
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-secondary">
                                                {{ __('Email Password Reset Link') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 px-0 d-none d-sm-block">
                    <img src="{{ asset('themes/global/assets/images/login-bg.png') }}" alt="Login image" class="w-100 vh-100" style="object-fit: cover; object-position: left;">
                </div>
            </div>
        </div>
    </section>
@endsection
