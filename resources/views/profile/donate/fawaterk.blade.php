@section('title', __('Fawaterk'))

@forelse($data['package'] as $row)
    <div class="card mb-2" data-name="{{ $row['name'] }}" data-price="{{ $row['price'] }}" data-currency="{{ $data['currency'] }}">
        <div class="card-body d-flex justify-content-between align-items-center">
            <strong>{{ $row['name'] }}</strong>
            <span>{{ $data['currency'] }} {{ $row['price'] }}</span>
        </div>
    </div>
@empty
    <p class="text-muted">{{ __('No Packages Available!') }}</p>
@endforelse
