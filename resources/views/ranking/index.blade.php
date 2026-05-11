@extends('layouts.full')
@section('title', __('Ranking'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="d-block text-center my-4">
                    @foreach($config as $item)
                        @if($item->enabled)
                            <button class="btn btn-primary btn-lg border-0 me-1 mb-2 {{ $loop->first ? 'active' : '' }}" data-link="{{ is_array($item->route)? route($item->route['name'], $item->route['params'] ?? []): route($item->route) }}">
                                {{ __($item->name) }}
                            </button>
                        @endif
                    @endforeach
                </div>
                <div id="content-ranking">
                    @if(request()->filled('search') || request()->filled('type'))
                        @if($type == 'guild')
                            @include('ranking.ranking.guild')
                        @else
                            @include('ranking.ranking.player')
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        </div>
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

            const params = new URLSearchParams(window.location.search);
            const hasRankingQuery = params.has('type') || params.has('search');

            if (!hasRankingQuery) {
                const $defaultButton = $('[data-link]').first();
                if ($defaultButton.length) {
                    $defaultButton.trigger('click');
                } else {
                    $('#content-ranking').html('<div class="alert alert-warning text-center">No ranking is enabled.</div>');
                }
            }
        });
    </script>
@endpush
