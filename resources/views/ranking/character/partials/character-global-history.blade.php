@if(config('widgets.globals_history.enabled'))
<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Message') }}</th>
                <th scope="col">{{ __('Time') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data->globalHistory as $row)
                <tr>
                    <td>{!! $row->Comment !!}</td>
                    <td>{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
