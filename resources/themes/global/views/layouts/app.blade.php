<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" media="screen" rel="stylesheet" type="text/css" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- Theme by Exotic -->
    <link href="{{ asset('themes/global/assets/css/style-global.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/global/assets/css/custom.css') }}" rel="stylesheet">
    
    <!-- Revenor Classic Cinematic Theme -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('themes/global/assets/css/revenor.css') }}?v={{ time() }}" rel="stylesheet">

    <!-- Inline Styles -->
    @stack('styles')
</head>
<body data-bs-theme="dark">
@include('layouts.header')
<div class="clearfix"></div>

@section('hero')
    <header class="golden-page-hero" style="background-image: url('{{ asset('themes/global/assets/images/herobg.png') }}'); background-size: cover; background-position: center top;">
        
        <!-- Parallax removed as herobg provides the full image -->

        {{-- Top atmospheric shadow --}}
        <div class="golden-page-hero__top-shadow"></div>

        {{-- Immersive Fog & Mist Overlays --}}
        <div class="golden-page-hero__fog-layer-1"></div>
        <div class="golden-page-hero__fog-layer-2"></div>

        {{-- Cinematic double gradient fade --}}
        <div class="golden-page-hero__fade"></div>

        {{-- Luxury Golden Accent Line with Diamond Center --}}
        <div class="golden-hero-accent-wrap">
            <div class="golden-hero-line-left"></div>
            <div class="golden-hero-diamond">
                <div class="golden-hero-diamond-glow"></div>
            </div>
            <div class="golden-hero-line-right"></div>
            {{-- Infinite light sweep --}}
            <div class="golden-hero-sweep"></div>
        </div>
    </header>
@show


<main class="golden-main">
    @if(request()->routeIs('home'))
        <!-- Full width stacked layout for Homepage -->
        @yield('content')
    @else
        <!-- Boxed layout for Inner Pages (Ranking, Download, etc.) -->
        <div class="custom-container my-5 pb-5">
            <div class="golden-page-wrapper">
                @yield('content')
            </div>
        </div>
    @endif
</main>

@include('layouts.footer')

<script crossorigin="anonymous" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>
<script src="{{ asset('js/function.js') }}"></script>

<script type="text/javascript">
    var div = document.getElementById('parallax');
    var parallaxInstance = new Parallax(div);
</script>

<script type="text/javascript">
    var ServerTime = new Date( {{ now()->format('Y, n, j, G, i, s') }} );
    var iTimeStamp = {{ now()->format('U') }} - Math.round( + new Date() / 1000 );
    startClockTimer('#idTimerClock');
</script>

<!-- Inline Scripts -->
@stack('scripts')

</body>
</html>
