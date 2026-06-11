@if(config('widgets.globals_history.enabled'))
    <div class="card widget-unique widget-global mb-5">
        <div class="card-header">
            <img src="{{ asset('themes/global/assets/images/widget-icon-global.png') }}" alt="" height="31">
            <h3>{{ __('Global History') }}</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @forelse($globalsHistory as $row)
                    <li>
                        <div class="unique-image">
                            @if($row->RefObjID)
                                <img src="{{ asset('themes/global/assets/images/characters/'.$row->RefObjID.'.png') }}" alt="" height="60">
                            @else
                                <img src="{{ asset('themes/global/assets/images/characters/1924.png') }}" alt="" height="60">
                            @endif
                        </div>
                        <div class="unique-content">
                            <h4>
                                @if(!empty($row->CharName))
                                    <a href="{{ route('ranking.character.view', ['name' => $row->CharName]) }}" class="text-decoration-none">{{ $row->CharName }}</a>
                                @else
                                    <span>{{ __('NoName') }}</span>
                                @endif
                            </h4>
                            <p class="unique-killer">{!! $row->Comment !!}</p>
                        </div>
                    </li>
                @empty
                    <p class="text-center">{{ __('No Records Found!') }}</p>
                @endforelse
            </ul>
        </div>
    </div>
@endif

