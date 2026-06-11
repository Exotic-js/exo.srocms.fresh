@if(config('widgets.event_schedule.enabled'))
    <div class="card widget-servertimes mb-5">
        <div class="card-header">
            <img src="{{ asset('themes/global/assets/images/widget-icon-schedule.png') }}" alt="" height="31">
            <h3>{{ __('Event Schedule') }}</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @foreach($eventSchedule as $row)
                    <li>
                        <span>
                            <i class="fas fa-cubes"></i>
                            {{ $row->name }}
                        </span>
                        @if($row->status)
                            <span class="text-success">{{ __('Active') }}</span>
                        @else
                            <span class="timerCountdown" id="idTimeCountdown_{{ $row->idx }}" data-time="{{ $row->timestamp }}"></span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
