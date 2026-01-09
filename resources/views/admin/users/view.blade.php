@extends('admin.layouts.app')
@section('title', __('View User'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">View User</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    @forelse($data->shardUser as $char)
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="d-flex overflow-hidden align-items-center justify-content-center mb-2">
                                        @if(config('global.server.version') === 'vSRO')
                                        <img class="object-fit-cover rounded border" src="{{ asset('images/character/'.config('ranking.character_image_vsro')[$char->RefObjID]) }}" width="100" height="100" alt=""/>
                                        @else
                                        <img class="object-fit-cover rounded border" src="{{ asset('images/character/'.config('ranking.character_image')[$char->RefObjID]) }}" width="100" height="100" alt=""/>
                                        @endif
                                    </div>

                                    @if($char->RefObjID > 2000)
                                        <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                                    @else
                                        <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
                                    @endif
                                    <a href="{{ route('admin.characters.view', $char->CharID) }}" class="text-decoration-none">{{ $char->CharName16 }}</a>

                                    <p>{{ __('Gold:') }} {{ number_format($char->RemainGold , 0, ',') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-danger">
                            <p class="text-center">No characters found.</p>
                        </div>
                    @endforelse
                </div>

                <div class="card p-0 mt-4">
                    <div class="card-header">
                        <h4 class="text-center">User Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                @if(config('global.server.version') === 'vSRO')
                                    <tbody>
                                    <tr>
                                        <th scope="row">JID</th>
                                        <td>{{ $data->JID }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Username</th>
                                        <td>{{ $data->StrUserID }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Email</th>
                                        <td>{{ $data->Email }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('Silk') }}</th>
                                        <td>{{ $data->getSkSilk->silk_own ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('Gift Silk') }}</th>
                                        <td>{{ $data->getSkSilk->silk_gift ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('Point Silk') }}</th>
                                        <td>{{ $data->getSkSilk->silk_point ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('Reg. Date') }}</th>
                                        <td>{{ $data->regtime ?? '—' }}</td>
                                    </tr>
                                    </tbody>
                                @else
                                    <tbody>
                                    <tr>
                                        <th scope="row">Portal JID</th>
                                        <td>{{ $data->PortalJID }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Username</th>
                                        <td>{{ $data->StrUserID }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Email</th>
                                        <td>{{ $data->muUser->muEmail->EmailAddr }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('Silk') }}</th>
                                        <td>{{ $data->muUser->JCash->Silk ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('Premium Silk') }}</th>
                                        <td>{{ $data->muUser->JCash->PremiumSilk ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('Month Usage') }}</th>
                                        <td>{{ $data->muUser->JCash->MonthUsage ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ __('3Month Usage') }}</th>
                                        <td>{{ $data->muUser->JCash->ThreeMonthUsage ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">VIP</th>
                                        <td>
                                            @isset($data->muUser->muVIPInfo->VIPUserType)
                                                <img src="{{ asset(config('ranking.vip_level')['level'][$data->muUser->muVIPInfo->VIPLv]['image']) }}" width="24" height="24" alt="">
                                                <span>{{ config('ranking.vip_level')['level'][$data->muUser->muVIPInfo->VIPLv]['name'] }}</span>
                                            @else
                                                <span>{{ __('None') }}</span>
                                            @endisset
                                        </td>
                                    </tr>
                                    </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card p-0 mt-4">
                    <div class="card-header">
                        <h4 class="text-center">Silk History</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Method</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                    <th>Amount (€)</th>
                                    <th>Value (Silk)</th>
                                    <th>Description</th>
                                    <th>IP</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($data->donationLogs as $row)
                                    <tr>
                                        <td>{{ $row->created_at }}</td>
                                        <td>{{ $row->method }}</td>
                                        <td>{{ $row->transaction_id }}</td>
                                        <td>{{ $row->status }}</td>
                                        <td>{{ $row->amount }}</td>
                                        <td>{{ $row->value }}</td>
                                        <td>{{ $row->desc }}</td>
                                        <td>{{ $row->ip }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No transactions found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-0">
                    <div class="card-header">
                        <h4 class="text-center">Add Silk</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.silk', $data->JID) }}">
                            @csrf
                            <div class="row mb-3">
                                <label for="amount" class="col-md-12 col-form-label text-md-start">{{ __('Silk Amount') }}</label>

                                <div class="col-md-12">
                                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>

                                    @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="type" class="col-md-12 col-form-label text-md-start">{{ __('Type') }}</label>

                                <div class="col-md-12">
                                    @if(config('global.server.version') === 'vSRO')
                                        <select class="form-select @error('type') is-invalid @enderror" name="type" aria-label="Default select example">
                                            <option value="0">Normal</option>
                                            <option value="1">Gift</option>
                                            <option value="2">Point</option>
                                        </select>
                                    @else
                                        <select class="form-select @error('type') is-invalid @enderror" name="type" aria-label="Default select example">
                                            <option value="0">Normal</option>
                                            <option value="3">Premium</option>
                                        </select>
                                    @endif

                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('Add Silk') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card p-0 mt-4">
                    <div class="card-header">
                        <h4 class="text-center">Block Account</h4>
                    </div>
                    <div class="card-body">
                        @if($data->blockedUser && \Carbon\Carbon::parse($data->blockedUser->timeEnd)->isFuture())
                            <div class="alert alert-danger py-1 px-2 mb-2 small text-center">
                                <strong>Currently banned</strong><br>
                                Reason: <em>{{ $data->activeBlock?->punishment->Guide }}</em><br>
                                {{ \Carbon\Carbon::parse($data->activeBlock?->punishment->BlockStartTime)->format('d.m.Y H:i') }}
                                –
                                {{ \Carbon\Carbon::parse($data->activeBlock?->punishment->BlockEndTime)->format('d.m.Y H:i') }}
                                ({{ \Carbon\Carbon::parse($data->activeBlock?->punishment->BlockStartTime)->diffForHumans(\Carbon\Carbon::parse($data->activeBlock?->punishment->BlockEndTime), true) }})
                            </div>

                            <form id="unblock" method="POST" action="{{ route('admin.users.unblock', $data->JID) }}">
                                @csrf
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.users.block', $data->JID) }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="reason" class="col-md-12 col-form-label text-md-start">{{ __('Reason') }}</label>

                                <div class="col-md-12">
                                    <select class="form-select @error('reason') is-invalid @enderror" name="reason" aria-label="Default select example" onchange="toggleCustomReason(this)" required>
                                        <option value="Botting">Botting</option>
                                        <option value="Insults">Insults</option>
                                        <option value="Scamming">Scamming</option>
                                        <option value="Custom">Custom</option>
                                    </select>

                                    @error('reason')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" id="custom-reason-field" style="display: none;">
                                <label for="custom_reason" class="col-md-12 col-form-label text-md-start">{{ __('Custom Reason') }}</label>

                                <div class="col-md-12">
                                    <input id="custom_reason" type="text" class="form-control @error('custom_reason') is-invalid @enderror" name="custom_reason" value="{{ old('custom_reason') }}">

                                    @error('custom_reason')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="duration" class="col-md-12 col-form-label text-md-start">{{ __('Duration (Hour)') }}</label>

                                <div class="col-md-12">
                                    <input id="duration" type="number" class="form-control @error('duration') is-invalid @enderror" name="duration" min="1" value="24" required>

                                    @error('duration')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-danger w-100">{{ __('Block') }}</button>
                                </div>
                            </div>

                            @if($data->blockedUser && \Carbon\Carbon::parse($data->blockedUser->timeEnd)->isFuture())
                                <hr class="my-2">
                                <div class="row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" form="unblock" class="btn btn-success w-100" onclick="return confirm('Really unblock?');">{{ __('UnBlock') }}</button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-header.bg-primary {
            border-bottom: 1px solid #003087;
        }
        .table-sm th, .table-sm td {
            padding: 0.2rem;
            font-size: 0.85rem;
        }
        .card-body p, .form-label {
            font-size: 0.9rem;
        }
        .btn-sm {
            font-size: 0.8rem;
        }
        .form-control-sm, .form-select-sm {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
        }
        .table-responsive {
            min-height: auto !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function toggleCustomReason(select) {
            const field = document.getElementById('custom-reason-field');
            field.style.display = (select.value === 'Custom') ? 'block' : 'none';
        }
    </script>
@endpush
