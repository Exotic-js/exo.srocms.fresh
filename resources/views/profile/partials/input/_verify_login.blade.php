<div class="row mb-3">
    <label for="verify_login" class="col-md-4 col-form-label text-md-end">{{ __('Enable Login Verification') }}</label>

    <div class="col-md-6">
        <select class="form-select @error('verify_login') is-invalid @enderror" name="verify_login" aria-label="Default select example">
            <option value="0" {{ !config("settings.verify_jid_{$user->tbUser->JID}") ? 'selected' : '' }}>
                Disabled
            </option>
            <option value="1" {{ config("settings.verify_jid_{$user->tbUser->JID}") ? 'selected' : '' }}>
                Enabled
            </option>
        </select>

        @error('verify_login')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
