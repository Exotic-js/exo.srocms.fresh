@extends('layouts.full')
@section('title', __('Ranking'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="d-block text-center my-4">
                    @foreach($config->menu as $item)
                        @if($item->enabled)
                            <button class="btn btn-primary btn-lg border-0 me-1 mb-2 {{ $item->route === 'ranking.player' ? 'active' : '' }}" data-link="{{ is_array($item->route)? route($item->route['name'], $item->route['params'] ?? []): route($item->route) }}">
                                {{ __($item->name) }}
                            </button>
                        @endif
                    @endforeach
                </div>
                <div id="content-ranking">
                    @include($type === 'guild' ? 'ranking.ranking.guild' : 'ranking.ranking.player')
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function () {
            let currentRequest = null;

            $(document).on('click', '[data-link]', function (e) {
                e.preventDefault();
                let $btn = $(this);
                let link = $btn.data('link');

                if (currentRequest) currentRequest.abort();

                $('[data-link]').removeClass('active');
                $btn.addClass('active');

                $('#content-ranking').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');

                currentRequest = $.get(link, function(res){
                    $('#content-ranking').html(res);
                }).fail(() => {
                    $('#content-ranking').html('<div class="alert alert-danger text-center">Failed to load ranking.</div>');
                });
            });
        });
    </script>
@endpush
