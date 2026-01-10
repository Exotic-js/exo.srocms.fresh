@if(config('widgets.fortress_war.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Fortress War') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @forelse($fortressWar as $row)
                    <li>
                        <span>
                            <img src="{{ asset(config('widgets.fortress_war')['names'][$row->FortressID]['image']) }}" alt="">
                            {{ config('widgets.fortress_war')['names'][$row->FortressID]['name'] }}
                        </span>
                        <span class="float-end">
                            @if($row->Name !== 'DummyGuild')
                                <a href="{{ route('ranking.guild.view', ['name' => $row->Name]) }}" class="text-decoration-none">{{ $row->Name }}</a>
                            @else
                                <span>{{ __('None') }}</span>
                            @endif
                        </span>
                    </li>
                @empty
                    <p class="text-center">{{ __('No Records Found!') }}</p>
                @endforelse
            </ul>
        </div>
    </div>
@endif
