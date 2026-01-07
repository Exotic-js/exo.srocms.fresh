<div class="row mb-3">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-end">
        {{ __('Verification Code') }}
    </label>

    <div class="col-md-6">
        <input id="{{ $name }}" type="text" class="form-control @error($name) is-invalid @enderror" name="{{ $name }}" value="{{ old($name) }}" required>

        @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror

        <div class="mt-2">
            <p class="mb-0">
                <button form="send-verify-code" class="btn btn-link p-0" onclick="document.getElementById('verify-context').value='{{ $name }}'">
                    {{ __('Send Verification code') }}
                </button>
            </p>

            @if(session('verify_code_sent') === $name)
                <span>Verification Code sent to your current email.</span>
            @endif
        </div>
    </div>
</div>
