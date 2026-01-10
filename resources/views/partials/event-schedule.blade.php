@if(config('widgets.event_schedule.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Event Schedule') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @foreach($eventSchedule as $row)
                    <li>
                        <span>{{ $row->name }}</span>
                        <span class="float-end">
                            @if($row->status)
                                <span class="text-success">{{ __('Active') }}</span>
                            @else
                                <span class="timerCountdown" id="idTimeCountdown_{{ $row->idx }}" data-time="{{ $row->timestamp }}"></span>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
