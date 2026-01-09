@extends('layouts.full')
@section('title', __('Ranking'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="card border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div>
                                    <h2>
                                        @if(isset($data->Crest))
                                            <img src="{{ route('ranking.guild.crest', ['bin' => $data->Crest]) }}" alt="" width="32" height="32">
                                        @endif
                                        {{ $data->Name }}
                                    </h2>
                                    <p class="m-0">{{ __('Foundation Date:') }} <span class="">{{ date('d-m-Y', strtotime($data->FoundationDate)) }}</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row justify-content-end text-center">
                                <div class="col-md-3">
                                    <ul class="list-unstyled mt-3">
                                        <li class="mb-2"><h4>{{ $data->LeaderName }}</h4></li>
                                        <li class="mb-2">{{ __('Leader') }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <ul class="list-unstyled mt-3">
                                        <li class="mb-2"><h4>{{ $data->ItemPoints }}</h4></li>
                                        <li class="mb-2">{{ __('Item Points') }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <ul class="list-unstyled mt-3">
                                        <li class="mb-2"><h4>{{ $data->Lvl }}</h4></li>
                                        <li class="mb-2">{{ __('Level') }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <ul class="list-unstyled mt-3">
                                        <li class="mb-2"><h4>{{ $data->TotalMember }}</h4></li>
                                        <li class="mb-2">{{ __('Members') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('ranking.guild.partials.guild-members')

                    <div class="mt-4">
                        @include('ranking.guild.partials.guild-alliances')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
