@extends('layouts.full')
@section('title', __('Unique Tracker'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('Unique') }}</th>
                            <th scope="col">{{ __('Dead/Spawn Time') }}</th>
                            <th scope="col">{{ __('Killer') }}</th>
                            <th scope="col">{{ __('Area') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $key => $row)
                            <tr>
                                <td>
                                    <img src="{{ asset(config('ranking.uniques')[$row->Value]['image']) }}" alt=""/>
                                    {{ config('ranking.uniques')[$row->Value]['name'] }}
                                </td>
                                <td>{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</td>
                                <td>
                                    @if($row->CharName16 && $row['ValueCodeName128'] == 'KILL_UNIQUE_MONSTER')
                                        @if($row->RefObjID > 2000)
                                            <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                                        @else
                                            <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
                                        @endif
                                        <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>
                                    @endif
                                </td>
                                <td>{{ $row->AreaName }}</td>
                                <td>
                                    @if($row['ValueCodeName128'] == 'KILL_UNIQUE_MONSTER')
                                        <span class="text-danger">{{ __('Killed') }}</span>
                                    @elseif($row['ValueCodeName128'] == 'SPAWN_UNIQUE_MONSTER')
                                        <span class="text-success">{{ __('Spawned') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('No Records Found!') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
