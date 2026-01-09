@extends('layouts.full')
@section('title', __('Pvp Kills'))

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
                        @forelse($data as $row)
                            <tr>
                                <td>
                                    @if(!empty($row->KillerCharName))
                                        <a href="{{ route('ranking.character.view', ['name' => $row->KillerCharName]) }}" class="text-decoration-none">{{ $row->KillerCharName }}</a>
                                    @else
                                        <span>{{ __('NoName') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($row->DeadCharName))
                                        <a href="{{ route('ranking.character.view', ['name' => $row->DeadCharName]) }}" class="text-decoration-none">{{ $row->DeadCharName }}</a>
                                    @else
                                        <span>{{ __('NoName') }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</td>
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
