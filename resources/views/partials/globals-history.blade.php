@if(config('widgets.globals_history.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Global History') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @forelse($globalsHistory as $row)
                    <li class="mb-3">
                        <p class="mb-0">{!! $row->Comment !!}</p>
                        <small>
                            {{ __('Sent by:') }}
                            @if(!empty($row->CharName))
                                <a href="{{ route('ranking.character.view', ['name' => $row->CharName]) }}" class="text-decoration-none">{{ $row->CharName }}</a>
                            @else
                                <span>{{ __('NoName') }}</span>
                            @endif
                            {{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}
                        </small>
                    </li>
                @empty
                    <p class="text-center">{{ __('No Records Found!') }}</p>
                @endforelse
            </ul>
        </div>
    </div>
@endif
