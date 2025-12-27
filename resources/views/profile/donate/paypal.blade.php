@section('title', __('Paypal'))

@if($data['mode'] !== 'IPN')
    @forelse($data['package'] as $value)
        <div class="card mb-2" data-name="{{ $value['name'] }}" data-price="{{ $value['price'] }}" data-currency="{{ $data['currency'] }}">
            <div class="card-body d-flex justify-content-between align-items-center">
                <strong>{{ $value['name'] }}</strong>
                <span>{{ $data['currency'] }} {{ $value['price'] }}</span>
            </div>
        </div>
    @empty
        <p class="text-muted">{{ __('No Packages Available!') }}</p>
    @endforelse
@else
    <form action="{{ $data['endpoint_ipn'] }}" method="post" id="paypalForm">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="{{ $data['business_email'] }}">
        <input type="hidden" name="currency_code" value="{{ $data['currency'] }}">
        <input type="hidden" name="notify_url" value="{{ route('webhook', ['method' => 'paypal']) }}">
        <input type="hidden" name="custom" value="{{ auth()->user()->jid }}">

        <input type="hidden" name="item_name" id="paypal_item_name">
        <input type="hidden" name="amount" id="paypal_amount">

        <div class="mb-3">
            <label class="form-label">Choose Package</label>
            <select class="form-select" id="packageSelect" required>
                <option value="" selected disabled>-- Select Package --</option>
                @foreach($data['package'] as $package)
                    <option value="{{ $package['price'] }}" data-name="{{ $package['name'] }}">
                        {{ $package['name'] }} - {{ $data['currency'] }} {{ $package['price'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Buy with PayPal
        </button>
    </form>

    <script>
        document.getElementById('packageSelect').addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];

            document.getElementById('paypal_item_name').value = selected.getAttribute('data-name');
            document.getElementById('paypal_amount').value = selected.value;
        });
    </script>
@endif
