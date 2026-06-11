@section('title', __('HipoCard'))

<div class="cinematic-auth-box p-4 mb-5 mx-auto w-100" style="background: rgba(10, 8, 5, 0.5); border: 1px solid rgba(201,160,91,0.3); border-radius: 4px; box-shadow: inset 0 0 20px rgba(0,0,0,0.8);">
    <h5 class="text-center mb-4" style="color: #ebd197; text-transform: uppercase; border-bottom: 1px solid rgba(201,160,91,0.2); padding-bottom: 15px;">
        <i class="fas fa-credit-card me-2"></i>{{ __('Enter your e-pin card') }}
    </h5>
    <form method="post" action="{{ route('profile.donate.process', ['method' => $data['route']]) }}">
        @csrf

        <div class="form-group mb-4">
            <label for="code" class="cinematic-label mb-2 fw-bold" style="color: #dfcdb8;">{{ __('E-Pin Code') }}</label>
            <div class="col-md-12">
                <input id="code" type="text" class="form-control cinematic-input @error('code') is-invalid @enderror" name="code" required placeholder="XXXX-XXXX-XXXX-XXXX">
                @error('code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="password" class="cinematic-label mb-2 fw-bold" style="color: #dfcdb8;">{{ __('E-Pin Password') }}</label>
            <div class="col-md-12">
                <input id="password" type="text" class="form-control cinematic-input @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-0">
            <div class="col-md-12">
                <button type="submit" class="btn cinematic-btn-primary w-100 py-3 fw-bold text-uppercase">{{ __('Verify & Submit') }}</button>
            </div>
        </div>
    </form>
</div>

@forelse($data['package'] as $row)
    <div class="card mb-2 cinematic-payment-card" data-name="{{ $row['name'] }}" data-price="{{ $row['price'] }}" data-currency="{{ $data['currency'] }}">
        <div class="card-body d-flex justify-content-between align-items-center">
            <strong>{{ $row['name'] }}</strong>
            <span>{{ $data['currency'] }} {{ $row['price'] }}</span>
        </div>
    </div>
@empty
    <p class="text-muted text-center p-3" style="border: 1px dashed rgba(201,160,91,0.2); color: #a8a095 !important;">{{ __('No Packages Available!') }}</p>
@endforelse
