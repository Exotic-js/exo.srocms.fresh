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
                        @forelse($data as $row)
                            <tr>
                                <td>
                                    @if(config('global.server.version') === 'vSRO')
                                        <span class="">{{ $row->OrderNumber }}</span>
                                    @else
                                        @if($row->PTInvoiceID)
                                            <img src="{{ asset('images/webmall/'.$row->CPItemCode.'.jpg') }}" alt="" width="32" height="32" class="">
                                            {{ $row->CPItemName }}
                                        @elseif($row->ChangedSilk == 0 && $row->RemainedSilk > 0)
                                            <span class="text-success">{{ __('Add Silk') }}</span>
                                        @else
                                            <span class="">{{ __('NoName') }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td style="color: orange">{{ $row->RemainedSilk ?? $row->Silk_Offset }}</td>
                                <td style="color: orangered">{{ $row->ChangedSilk ?? $row->Silk_Remain }}</td>
                                <td>{{ ($row->SilkType == 3) ? __('Premium') : __('Normal') }}</td>
                                <td>
                                    @if(config('global.server.version') === 'vSRO')
                                        {{ \Carbon\Carbon::make($row->RegDate)->diffForHumans() }}
                                    @else
                                        {{ \Carbon\Carbon::make($row->ChangeDate)->diffForHumans() }}
                                    @endif
                                </td>
                                <td>{{ ($row->AvailableStatus == 'Y') ? __('Available') : __('Not Available') }}</td>
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
