@if(config('widgets.unique_history.enabled'))
<div class="col-12 mb-3">
    <div class="card widget-unique-history">
        <div class="card-header">
            <img src="{{ asset('themes/global/assets/images/widget-icon-unique.png') }}" alt="" height="31">
            <h3>Last Unique Kills</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0">
                @forelse($data->uniqueHistory as $row)
                <li>
                    <h4 class="mb-0">
                        <img src="{{ asset('themes/global/assets/images/char-icon-boss.png') }}" alt="" height="20">
                        {{ config('ranking.uniques')[$row->Value]['name'] }}
                    </h4>
                    <p class="mb-0">
                        <img src="{{ asset('themes/global/assets/images/char-icon-calender.png') }}" alt="" height="20">
                        {{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}
                    </p>
                </li>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">{{ __('No Records Found!') }}</td>
                    </tr>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endif
