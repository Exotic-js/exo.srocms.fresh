<div class="row mb-3">
    @if(isset($name) && $name == 'current_password')
        <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Current Password') }}</label>
    @else
        <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
    @endif

    <div class="col-md-6">
        @if(isset($name) && $name == 'current_password')
            <input id="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" name="current_password" required autocomplete="current-password">
        @else
            <input id="password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" name="password" required>
        @endif

        @error('current_password', 'updatePassword')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
