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
            @foreach($data as $key => $row)
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="d-flex overflow-hidden align-items-center justify-content-center mb-2">
                                <img class="object-fit-cover rounded border" src="{{ $row->image }}" alt="" style="min-width: 90px; min-height: 50px;"/>
                            </div>
                            <p class="text-white mb-0">{{ $row->name }}</p>
                            <p class="text-muted mb-0">{{ __('Reward:') }} {{ $row->reward }} Silk</p>
                            <p class="text-muted mb-2">{{ __('Timeout:') }} {{ $row->timeout }} Hours</p>

                            @if(!$row->enabled)
                                {{ __('Disabled') }}
                            @elseif($row->expire)
                                {{ __('Wait until') }} {{ $row->expire?->format('Y-m-d H:i') }}
                            @else
                                <a href="#" target="_blank" class="btn btn-primary vote-btn" data-site="{{ $key }}" data-url="{{ route('profile.vote.voting', $key) }}">Vote Now</a>
                            @endif
                        </div>
                    </div>
                </div>
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

            document.querySelectorAll('.vote-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const url = btn.dataset.url + '?fingerprint=' + fingerprint;
                    window.open(url, '_blank');
                });
            });
        })();
    </script>
@endpush
