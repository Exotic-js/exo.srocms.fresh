@extends('layouts.full')
@section('title', __('Global History'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">{{ __('Message') }}</th>
                                <th scope="col">{{ __('Character') }}</th>
                                <th scope="col">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>{!! $row->Comment !!}</td>
                                    <td>
                                        @if(!empty($row->CharName))
                                            <a href="{{ route('ranking.character.view', ['name' => $row->CharName]) }}" class="text-decoration-none">{{ $row->CharName }}</a>
                                        @else
                                            <span>{{ __('NoName') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</td>
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
