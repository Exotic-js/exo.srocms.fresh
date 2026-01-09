@extends('layouts.full')
@section('title', __('Sox Drop'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('ItemName') }}</th>
                            <th scope="col">{{ __('Degree') }}</th>
                            <th scope="col">{{ __('MobName') }}</th>
                            <th scope="col">{{ __('Character') }}</th>
                            <th scope="col">{{ __('Date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td>
                                    <img src="{{ asset('images/sro/'.$row->AssocFileIcon128.'.png') }}" alt="" width="32" height="32" class="">
                                    {{ $row->RealName ?? $row->ENG }}
                                </td>
                                <td>
                                    {{ $row->Degree }} degrees
                                </td>
                                <td>
                                    <!-- TODO: getting mob real name-->
                                    {{ $row->MobCode }}
                                </td>
                                <td>
                                    @if(!empty($row->CharName16))
                                        <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>
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
