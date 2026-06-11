@if(config('widgets.server_info.enabled'))
    <div class="card widget-serverinfo mb-5">
        <div class="card-header">
            <img src="{{ asset('themes/global/assets/images/widget-icon-serverinfo.png') }}" alt="" height="31">
            <h3>{{ __('Server Info') }}</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @foreach(config('widgets.server_info')['data'] as $row)
                    <li>
                        <span>
                            {!! $row['icon'] !!}
                            <b>{{ $row['name'] }}</b>
                        </span>
                        <span>{{ $row['value'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
