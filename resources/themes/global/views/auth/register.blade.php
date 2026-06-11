@extends('layouts.guest')
@section('title', __('Register'))

@section('content')
    <section class="golden-main cinematic-auth-section vh-100 position-relative d-flex align-items-center justify-content-center" style="background-image: url('{{ asset('themes/global/assets/images/register-bg.png') }}'); background-size: cover; background-position: center; overflow-y: auto; padding: 50px 0;">
        
        {{-- Atmospheric dark overlay --}}
        <div class="cinematic-auth-overlay position-fixed w-100 h-100 top-0 start-0" style="background: linear-gradient(135deg, rgba(10, 8, 5, 0.9) 0%, rgba(10, 8, 5, 0.4) 50%, rgba(10, 8, 5, 0.9) 100%);"></div>

        <div class="container position-relative" style="z-index: 10;">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    
                    {{-- Glassmorphism Card --}}
                    <div class="cinematic-auth-box p-5 mt-5 mb-5">
                        <div class="auth-box-header mb-5 text-center">
                            <img src="{{ asset('themes/global/assets/images/widget-icon-login.png') }}" alt="Register" class="mb-3" height="55">
                            <h2 class="cinematic-heading text-uppercase mb-1" style="color: #ebd197; text-shadow: 0 0 10px rgba(201,160,91,0.5);">{{ __('Join The Realm') }}</h2>
                            <p class="cinematic-subheading" style="color: #dfcdb8;">{{ __('Forge your legacy today') }}</p>
                        </div>

                        @if (!config('settings.disable_register', false))
                            @if ($errors->any())
                                <div class="alert alert-danger" style="background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ffb5b5; border-radius: 10px;">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('register') }}" class="cinematic-auth-form text-start">
                                @csrf

                                <div class="form-group mb-4">
                                    <label for="username" class="cinematic-label mb-2" style="color: #ebd197;">{{ __('Username') }}</label>
                                    <input id="username" type="text" class="form-control cinematic-input @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autofocus placeholder="Choose a username">
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="email" class="cinematic-label mb-2" style="color: #ebd197;">{{ __('Email Address') }}</label>
                                    <input id="email" type="email" class="form-control cinematic-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="phone_number" class="cinematic-label mb-2" style="color: #ebd197;">{{ __('Phone Number') }}</label>
                                    <input id="phone_number" type="text" class="form-control cinematic-input @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Enter your phone number">
                                    @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="password" class="cinematic-label mb-2" style="color: #ebd197;">{{ __('Password') }}</label>
                                            <input id="password" type="password" class="form-control cinematic-input @error('password') is-invalid @enderror" name="password" required placeholder="Create password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-4">
                                            <label for="password-confirm" class="cinematic-label mb-2" style="color: #ebd197;">{{ __('Confirm Password') }}</label>
                                            <input id="password-confirm" type="password" class="form-control cinematic-input" name="password_confirmation" required placeholder="Repeat password">
                                        </div>
                                    </div>
                                </div>

                                @if(env('NOCAPTCHA_ENABLE', false))
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

                                @if(config('settings.agree_terms', false))
                                    <div class="form-group mb-4">
                                        <div class="form-check cinematic-checkbox">
                                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="terms" style="color: #dfcdb8;">
                                                I agree to the <a href="#" target="_blank" class="cinematic-link" style="color: #c9a05b; text-decoration: underline;">terms and conditions</a>
                                            </label>
                                            @error('terms')
                                                <span class="invalid-feedback d-block mt-2" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group mb-0 text-center mt-5">
                                    <button type="submit" class="btn cinematic-btn-primary w-100 text-uppercase fw-bold py-3 fs-5">
                                        {{ __('Create Account') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-danger cinematic-alert text-center fw-bold mt-4" style="background-color: rgba(139, 0, 0, 0.4); border: 1px solid #ff3333; color: #ffcccc;" role="alert">
                                {{ __('Register page is disabled!') }}
                            </div>
                        @endif
                        
                        <div class="mt-4 pt-4 border-top text-center" style="border-color: rgba(201, 160, 91, 0.15) !important;">
                            <p class="mb-0" style="color: #dfcdb8;">Already have an account? <a href="{{ route('login') }}" class="cinematic-link fw-bold" style="color: #ffdc87; text-decoration: none;">Login Here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"></script>
    <script>
        FingerprintJS.load().then(fp => {
            fp.get().then(result => {
                const form = document.querySelector('form[action*="register"]');

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'fingerprint';
                input.value = result.visitorId;
                form.appendChild(input);

                const invite = new URLSearchParams(window.location.search).get('invite');
                if (invite) {
                    const inviteInput = document.createElement('input');
                    inviteInput.type = 'hidden';
                    inviteInput.name = 'invite';
                    inviteInput.value = invite;
                    form.appendChild(inviteInput);
                }
            });
        });
    </script>
@endpush
