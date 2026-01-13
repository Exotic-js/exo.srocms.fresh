@extends('layouts.guest')
@section('title', __('Register'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mt-5">{{ __('Register') }}</h2>

                @if (!config('settings.disable_register', false))
                    <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group row mb-3">
                        <label for="username" class="col-md-12 col-form-label text-md-left">{{ __('Username') }}</label>

                        <div class="col-md-12">
                            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required>

                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="email" class="col-md-12 col-form-label text-md-left">{{ __('Email') }}</label>

                        <div class="col-md-12">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="password" class="col-md-12 col-form-label text-md-left">{{ __('Password') }}</label>

                        <div class="col-md-12">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="password-confirm" class="col-md-12 col-form-label text-md-left">{{ __('Confirm Password') }}</label>

                        <div class="col-md-12">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>

                    @if(env('NOCAPTCHA_ENABLE', false))
                        <!-- google recaptch -->
                        <div class="form-group row mb-3">
                            <div class="col-md-12">
                                {!! NoCaptcha::renderJs() !!}
                                {!! NoCaptcha::display() !!}
                                @error('g-recaptcha-response')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    @endif

                    @if(config('settings.agree_terms', false))
                        <div class="form-check mb-3">
                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }}>

                            <label class="form-check-label" for="terms">
                                I agree to the <a href="{{ route('page.show', ['slug' => 'terms']) }}" target="_blank">terms and conditions</a>
                            </label>

                            @error('terms')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group row mb-0">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Register') }}
                            </button>
                            <a class="btn btn-link" href="{{ route('login') }}">
                                {{ __('Already registered?') }}
                            </a>
                        </div>
                    </div>
                </form>
                @else
                    <div class="alert alert-danger text-center" role="alert">
                        {{ __('Register page is disabled!') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
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
