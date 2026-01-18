@extends('layouts.full')
@section('title', __('Character') . ' - ' .$data->CharName16)

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="d-flex me-3 overflow-hidden align-items-center">
                                @if(config('global.server.version') === 'vSRO')
                                    <img class="object-fit-cover rounded border" src="{{ asset('images/character/'.config('ranking.character_image_vsro')[$data->RefObjID]) }}" width="100" height="100" alt=""/>
                                @else
                                    <img class="object-fit-cover rounded border" src="{{ asset('images/character/'.config('ranking.character_image')[$data->RefObjID]) }}" width="100" height="100" alt=""/>
                                @endif
                            </div>

                            <div class="mt-3">
                                <h2>{{ $data->CharName16 }}</h2>
                                <p class="m-0">{{ __('Item Points:') }} <span class="">{{ $data->ItemPoints }}</span></p>

                                @if(config('ranking.extra.character_build') && $data->buildInfo)
                                <p class="mb-0">
                                    @foreach($data->buildInfo as $key => $row)
                                        @if(isset(config('ranking.skill_mastery')[$row->MasteryID]))
                                            <span>{{ config('ranking.skill_mastery')[$row->MasteryID]['name'] }}</span> @if($key < count($data->buildInfo) - 1) / @endif
                                        @endif
                                    @endforeach
                                </p>
                                @endif
                                @if(config('ranking.extra.character_buff') && $data->buffInfo)
                                <ul class="list-unstyled d-flex">
                                    @foreach($data->buffInfo as $row)
                                        <li class="me-1">
                                            <img src="{{ asset('images/sro/'.$row->UI_IconFile_PNG) }}" title="{{ $row->UI_SkillName }}" alt="" width="24" height="24">
                                        </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mt-3 justify-content-end">
                            @if(config('ranking.extra.character_job') && $data->charJob->JobType)
                                <div class="col-md-4">
                                    <div class="d-flex">
                                        <div class="d-flex align-items-center">
                                            @if(config('global.server.version') === 'vSRO')
                                                <img src="{{ asset(config('ranking.job_type_vsro')[$data->charJob->JobType]['image']) }}" width="50" height="" alt=""/>
                                            @else
                                                <img src="{{ asset(config('ranking.job_type')[$data->charJob->JobType]['image']) }}" width="50" height="" alt=""/>
                                            @endif
                                        </div>

                                        <ul class="list-unstyled mt-3">
                                            <li class="mb-0">
                                                @if(config('global.server.version') === 'vSRO')
                                                    <span>{{ config('ranking.job_type_vsro')[$data->charJob->JobType]['name'] }}</span>
                                                @else
                                                    <span>{{ config('ranking.job_type')[$data->charJob->JobType]['name'] }}</span>
                                                @endif
                                            </li>
                                            <li class="mb-0">{{ __('Job Level:') }} <span class="">{{ $data->charJob->JobLevel ?? $data->charJob->Level }}</span></li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <ul class="list-unstyled mt-3">
                                    <li class="mb-2"><i class="fa-solid fa-heart text-danger"></i> {{ __('Health:') }} <span>{{ $data->HP }}</span></li>
                                    <li class="mb-2"><i class="fa-solid fa-star-of-life text-primary"></i> {{ __('Mana:') }} <span>{{ $data->MP }}</span></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-unstyled mt-3">
                                    <li class="mb-2"><i class="fa-solid fa-hand-fist text-warning"></i> {{ __('Strength:') }} <span>{{ $data->Strength }}</span></li>
                                    <li class="mb-2"><i class="fa-solid fa-brain text-warning"></i> {{ __('Intellect:') }} <span>{{ $data->Intellect }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button" role="tab" aria-controls="info-tab-pane" aria-selected="true">{{ __('Information') }}</button>
                            </li>
                            @if(config('widgets.globals_history.enabled'))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="globals-tab" data-bs-toggle="tab" data-bs-target="#globals-tab-pane" type="button" role="tab" aria-controls="globals-tab-pane" aria-selected="false">{{ __('Global Chat') }}</button>
                            </li>
                            @endif
                            @if(config('widgets.unique_history.enabled'))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="uniques-tab" data-bs-toggle="tab" data-bs-target="#uniques-tab-pane" type="button" role="tab" aria-controls="uniques-tab-pane" aria-selected="false">{{ __('Unique Kills') }}</button>
                            </li>
                            @endif
                            @if(config('widgets.custom.owned_titles.enabled'))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="titles-tab" data-bs-toggle="tab" data-bs-target="#titles-tab-pane" type="button" role="tab" aria-controls="titles-tab-pane" aria-selected="false">{{ __('Owned Titles') }}</button>
                            </li>
                           @endif
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                                @include('ranking.character.partials.character-information')
                            </div>
                            <div class="tab-pane fade" id="globals-tab-pane" role="tabpanel" aria-labelledby="globals-tab" tabindex="0">
                                @include('ranking.character.partials.character-global-history')
                            </div>
                            <div class="tab-pane fade" id="uniques-tab-pane" role="tabpanel" aria-labelledby="uniques-tab" tabindex="0">
                                @include('ranking.character.partials.character-unique-history')
                            </div>
                            <div class="tab-pane fade" id="titles-tab-pane" role="tabpanel" aria-labelledby="titles-tab" tabindex="0">
                                @include('partials.character-owned-titles', ['Limit' => 5, 'CharID' => $data->CharID])
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card" style="height: 345px">
                            <div class="card-body d-flex flex-column position-relative h-100" id="display-inventory">
                                <div class="position-absolute top-0 start-0 w-100 h-100 p-4 d-block" id="display-inventory-set">
                                    @include('ranking.character.partials.inventory.inventory-view', ['inventorySetList' => $data->getCharInventorySet(12, 0, 8)])
                                </div>
                                @if(config('global.server.version') !== 'vSRO')
                                    <div class="position-absolute top-0 start-0 w-100 h-100 p-4 d-none" id="display-inventory-job">
                                        @include('ranking.character.partials.inventory.inventory-job-view', ['inventoryJobList' => $data->charInventoryJob])
                                    </div>
                                @endif
                                <div class="position-absolute top-0 start-0 w-100 h-100 p-4 d-none" id="display-inventory-avatar">
                                    @include('ranking.character.partials.inventory.inventory-avatar-view', ['inventoryAvatarList' => $data->charInventoryAvatar])
                                </div>

                                @if(config('global.server.version') === 'vSRO')
                                    <img class="position-absolute top-50 start-50 translate-middle h-100 w-auto object-fit-cover z-0 pt-4" src="{{ asset('images/character_full/'.config('ranking.character_image_vsro')[$data->RefObjID]) }}" alt=""/>
                                @else
                                    <img class="position-absolute top-50 start-50 translate-middle h-100 w-auto object-fit-cover z-0 pt-4" src="{{ asset('images/character_full/'.config('ranking.character_image')[$data->RefObjID]) }}" alt=""/>
                                @endif

                                <button id="display-inventory-switch-isro" data-type="set" class="btn btn-secondary mt-auto w-auto align-self-center position-relative z-1">{{ __('Switch') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .sro-item-detail .tooltip {
            text-align: left !important;
            font-size: 12px;
            width: 300px;
            min-height: 200px;
            background-color: rgba(28, 30, 52, .8);
            color: #fff;
            padding: 6px;
            border: 1px solid #808bba;
            border-radius: 5px;
            box-shadow: none;
            z-index: 999;
        }
        .sro-item-detail .item > img {
            position: absolute;
            width: 32px;
            height: 32px;
        }
    </style>
    <style>
        .table-inventory tr td {
            background: none;
            border: none;
        }
        /*
        #display-inventory .d-block {
            display: block !important;
        }
        */
        #display-inventory .d-none {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
<script>
    jQuery('#display-inventory-switch-isro').click(function() {
        var current = jQuery(this).data('type');
        var stages = ['set'];

        @if(config('global.server.version') !== 'vSRO')
        stages.push('job');
        @endif
        stages.push('avatar');

        var currentIndex = stages.indexOf(current);
        var nextIndex = (currentIndex + 1) % stages.length;
        var change = stages[nextIndex];

        jQuery('#display-inventory-' + current).addClass('d-none');
        jQuery('#display-inventory-' + change).removeClass('d-none');

        jQuery(this).data('type', change);
    });
</script>
@endpush

