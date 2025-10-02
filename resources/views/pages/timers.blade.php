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
                            @php $i = 0; @endphp
                            @foreach($data as $value)
                                <tr>
                                    <td>{{ $value['idx'] }}</td>
                                    <td>{{ $value['name'] }}</td>
                                    <td>
                                        <span class="timerCountdown" id="idTimeCountdown_{{ $i }}" data-time="{{ $value['timestamp'] }}"></span>
                                    </td>
                                    <td>{{ Carbon\CarbonInterval::seconds($value['duration'])->cascade()->forHumans() }}</td>
                                    <td>
                                        @if($value['status'])
                                            <span class="text-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="text-warning">{{ __('Planned') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
