<img src="{{ asset('/images/com_itemsign.png') }}" class="img-clear" style="display: inline-block" alt="">

@if(in_array((int) $item->TypeID2, [4], true))
    <span style="color:#50cecd;font-weight: bold;margin-left: 20px">
        {{ $item->ItemName }} {{ ($item->OptLevel > 0) ? '(+' . $item->OptLevel . ')' : '' }}
    </span>
@elseif($item->SoxType != 'Normal' || $item->BlueInfo->isNotEmpty())
    <span style="color:#{{ $item->SoxType != 'Normal' ? 'f2e43d' : '50cecd' }};font-weight: bold;margin-left: 20px">
        {{ $item->ItemName }} {{ (($item->OptLevel + $item->nOptValue) > 0) ? '(+' . ($item->OptLevel + $item->nOptValue) . ')' : '' }}
    </span>
@else
    <span style="font-weight: bold;margin-left: 20px">
        {{ $item->ItemName }}
    </span>
@endif
<br /><br />

@if($item->SoxType != 'Normal' && !in_array((int) $item->TypeID2, [4], true))
    <b style="color:#f2e43d;">{{ $item->SoxType }}</b><br />
@endif

@if($item->SoxName && !in_array((int) $item->TypeID2, [4], true))
    <span style="color:#53EE92;font-weight: bold;">{{ $item->SoxName }}</span><br />
@endif

<span style="color:#efdaa4;">
    @isset($item->Type)
        {{ __('Sort of item:') }} {{ $item->Type }}<br />
    @endisset

    @if(in_array((int) $item->TypeID2, [1], true) && !in_array((int) $item->TypeID3, [13, 14], true))
        @isset($item->Detail)
            {{ __('Mounting part:') }} {{ $item->Detail }}<br />
        @endisset
        @if(in_array((int) $item->TypeID2, [4], true))
            @isset($item->Degree)
                {{ __('Level:') }} {{ $item->JobDegree }}<br />
            @endisset
        @elseif(!in_array((int) $item->TypeID2, [3], true))
            @isset($item->Degree)
                {{ __('Degree: :degree degrees', ['degree' => $item->Degree]) }}<br />
            @endisset
        @endif

        <br />
    @endif
</span>

@if(!config("settings.item_stats_jid_{$data->jid}") || auth()->user()?->role?->is_admin)
    @if($item->WhiteInfo)
        @foreach($item->WhiteInfo as $sWhites)
            @if(!empty($sWhites))
                {{ $sWhites }}<br />
            @endif
        @endforeach
        <br />
    @endif
@endif

@if($item->ReqLevel1)
    @if(in_array((int) $item->TypeID2, [4], true))
        {{ __('Job level:') }} {{ $item->ReqLevel1 }}<br />
    @else
        @if(config('global.server.version') === 'vSRO')
            {{ __('Reqiure level:') }} {{ $item->ReqLevel1 }}<br />
        @else
            @if(in_array((int) $item->TypeID3, [9, 10], true))
                @if($item->ReqLevelType1 > 1)
                    {{ __('Mastery level:') }} {{ config('ranking.skill_mastery')[$item->ReqLevelType1]['name'] }} {{ __('Mastery') }} {{ $item->ReqLevel1 }}<br />
                @endif
                @if($item->ReqLevelType2 > 1)
                    {{ __('Mastery level:') }} {{ config('ranking.skill_mastery')[$item->ReqLevelType2]['name'] }} {{ __('Mastery') }} {{ $item->ReqLevel2 }}<br />
                @endif
                @if($item->ReqLevelType3 > 1)
                    {{ __('Mastery level:') }} {{ config('ranking.skill_mastery')[$item->ReqLevelType3]['name'] }} {{ __('Mastery') }} {{ $item->ReqLevel3 }}<br />
                @endif
                @if($item->ReqLevelType4 > 1)
                    {{ __('Mastery level:') }} {{ config('ranking.skill_mastery')[$item->ReqLevelType4]['name'] }} {{ __('Mastery') }} {{ $item->ReqLevel4 }}<br />
                @endif
            @else
                {{ __('Reqiure level:') }} {{ $item->ReqLevel1 }}<br />
            @endif
        @endif
    @endif
@endif

@if(in_array((int) $item->TypeID2, [1], true) && in_array((int) $item->TypeID3, [1, 9, 10], true))
    @isset($item->Gender)
        {{ $item->Gender }}<br />
    @endisset
@endif

@if(!in_array((int) $item->TypeID2, [3, 4], true) && !in_array((int) $item->TypeID3, [13, 14], true))
    @isset($item->Country)
        {{ $item->Country }}<br />
    @endisset
@endif

@if(in_array((int) $item->TypeID3, [13, 14], true) || in_array((int) $item->TypeID2, [3], true))
    @if($item->TypeID3 == 14)
        {{ __('Basic stats (HP/MP) increase when equipped.  Also, upon awakening the bracelet and activating it, the outer appearance becomes extravagant and divine power becomes available to the wearer for a time.') }}
        <br /><br />
        {{ __('When awakened, the Awakening Time is counted down.') }}
    @elseif($item->TypeID3 == 13 && $item->TypeID4 == 4 && $item->MaxMagicOptCount == 0)
        {{ __('Flag with enormous, magnificent dragon pattern engraved. Can be equipped in the job slot.') }}<br />
    @elseif($item->TypeID3 == 13)
        {{ $item->ItemDesc ?? 'Dress worn by undefined' }} <br />
    @elseif(($item->TypeID3 == 4 && $item->TypeID4 == 1) || ($item->TypeID3 == 4 && $item->TypeID4 == 2))
        {{ $item->ItemDesc ?? 'A quiver of 10,000 arrows.' }} <br />
    @else
        {{ $item->ItemDesc }} <br />
    @endif
    <br />
@endif

@if(!in_array((int) $item->TypeID2, [4], true) && in_array((int) $item->TypeID3, [13], true) && in_array((int) $item->TypeID4, [2], true))
    <span style="color:#efdaa4;">{{ __('Attachment:') }} {{ $item->ChildItemCount == 1 ? 'Able to equip' : 'Unable to equip' }}</span><br />
@endif

@if(in_array((int) $item->TypeID2, [4], true) || in_array((int) $item->TypeID3, [13], true))
    <span style="color:#efdaa4;">{{ __('Max. no. of magic options: :unit Unit', ['unit' => $item->MaxMagicOptCount]) }}</span><br />
@endif

@if(config('global.server.version') === 'vSRO' && !in_array((int) $item->TypeID3, [13, 14], true))
    <span style="color:#efdaa4;">{{ __('Max. no. of magic options: :unit Unit', ['unit' => $item->MaxMagicOptCount]) }}</span><br />
@endif

@if(in_array((int) $item->TypeID3, [13], true) && $item->MaxMagicOptCount != 0)
    @isset($item->Gender)
        <br />{{ $item->Gender }}<br />
    @endisset
@endif

@if(in_array((int) $item->TypeID3, [14], true))
    <br />
    <span style="color:#efdaa4;">{{ __('Basic Option') }}</span><br />
    {{ __('MaximumHP :max% Increase', ['max' => $item->DevilMaxHP]) }}<br />
    {{ __('MaximumMP :max% Increase', ['max' => $item->DevilMaxHP]) }}<br />
    <br />
@endif

@if(!config("settings.item_stats_jid_{$data->jid}") || auth()->user()?->role?->is_admin)
    @if(!in_array((int) $item->TypeID2, [4], true) && !in_array((int) $item->TypeID3, [13, 14], true))
        @if($item->MagParam1 >= 4611686018427387904)
            <span style="color:#ff2f51;">{{ __('You may not use normal Magic Stone') }}</span><br />
            @php
                $STR = collect($item->BlueInfo)->where('code', 'MATTR_STR')->sum('value');
                $INT = collect($item->BlueInfo)->where('code', 'MATTR_INT')->sum('value');
            @endphp

            @if($item->BlueInfo)
                <span style="color:#efdaa4;">{{ __('Wheels Count:') }} [{{ intval($item->MagParamNum - 1) }}]</span><br />
                <span style="color:#efdaa4;">{{ __('STR Count:') }} [{{ $STR }}]</span><br />
                <span style="color:#efdaa4;">{{ __('INT Count:') }} [{{ $INT }}]</span><br />
            @endif
        @endif
    @endif

    @if($item->BlueInfo)
        <br />
        @foreach($item->BlueInfo as $row)
            <b style="color:#{{ ($row->code === 'MATTR_DEC_MAXDUR' || ($row->code === 'MATTR_DUR' && $row->value == 400)) ? 'ff2f51' : '50cecd' }}">
                {{ $row->name }}

                @if($row->mValue > 0)
                    (+{{ max(0, ceil((($row->value - 1) / ($row->mValue - 1)) * 100)) }}%)
                @endif
            </b><br />
        @endforeach
    @endif
@else
    <span style="color:#ff2f51">{{ __('Item details are restricted by User') }}</span><br />
@endif

@if(!in_array((int) $item->TypeID2, [3, 4], true) && !in_array((int) $item->TypeID3, [13, 14], true))
    @if(!$item->nOptValue)
        {{ __('Able to use Advanced elixir.') }}
    @else
        <b>{{ __('Advanced elixir is in effect') }} [+{{ $item->nOptValue }}]</b>
    @endif
@endif

@if(in_array((int) $item->TypeID3, [14], true))
    <br/><span style="color:#efdaa4;font-weight:bold;">{{ __('Awaken period') }}</span><br/>
    @isset($item->TimeEnd)
        {{ $item->TimeEnd }}<br/>
    @endisset
@endif

@if(in_array((int) $item->TypeID2, [3], true))
    @if($item->Amount)
        {{ __('Quantity') }} {{ $item->Amount }}<br/>
    @endif
@endif

@if(auth()->user()?->role?->is_admin && request()->routeIs('admin.characters.*'))
    <br/><br/><span style="color:#efdaa4;">{{ __('GM info:') }}</span><br/>
    <span style="color:#ff2f51;">{{ __('ItemID:') }} {{ $item->ID64 }}</span><br/>
    <span style="color:#ff2f51;">{{ __('RefItemID:') }} {{ $item->RefItemID }}</span><br/>
    <span style="color:#ff2f51;">{{ __('Serial64:') }} {{ $item->Serial64 }}</span><br/>
@endif
