<div class="container">
    <div class="col-md-12">
        <div class="d-inline-block mb-4 mx-2">
            @foreach($config as $item)
                @if($item->enabled)
                    <button class="btn btn-secondary rounded-0 me-2 mb-2 btn-sm" data-link-job="{{ route($item->route) }}">
                        {{ __($item->name) }}
                    </button>
                @endif
            @endforeach
        </div>
    </div>

    <div class="col-md-12">
        <div id="content-ranking-job">
            @include('ranking.ranking.job-all')
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('[data-link-job]').on('click', function (e) {
            e.preventDefault();
            const link = $(this).data('link-job');

            $('[data-link-job]').removeClass('selected');
            $(this).addClass('selected');

            $('#content-ranking-job').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');

            $.get(`${link}`, function (res) {
                $('#content-ranking-job').html(res);
            }).fail(function () {
                $('#content-ranking-job').html('<div class="alert alert-danger">Failed to load Job Ranking.</div>');
            });
        });
    });
</script>
