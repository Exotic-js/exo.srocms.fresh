@extends('layouts.app')
@section('title', __('Silk History'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('ItemName') }}</th>
                            <th scope="col">{{ __('Remained Silk') }}</th>
                            <th scope="col">{{ __('Changed Silk') }}</th>
                            <th scope="col">{{ __('Silk Type') }}</th>
                            <th scope="col">{{ __('Date') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $value)
                            <tr>
                                <td>
                                    @if(config('global.server.version') === 'vSRO')
                                        <span class="">{{ $value->OrderNumber }}</span>
                                    @else
                                        @if($value->PTInvoiceID)
                                            <img src="{{ asset('images/webmall/' . $value->CPItemCode . '.jpg') }}" alt="" width="32" height="32" class="">
                                            {{ $value->CPItemName }}
                                        @elseif($value->ChangedSilk == 0 && $value->RemainedSilk > 0)
                                            <span class="text-success">{{ __('Add Silk') }}</span>
                                        @else
                                            <span class="">{{ __('NoName') }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td style="color: orange">{{ $value->RemainedSilk ?? $value->Silk_Offset }}</td>
                                <td style="color: orangered">{{ $value->ChangedSilk ?? $value->Silk_Remain }}</td>
                                <td>{{ ($value->SilkType == 3) ? __('Premium') : __('Normal') }}</td>
                                <td>
                                    @if(config('global.server.version') === 'vSRO')
                                        {{ \Carbon\Carbon::make($value->RegDate)->diffForHumans() }}
                                    @else
                                        {{ \Carbon\Carbon::make($value->ChangeDate)->diffForHumans() }}
                                    @endif
                                </td>
                                <td>{{ ($value->AvailableStatus == 'Y') ? __('Available') : __('Not Available') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('No Records Found!') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $data->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
