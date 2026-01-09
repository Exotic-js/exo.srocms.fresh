@isset ($eventScheduleConfig['enabled'])
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Event Schedule') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @php $i = 0; @endphp
                @foreach($eventSchedule as $value)
                    <li>
                        <span>{{ $value->name }}</span>
                        <span class="float-end">
                            @if($value->status)
                                <span class="text-success">{{ __('Active') }}</span>
                            @else
                                <span class="timerCountdown" id="idTimeCountdown_{{ $i }}" data-time="{{ $value->timestamp }}"></span>
                            @endif
                        </span>
                    </li>
                    @php $i++; @endphp
                @endforeach
            </ul>
        </div>
    </div>
@endisset
