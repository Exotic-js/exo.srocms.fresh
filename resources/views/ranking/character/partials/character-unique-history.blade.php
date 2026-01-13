@if(config('widgets.unique_history.enabled'))
<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Unique Name') }}</th>
                <th scope="col">{{ __('Points') }}</th>
                <th scope="col">{{ __('Time') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data->uniqueHistory as $row)
                <tr>
                    <td>{{ config('ranking.uniques')[$row->Value]['name'] }}</td>
                    <td>+{{ config('ranking.uniques')[$row->Value]['points'] }}</td>
                    <td>{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
