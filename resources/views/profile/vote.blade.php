@extends('layouts.app')
@section('title', __('Vote Sites'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            @foreach($data as $key => $value)
                @if($value['enabled'])
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex overflow-hidden align-items-center justify-content-center mb-2">
                                    <img class="object-fit-cover rounded border" src="{{ $value['image'] }}" alt="" style="min-width: 90px; min-height: 50px;"/>
                                </div>
                                <p class="text-white mb-0">{{ $value['name'] }}</p>
                                <p class="text-muted mb-0">{{ __('Reward:') }} {{ $value['reward'] }} Silk</p>
                                <p class="text-muted mb-2">{{ __('Timeout:') }} {{ $value['timeout'] }} Hours</p>

                                <a href="{{ route('profile.vote.voting', $key) }}" target="_blank" class="btn btn-primary vote-btn" data-site="{{ $key }}">Vote Now</a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"></script>
    <script>
        (async () => {
            const fp = await FingerprintJS.load();
            const result = await fp.get();
            const fingerprint = result.visitorId;

            const params = new URLSearchParams(window.location.search);

            if (params.get('fingerprint') !== fingerprint) {
                params.set('fingerprint', fingerprint);
                history.replaceState({}, '', `${location.pathname}?${params}`);
                location.reload();
            }
        })();
    </script>
@endpush
