@if(config('widgets.globals_history.enabled'))
<div class="col-12 mb-3">
    <div class="card widget-unique widget-global mb-5">
        <div class="card-header">
            <img src="{{ asset('themes/global/assets/images/widget-icon-global.png') }}" alt="" height="31">
            <h3>Last Global Chatting</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled h-auto">
                @forelse($data->globalHistory as $row)
                <li>
                    <div class="unique-image">
                        <img src="{{ asset('themes/global/assets/images/characters/1908.png') }}" alt="" height="60">
                    </div>
                    <div class="unique-content">
                        <p class="unique-killer">{!! $row->Comment !!}</p>
                    </div>
                </li>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">{{ __('No Records Found!') }}</td>
                    </tr>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endif
