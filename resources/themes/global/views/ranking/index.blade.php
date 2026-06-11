@extends('layouts.full')
@section('title', __('Ranking'))
@section('hero')
    <header class="golden-page-hero"
        style="background-image: url('{{ asset('themes/global/assets/images/ranking-bg.png') }}'); background-size: cover; background-position: center top;">
        
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
@stop
@section('content')
    <div class="golden-ranking-wrapper mb-5">
        <!-- Header Ornament -->
        <div class="ranking-header-ornament">
            <div class="line"></div>
            <div class="diamond"></div>
            <div class="line"></div>
        </div>

        <div class="row mt-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="ranking-sidebar">
                    @foreach($config as $item)
                        @if($item->enabled)
                            <div class="mb-2">
                                <button class="btn-ranking-nav {{ $item->route === 'ranking.player' ? 'active' : '' }}" data-link="{{ is_array($item->route)? route($item->route['name'], $item->route['params'] ?? []): route($item->route) }}">
                                    {{ __($item->name) }}
                                </button>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Table Content -->
            <div class="col-lg-9 col-md-8">
                <div id="content-ranking" class="ranking-table-container">
                    @if($type == 'guild')
                        @include('ranking.ranking.guild')
                    @else
                        @include('ranking.ranking.player')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('[data-link]').on('click', function (e) {
                e.preventDefault();
                let link = $(this).data('link');

                $('[data-link]').removeClass('active');
                $(this).addClass('active');

                $('#content-ranking').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');

                $.get(link, function(res){
                    $('#content-ranking').html(res);
                }).fail(() => {
                    $('#content-ranking').html('<div class="alert alert-danger text-center">Failed to load ranking.</div>');
                });
            });
        });
    </script>
@endpush
