@extends('layouts.app')
@section('title', __('Profile'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <h3 class="">{{ __('Characters') }}</h3>
        <div class="row">
            @if(!Auth::user()->tbUser || Auth::user()->tbUser->shardUser->isEmpty())
                <div class="alert alert-danger text-center" role="alert">
                    {{ __('No Characters Found!') }}
                </div>
            @else
                @foreach(Auth::user()->tbUser->shardUser as $value)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex overflow-hidden align-items-center justify-content-center mb-2">
                                    <img class="object-fit-cover rounded border" src="{{ asset($characterImage[$value->RefObjID]) }}" width="100" height="100" alt=""/>
                                </div>

                                @if($value->RefObjID > 2000)
                                    <img src="{{ asset($characterRace[1]['image']) }}" width="16" height="16" alt=""/>
                                @else
                                    <img src="{{ asset($characterRace[0]['image']) }}" width="16" height="16" alt=""/>
                                @endif
                                <a href="{{ route('ranking.character.view', ['name' => $value->CharName16]) }}" class="text-decoration-none">{{ $value->CharName16 }}</a>
                                <p>{{ __('Lv:') }} {{ $value->CurLevel }}</p>
                                <p>{{ __('Gold:') }} {{ number_format($value->RemainGold , 0, ',')}}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <h3 class="mt-4">{{ __('Information') }}</h3>
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        @if(!Auth::user()->tbUser)
                            <tr>
                                <td class="text-center">{{ __('Cannot load your account information!') }}</td>
                            </tr>
                        @else
                            @if(config('global.server.version') === 'vSRO')
                                <tbody>
                                <tr>
                                    <th scope="row">Username</th>
                                    <td>{{ Auth::user()->tbUser->StrUserID }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>{{ Auth::user()->tbUser->Email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Silk') }}</th>
                                    <td>{{ Auth::user()->tbUser->getSkSilk->silk_own ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Gift Silk') }}</th>
                                    <td>{{ Auth::user()->tbUser->getSkSilk->silk_gift ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Point Silk') }}</th>
                                    <td>{{ Auth::user()->tbUser->getSkSilk->silk_point ?? 0 }}</td>
                                </tr>
                                </tbody>
                            @else
                                <tbody>
                                <tr>
                                    <th scope="row">Username</th>
                                    <td>{{ Auth::user()->tbUser->StrUserID }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>{{ Auth::user()->muUser->muEmail->EmailAddr }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Silk') }}</th>
                                    @php $cash = Auth::user()->muUser->getJCash() @endphp
                                    <td>{{ $cash->Silk ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Premium Silk') }}</th>
                                    <td>{{ $cash->PremiumSilk ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Month Usage') }}</th>
                                    <td>{{ $cash->MonthUsage ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('3Month Usage') }}</th>
                                    <td>{{ $cash->ThreeMonthUsage ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('VIP') }}</th>
                                    <td>
                                        @isset(Auth::user()->muUser->muVIPInfo->VIPUserType)
                                            <img src="{{ asset($vipLevel['level'][Auth::user()->muUser->muVIPInfo->VIPLv]['image']) }}" width="24" height="24" alt="">
                                            <span>{{ $vipLevel['level'][Auth::user()->muUser->muVIPInfo->VIPLv]['name'] }}</span>
                                        @else
                                            <span>{{ __('None') }}</span>
                                        @endisset
                                    </td>
                                </tr>
                                </tbody>
                            @endif
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
