@if(config('widgets.top_player.enabled'))
    <div class="card widget-ranking mb-5">
        <div class="card-header">
            <img src="{{ asset('themes/global/assets/images/widget-icon-ranking.png') }}" alt="" height="31">
            <h3>{{ __('Game Ranking') }}</h3>
        </div>
        <div class="card-body">
            <div>
                <nav>
                    <div class="nav nav-tabs d-flex flex-nowrap w-100 border-0 mb-3" id="nav-tab" role="tablist">
                        <button class="nav-link active flex-fill golden-tab text-center" id="nav-player-tab" data-bs-toggle="tab" data-bs-target="#nav-player" type="button" role="tab" aria-controls="nav-player" aria-selected="true">{{ __('Player') }}</button>
                        <button class="nav-link flex-fill golden-tab text-center mx-1" id="nav-guild-tab" data-bs-toggle="tab" data-bs-target="#nav-guild" type="button" role="tab" aria-controls="nav-guild" aria-selected="false">{{ __('Guild') }}</button>
                        <button class="nav-link flex-fill golden-tab text-center" id="nav-unique-tab" data-bs-toggle="tab" data-bs-target="#nav-unique" type="button" role="tab" aria-controls="nav-unique" aria-selected="false">{{ __('Unique') }}</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-player" role="tabpanel" aria-labelledby="nav-player-tab">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col">{{ __('Rank') }}</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Points') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($topPlayer as $key => $row)
                                    <tr>
                                        <td>
                                            @if($key < 3)
                                                <img src="{{ asset(config('ranking.top_image')[$key + 1]) }}" alt=""/>
                                            @else
                                                {{ $key + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->CharName16)
                                                <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $row->ItemPoints }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">{{ __('No Records Found!') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-guild" role="tabpanel" aria-labelledby="nav-guild-tab">
                        @include('partials.top-guild')
                    </div>
                    <div class="tab-pane fade" id="nav-unique" role="tabpanel" aria-labelledby="nav-unique-tab">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col">{{ __('Unique') }}</th>
                                    <th scope="col">{{ __('Killed by') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(config('widgets.unique_history.enabled') && isset($uniqueHistory))
                                    @forelse($uniqueHistory as $row)
                                        <tr>
                                            <td>
                                                @if(isset(config('ranking.uniques')[$row->Value]['name']))
                                                    {{ config('ranking.uniques')[$row->Value]['name'] }}
                                                @else
                                                    {{ $row->Value }}
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($row->CharName16))
                                                    <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>
                                                @else
                                                    <span class="text-muted">{{ __('None') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">{{ __('No Records Found!') }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center">{{ __('No Records Found!') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
