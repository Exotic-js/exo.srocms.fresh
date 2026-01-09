<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('settings.site_title', 'iSRO CMS v2') }} - @yield('title')</title>
    <meta name="description" content="{{ config('settings.site_desc', 'Description') }}">
    <link rel="shortcut icon" href="{{ asset(config('settings.site_favicon', 'images/favicon.ico')) }}">

    <!-- SEO -->
    <meta property="og:url" content="{{ config('settings.site_url', 'http://localhost') }}" />
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ config('settings.site_title', 'iSRO CMS v2') }}"/>
    <meta property="og:title" content="{{ config('settings.site_title', 'iSRO CMS v2') }} - @yield('title')" />
    <meta property="og:image" content="{{ asset(config('settings.site_logo', 'images/logo.png')) }}" />
    <meta property="og:image:secure_url" content="{{ asset(config('settings.site_logo', 'images/logo.png')) }}" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://use.fontawesome.com/releases/v6.7.2/css/all.css" media="screen" rel="stylesheet" type="text/css" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- Inline Styles -->
    @stack('styles')
</head>

<body data-bs-theme="{{ config('settings.dark_mode', 'dark') }}">
@include('layouts.header')

<main>
    @section('hero')
        <div class="mb-5">
            <div class="p-5 text-center bg-body-tertiary" style="background-image: url({{ asset(config('settings.hero_background', '/images/bg.jpg')) }}) !important; background-repeat: no-repeat; background-size: cover; background-position: center;">
                <div class="container py-5">
                    <h1 class="display-5 text-white fw-bold">@yield('title')</h1>
                    <p class="col-lg-8 mx-auto lead"></p>
                </div>
            </div>
        </div>
    @show

    <div class="container">
        <div class="row">
            <div class="col-md-9">
                @yield('content')
            </div>
            <div class="col-md-3">
                @section('sidebar')
                    @include('layouts.sidebar')
                @show
            </div>
        </div>
    </div><!-- /.container -->

    @include('layouts.footer')
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
<script src="{{ asset('js/color-modes.js') }}"></script>
<script src="{{ asset('js/function.js') }}"></script>

<script type="text/javascript">
    var ServerTime = new Date( {{ now()->format('Y, n, j, G, i, s') }} );
    var iTimeStamp = {{ now()->format('U') }} - Math.round( + new Date() / 1000 );
    startClockTimer('#idTimerClock');
</script>

<!-- Inline Scripts -->
@stack('scripts')

</body>
</html>
