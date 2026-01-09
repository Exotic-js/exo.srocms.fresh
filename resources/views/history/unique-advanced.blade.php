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
                                        <img src="{{ asset($config->uniqueList[$key]['image']) }}" alt=""/>
                                        {{ $config->uniqueList[$key]['name'] }}
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
                                        @foreach($value as $index => $kill)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ route('ranking.character.view', ['name' => $kill->CharName16]) }}" class="text-decoration-none">{{ $kill->CharName16 }}</a>
                                                </td>
                                                <td>{{ $kill->Points }}</td>
                                            </tr>
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
