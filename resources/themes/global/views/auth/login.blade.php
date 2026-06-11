@extends('layouts.guest')
@section('title', __('Login'))

@section('content')
    <section class="golden-main cinematic-auth-section vh-100 position-relative d-flex align-items-center justify-content-center" style="background-image: url('{{ asset('themes/global/assets/images/login-bg.png') }}'); background-size: cover; background-position: center;">
        
        {{-- Atmospheric dark overlay --}}
        <div class="cinematic-auth-overlay position-absolute w-100 h-100 top-0 start-0" style="background: linear-gradient(135deg, rgba(10, 8, 5, 0.9) 0%, rgba(10, 8, 5, 0.4) 50%, rgba(10, 8, 5, 0.9) 100%);"></div>

        <div class="container position-relative" style="z-index: 10;">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5">
                    
                    {{-- Glassmorphism Card --}}
                    <div class="cinematic-auth-box p-5">
                        <div class="auth-box-header mb-5 text-center">
                            <img src="{{ asset('themes/global/assets/images/widget-icon-login.png') }}" alt="Login" class="mb-3" height="55">
                            <h2 class="cinematic-heading text-uppercase mb-1" style="color: #ebd197; text-shadow: 0 0 10px rgba(201,160,91,0.5);">{{ __('Begin Your Saga') }}</h2>
                            <p class="cinematic-subheading" style="color: #dfcdb8;">{{ __('Enter your credentials to continue') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success cinematic-alert" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="cinematic-auth-form text-start">
                            @csrf

                            <div class="form-group mb-4">
                                <label for="username" class="cinematic-label mb-2" style="color: #ebd197;">{{ __('Username') }}</label>
                                <input id="username" type="text" class="form-control cinematic-input @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autofocus placeholder="Enter your username">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="password" class="cinematic-label mb-2" style="color: #ebd197;">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control cinematic-input @error('password') is-invalid @enderror" name="password" required placeholder="Enter your password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            @if (env('NOCAPTCHA_ENABLE', false))
                                <!-- google recaptch -->
                                <div class="form-group mb-4 d-flex justify-content-center">
                                    {!! NoCaptcha::renderJs() !!}
                                    {!! NoCaptcha::display() !!}
                                    @error('g-recaptcha-response')
                                        <span class="invalid-feedback d-block text-center" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif

                            <div class="form-group mb-4 d-flex justify-content-between align-items-center">
                                <div class="form-check cinematic-checkbox">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember" style="color: #dfcdb8;">
                                        {{ __('Remember me') }}
                                    </label>
                                </div>
                                
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="cinematic-link" style="color: #c9a05b; text-decoration: none; transition: 0.3s;">{{ __('Forgot password?') }}</a>
                                @endif
                            </div>

                            <div class="form-group mb-0 text-center mt-5">
                                <button type="submit" class="btn cinematic-btn-primary w-100 text-uppercase fw-bold py-3 fs-5">
                                    {{ __('Log in') }}
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 pt-4 border-top text-center" style="border-color: rgba(201, 160, 91, 0.15) !important;">
                            <p class="mb-0" style="color: #dfcdb8;">Don't have an account? <a href="{{ route('register') }}" class="cinematic-link fw-bold" style="color: #ffdc87; text-decoration: none;">Create account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
