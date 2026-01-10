@if(config('widgets.server_info.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Server Info') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @foreach(config('widgets.server_info')['data'] as $row)
                    <li>
                        <span>
                            {!! $row['icon'] !!}
                            {{ $row['name'] }}
                        </span>
                        <span class="float-end">{{ $row['value'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
