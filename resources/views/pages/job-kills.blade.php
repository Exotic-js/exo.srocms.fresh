@extends('layouts.full')
@section('title', __('Job Kills'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('Killer Name') }}</th>
                            <th scope="col">{{ __('Dead Name') }}</th>
                            <th scope="col">{{ __('Date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $value)
                            <tr>
                                <td>
                                    @if(!empty($value->KillerCharName))
                                        <a href="{{ route('ranking.character.view', ['name' => $value->KillerCharName]) }}" class="text-decoration-none">{{ $value->KillerCharName }}</a>
                                    @else
                                        <span>{{ __('NoName') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($value->DeadCharName))
                                        <a href="{{ route('ranking.character.view', ['name' => $value->DeadCharName]) }}" class="text-decoration-none">{{ $value->DeadCharName }}</a>
                                    @else
                                        <span>{{ __('NoName') }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::make($value->EventTime)->diffForHumans() }}</td>
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
