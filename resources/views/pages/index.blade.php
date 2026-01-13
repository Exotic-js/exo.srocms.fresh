@extends('layouts.full')
@section('title', __('Landing'))

@section('hero')
    <!-- Hero Section -->
    <section id="home" class="position-relative d-flex align-items-center bg-hero-pattern" style="background-image: url({{ config('global.hero.background', 'images/bg.jpg') }}) !important; background-repeat: no-repeat; background-size: cover; background-position: center;min-height: 70vh;">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-75"></div>

        <div class="container py-5 position-relative">
            <div class="row">
                <div class="col-lg-8 col-xl-7">
                    <div class="d-inline-block mb-4 px-3 py-1 border border-warning bg-black bg-opacity-25 rounded">
                        <p class="mb-0 text-warning font-cinzel small">ENTER THE LEGEND</p>
                    </div>

                    <h1 class="display-4 fw-bold text-white font-cinzel mb-4">
                        Experience the <span class="text-warning">Legend</span> of the Silkroad
                    </h1>

                    <p class="lead text-light mb-4">
                        Join thousands of players on our enhanced private server. Engage in epic PvP battles,
                        master the unique trading system, and forge your legacy on the ancient Silk Road.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 mb-4">
                        @auth
                            <a href="{{ route('profile') }}" class="btn btn-warning font-cinzel py-3 px-4">
                                Account Panel
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-warning font-cinzel py-3 px-4">
                                Create Account
                            </a>
                        @endauth
                        <a href="{{ route('download') }}" class="btn btn-outline-warning font-cinzel py-3 px-4">
                            Download Client
                        </a>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-4 mt-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle me-2 animate-pulse" style="height: 10px; width: 10px;"></div>
                            <span class="text-light font-cinzel"><span class="text-warning">{{ $onlineCounter->onlinePlayer+$onlineCounter->fakePlayer }}</span> Players Online</span>
                        </div>
                        <div class="vr bg-warning opacity-25 d-none d-sm-block" style="height: 24px;"></div>
                        <div class="text-light font-cinzel"><span class="text-warning"><span id="idTimerClock">{{ date('H:i:s') }}</span></span> Server Time</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('content')
    <!-- Stats Section -->
    <section class="py-5 bg-silk-red position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-texture opacity-10"></div>
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-25"></div>

        @php
            if (config('global.server.version') !== 'vSRO') {
                $contentConfig = collect(App\Models\SRO\Shard\ContentConfig::getContentConfig())->pluck('Value', 'CodeName128')->toArray();
            }
        @endphp

        <div class="container position-relative py-4">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3">
                    <div class="display-5 fw-bold font-cinzel text-white mb-2">{{ App\Models\SRO\Shard\Char::getCharCount() }}+</div>
                    <div class="text-silk-light font-cinzel">Total Players</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-5 fw-bold font-cinzel text-white mb-2">{{ App\Models\SRO\Account\TbUser::getTbUserCount() }}+</div>
                    <div class="text-silk-light font-cinzel">Registered Accounts</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-5 fw-bold font-cinzel text-white mb-2">{{ $contentConfig['EXP_RATIO'] ?? 1 }}x</div>
                    <div class="text-silk-light font-cinzel">EXP Rate</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-5 fw-bold font-cinzel text-white mb-2">{{ $contentConfig['DROP_ITEM_RATIO'] ?? 1 }}x</div>
                    <div class="text-silk-light font-cinzel">Drop Rate</div>
                </div>
            </div>
        </div>
    </section>

    @if(config('widgets.server_info.enabled'))
        <!-- Feature Section -->
        <section id="features" class="py-5 bg-silk-dark position-relative">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-texture opacity-10"></div>

            <div class="container position-relative py-5">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold font-cinzel mb-3">
                        Server <span class="text-warning">Features</span>
                    </h2>
                    <div class="mx-auto bg-warning mb-4" style="height: 2px; width: 80px;"></div>
                    <p class="mx-auto" style="max-width: 700px;">
                        Our private server offers an enhanced Silkroad experience with carefully balanced gameplay,
                        new content, and quality-of-life improvements.
                    </p>
                </div>

                <div class="row g-4">
                    @foreach(collect(config('widgets.server_info.data'))->take(6) as $row)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 p-4">
                                <div class="bg-warning rounded mb-4 d-flex align-items-center justify-content-center" style="height: 48px; width: 48px;">
                                    <span class="font-cinzel fw-bold">{!! $row['icon'] !!}</span>
                                </div>
                                <h3 class="font-cinzel fw-bold text-silk-gold fs-5 mb-3">{{ $row['name'] }}</h3>
                                <p class="mb-0">{{ $row['value'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(config('widgets.discord.enabled'))
        <!-- Call to Action -->
        <section class="py-5 bg-black position-relative">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-texture opacity-5"></div>

            <div class="container position-relative py-5">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold font-cinzel text-white mb-3">
                        Our <span class="text-warning">Discord</span>
                    </h2>
                    <div class="mx-auto bg-warning mb-4" style="height: 2px; width: 80px;"></div>
                    <p class="text-light mx-auto" style="max-width: 700px;">
                        Stay updated with the latest events, updates, and announcements from our server team.
                    </p>
                </div>

                <div class="row justify-content-center">
                    <widgetbot
                        server="{{ config('widgets.discord')['server_id'] }}"
                        channel="{{ config('widgets.discord')['channel_id'] }}"
                        width="100%"
                        height="600"
                    ></widgetbot>
                    <script src="https://cdn.jsdelivr.net/npm/@widgetbot/html-embed"></script>
                </div>
            </div>
        </section>
    @endif

    <!-- News Section -->
    <section id="news" class="py-5 bg-dark position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-texture opacity-5"></div>

        <div class="container position-relative py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold font-cinzel text-white mb-3">
                    Latest <span class="text-warning">News</span>
                </h2>
                <div class="mx-auto bg-warning mb-4" style="height: 2px; width: 80px;"></div>
                <p class="text-light mx-auto" style="max-width: 700px;">
                    Stay updated with the latest events, updates, and announcements from our server team.
                </p>
            </div>

            <div class="row g-4">
                @forelse($data->take(3) as $row)
                    <div class="col-lg-4">
                        <div class="card h-100">
                            @if ($row->image)
                                <img src="{{ $row->image }}" class="card-img-top" alt="..." style="height: 200px;">
                            @else
                                <div class="bg-secondary" style="height: 200px;">
                                    <div class="h-100 d-flex align-items-center justify-content-center text-white">
                                        [News Image Placeholder]
                                    </div>
                                </div>
                            @endif
                            <div class="card-body">
                                <div class="small mb-2 font-cinzel">
                                    @switch($row->category)
                                        @case('news')
                                            <span class="badge text-bg-warning">News</span>
                                            @break
                                        @case('update')
                                            <span class="badge text-bg-primary">Update</span>
                                            @break
                                        @case('event')
                                            <span class="badge text-bg-success">Event</span>
                                            @break
                                        @default
                                            <span class="badge text-bg-warning">News</span>
                                    @endswitch
                                    {{ $row->published_at->format("M j, Y") }}</div>
                                <a href="{{ route('post.show', ['slug' => $row->slug]) }}" class="text-decoration-none">
                                    <h3 class="card-title fw-bold font-cinzel h5">{{ \Illuminate\Support\Str::words(strip_tags($row->title), 3, '...') }}</h3>
                                </a>
                                <div class="card-text">
                                    {{ \Illuminate\Support\Str::words(strip_tags($row->content), 20, '...') }}
                                </div>
                                <a href="{{ route('post.show', ['slug' => $row->slug]) }}" class="text-decoration-none font-cinzel mt-4">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-danger text-center" role="alert">
                        {{ __('No Posts Available!') }}
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('news') }}" class="btn btn-outline-warning font-cinzel">
                    View All News
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-black position-relative">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-texture opacity-5"></div>

        <div class="container position-relative py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold font-cinzel text-white mb-4">
                        Begin Your <span class="text-warning">Adventure</span> on the Legendary Silkroad
                    </h2>

                    <p class="text-light mb-5">
                        Join thousands of players already enjoying our enhanced Silkroad Online experience.
                        Create your account now and become part of our thriving community.
                    </p>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mb-4">
                        @auth
                            <a href="{{ route('profile') }}" class="btn btn-warning font-cinzel py-3 px-4">
                                Account Panel
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-warning font-cinzel py-3 px-4">
                                Create Account
                            </a>
                        @endauth

                        <a href="{{ route('download') }}" class="btn btn-outline-warning font-cinzel py-3 px-4">
                            Download Client
                        </a>
                    </div>

                    <div class="text-secondary mt-3">
                        <p>Client Size: 2.1 GB • Compatible with Windows 7/8/10/11</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-cinzel {
            font-family: 'Cinzel', serif;
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
@endpush
