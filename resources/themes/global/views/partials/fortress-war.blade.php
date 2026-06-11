@if(config('widgets.fortress_war.enabled'))
    @php
        $ftwClass = [
                1 => 'fortress-box-jangan',
                3 => 'fortress-box-hotan',
                4 => 'fortress-box-const',
                6 => 'fortress-box-bandit',
            ]
    @endphp

    <div class="fortress row">
        @forelse($fortressWar as $row)
            <div class="fortress-box {{ $ftwClass[$row->FortressID] ?? '' }} col-xl-2 col-lg-3 col-md-12 col-sm-12 col-12 d-none d-md-block mt-3">
                <div class="d-flex flex-column justify-content-center text-center">
                    <div class="fortress-box-image">
                        <img src="{{ asset(config('widgets.fortress_war')['names'][$row->FortressID]['image']) }}" alt="" height="24" width="24">
                    </div>
                    <h4 class="fortress-box-name">{{ config('widgets.fortress_war')['names'][$row->FortressID]['name'] }}</h4>
                    <div class="fortress-box-details d-flex flex-row justify-content-between">
                        <p>
                            @if($row->Name !== 'DummyGuild')
                                <a href="{{ route('ranking.guild.view', ['name' => $row->Name]) }}" class="text-decoration-none">{{ $row->Name }}</a>
                            @else
                                <span>{{ __('None') }}</span>
                            @endif
                        </p>
                        <p>Tax <span>{{ $row->TaxRatio }}</span></p>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    </div>
@endif

