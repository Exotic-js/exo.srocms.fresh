@extends('layouts.full')
@section('title', __('Advanced Unique Tracker'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="row">
                    @foreach($data as $key => $value)
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3>
                                        <img src="{{ asset($config[$key]['image']) }}" alt=""/>
                                        {{ $config[$key]['name'] }}
                                    </h3>
                                    <small>{{ __('Last 5 Killers') }}</small>
                                </div>
                                <div class="card-body" style="min-height: 253px;">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Character') }}</th>
                                                <th>{{ __('Points') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <p class="text-center">{{ $config[$key]['points'] }} {{ __('Points') }}</p>
                                            @php $i = 1 @endphp
                                            @foreach($value as $values)
                                                @if($i > 5) @break @endif
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>
                                                        @if(!empty($values->CharName16))
                                                        <a href="{{ route('ranking.character.view', ['name' => $values->CharName16]) }}" class="text-decoration-none">{{ $values->CharName16 }}</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @foreach($ranking as $rank)
                                                            @if($rank->CharName16 == $values->CharName16)
                                                                {{ $rank->Points ?? 0 }}
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                </tr>
                                                @php $i++ @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
