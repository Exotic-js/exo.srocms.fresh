@extends('layouts.full')
@section('title', __('Ranking'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="d-block text-center my-4">
                    @foreach($config as $item)
                        @if($item->enabled)
                            <button class="btn btn-primary btn-lg border-0 me-1 mb-2 {{ $item->route === 'ranking.player' ? 'active' : '' }}" data-link="{{ is_array($item->route)? route($item->route['name'], $item->route['params'] ?? []): route($item->route) }}">
                                {{ __($item->name) }}
                            </button>
                        @endif
                    @endforeach
                </div>
                <div id="content-ranking">
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
