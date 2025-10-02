@extends('layouts.full')
@section('title', __('Fortress History'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">{{ __('Fortress') }}</th>
                                <th scope="col">{{ __('Winner') }}</th>
                                <th scope="col">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $value)
                                <tr>
                                    <td>
                                        <img src="{{ $config['names'][$value->FortressID]['image'] }}" alt="">
                                        {{ $config['names'][$value->FortressID]['name'] }}
                                    </td>
                                    <td>
                                        @if(!empty($value->strDesc))
                                            <a href="{{ route('ranking.guild.view', ['name' => $value->strDesc]) }}" class="text-decoration-none">{{ $value->strDesc }}</a>
                                        @else
                                            <span>{{ __('No Winner') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::make($value->EventTime)->diffForHumans() }}</td>
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
        </div>
    </div>
@endsection
