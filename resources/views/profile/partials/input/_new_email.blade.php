<div class="row mb-3">
    <label for="code" class="col-md-4 col-form-label text-md-end">
        {{ __('New Email') }}
    </label>

    <div class="col-md-6">
        <input id="new_email" type="email" class="form-control @error('new_email') is-invalid @enderror" name="new_email" value="{{ old('new_email', $user->new_email) }}">

        @error('new_email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
