@if(config('ranking.extra.character_job_kill', false))
<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Killer') }}</th>
                <th scope="col">{{ __('Dead') }}</th>
                <th scope="col">{{ __('Time') }}</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
@endif
