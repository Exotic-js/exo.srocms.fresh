@extends('layouts.full')
@section('title', __('Event Times'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Event Name') }}</th>
                                <th>{{ __('Remaining Time') }}</th>
                                <th>{{ __('Duration') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                                <tr>
                                    <td>{{ $row->idx }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>
                                        <span class="timerCountdown" id="idTimeCountdown_{{ $row->idx }}" data-time="{{ $row->timestamp }}"></span>
                                    </td>
                                    <td>{{ Carbon\CarbonInterval::seconds($row->duration)->cascade()->forHumans() }}</td>
                                    <td>
                                        @if($row->status)
                                            <span class="text-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="text-warning">{{ __('Planned') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
