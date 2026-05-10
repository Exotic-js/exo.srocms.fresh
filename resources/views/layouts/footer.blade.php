<!-- FOOTER -->
<footer class="mt-5 border-top">
    <div class="container">
        <div class="row py-5">
            <div class="col-lg-6 mb-3 text-center text-lg-start">
                <a href="{{ url('/') }}" class="d-flex align-items-center justify-content-center justify-content-lg-start me-3 mb-2 mb-lg-0 text-white text-decoration-none" aria-label="Bootstrap">
                    <img src="{{ asset(config('settings.site_logo', 'images/logo.png')) }}" alt="" width="" height="40" class="">
                </a>
                <p class="text-body-secondary mb-0 mt-2">
                    © {{ now()->year }}
                    <a href="{{ config('settings.site_url', 'http://localhost') }}">
                        {{ config('settings.site_title', 'iSRO CMS v2') }}
                    </a>
                    - {{ __('All Rights Reserved.') }}
                </p>
                <p class="text-body-secondary">
                    Powered by <a class="link-default" href="https://github.com/m1xawy/sro-cms" target="_blank">iSRO CMS v2.5</a>
                </p>
            </div>

            <div class="col-lg-2 col-md-4 d-none d-md-block mb-3">
                <h5>{{ __('General') }}</h5>
                <ul class="nav flex-column">
                    @foreach(config('global.footer')['general'] as $row)
                    <li class="nav-item mb-2">
                        <a href="{{ $row['url'] }}" target="_blank" class="nav-link p-0 text-body-secondary">
                            {{ __($row['name']) }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-2 col-md-4 d-none d-md-block mb-3">
                <h5>{{ __('Social Media') }}</h5>
                <ul class="nav flex-column">
                    @foreach(config('global.footer')['social'] as $row)
                        <li class="nav-item mb-2">
                            <a href="{{ $row['url'] }}" target="_blank" class="nav-link p-0 text-body-secondary">
                                {!! $row['image'] !!}
                                {{ $row['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-2 col-md-4 d-none d-md-block mb-3">
                <h5>{{ __('Backlink') }}</h5>
                <ul class="nav flex-column">
                    @foreach(config('global.footer')['backlink'] as $row)
                        <li class="nav-item mb-2">
                            <a href="{{ $row['url'] }}" target="_blank" class="nav-link p-0 text-body-secondary">
                                <img src="{{ $row['image'] }}" alt="" width="50">
                                {{ $row['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</footer>
