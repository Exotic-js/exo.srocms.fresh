@extends('admin.layouts.app')
@section('title', __('View Character'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">View Character</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card p-0">
                    <div class="card-header">
                        <h4 class="text-center">Character Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="min-height: auto !important;">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <th scope="row">Username</th>
                                    <td>
                                        <a href="{{ route('admin.users.view', $data->User->tbUser?->JID) }}" class="text-decoration-none">
                                            {{ $data->User->tbUser?->StrUserID }}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                                <!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                <path fill="currentColor" d="M384 64C366.3 64 352 78.3 352 96C352 113.7 366.3 128 384 128L466.7 128L265.3 329.4C252.8 341.9 252.8 362.2 265.3 374.7C277.8 387.2 298.1 387.2 310.6 374.7L512 173.3L512 256C512 273.7 526.3 288 544 288C561.7 288 576 273.7 576 256L576 96C576 78.3 561.7 64 544 64L384 64zM144 160C99.8 160 64 195.8 64 240L64 496C64 540.2 99.8 576 144 576L400 576C444.2 576 480 540.2 480 496L480 416C480 398.3 465.7 384 448 384C430.3 384 416 398.3 416 416L416 496C416 504.8 408.8 512 400 512L144 512C135.2 512 128 504.8 128 496L128 240C128 231.2 135.2 224 144 224L224 224C241.7 224 256 209.7 256 192C256 174.3 241.7 160 224 160L144 160z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">CharID</th>
                                    <td>{{ $data->CharID }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">CharName</th>
                                    <td>
                                        @if($data->RefObjID > 2000)
                                            <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                                        @else
                                            <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
                                        @endif

                                        <a href="{{ route('ranking.character.view', ['name' => $data->CharName16]) }}" target="_blank" class="text-decoration-none">
                                            {{ $data->CharName16 }}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                                                <!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                <path fill="currentColor" d="M384 64C366.3 64 352 78.3 352 96C352 113.7 366.3 128 384 128L466.7 128L265.3 329.4C252.8 341.9 252.8 362.2 265.3 374.7C277.8 387.2 298.1 387.2 310.6 374.7L512 173.3L512 256C512 273.7 526.3 288 544 288C561.7 288 576 273.7 576 256L576 96C576 78.3 561.7 64 544 64L384 64zM144 160C99.8 160 64 195.8 64 240L64 496C64 540.2 99.8 576 144 576L400 576C444.2 576 480 540.2 480 496L480 416C480 398.3 465.7 384 448 384C430.3 384 416 398.3 416 416L416 496C416 504.8 408.8 512 400 512L144 512C135.2 512 128 504.8 128 496L128 240C128 231.2 135.2 224 144 224L224 224C241.7 224 256 209.7 256 192C256 174.3 241.7 160 224 160L144 160z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Guild</th>
                                    <td>
                                        @if($data->GuildID)
                                            <a href="{{ route('ranking.guild.view', ['name' => $data->guild->Name]) }}" class="text-decoration-none">{{ $data->guild->Name }}</a>
                                        @else
                                            {{ __('None') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Jobname</th>
                                    <td>
                                        @if(!empty($data->NickName16))
                                            {{ $data->NickName16 }}
                                        @else
                                            {{ __('None') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Level</th>
                                    <td>{{ $data->CurLevel }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Exp</th>
                                    <td>{{ number_format($data->ExpOffset , 0, ',') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Gold</th>
                                    <td>{{ number_format($data->RemainGold , 0, ',') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card p-0 mt-4">
                    <div class="card-header">
                        <h4 class="text-center">Character Items</h4>
                    </div>

                    <ul class="nav nav-tabs justify-content-center mt-3" id="characterItemsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
                                Inventory
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="storage-tab" data-bs-toggle="tab" data-bs-target="#storage" type="button" role="tab">
                                Storage
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pet-tab" data-bs-toggle="tab" data-bs-target="#pet" type="button" role="tab">
                                Pet
                            </button>
                        </li>
                        @if(config('global.server.version') !== 'vSRO')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="chest-tab" data-bs-toggle="tab" data-bs-target="#chest" type="button" role="tab">
                                Chest
                            </button>
                        </li>
                        @endif
                    </ul>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="inventory" role="tabpanel">
                                <div class="card">
                                    <div id="display-inventory" class="card-body p-3 d-flex flex-column justify-content-center align-items-center">
                                        <h2 class="text-center">Inventory</h2>
                                        @include('ranking.character.partials.inventory.inventory-view', ['inventorySetList' => $data->getCharInventorySet(108, 13, 0), 'min' => 13, 'max' => 108])
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="storage" role="tabpanel">
                                <div class="card">
                                    <div id="display-storage" class="card-body p-3 d-flex flex-column justify-content-center align-items-center">
                                        <h2 class="text-center">Storage</h2>
                                        @include('ranking.character.partials.inventory.inventory-view', ['inventorySetList' => $data->charStorageItems, 'min' => 0, 'max' => 179])
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pet" role="tabpanel">
                                <div class="card mt-3">
                                    <div id="display-pet" class="card-body p-3 d-flex flex-column justify-content-center align-items-center">
                                        <h2 class="text-center">Pet</h2>
                                        @include('ranking.character.partials.inventory.inventory-view', ['inventorySetList' => $data->getCharPetItems(request()->input('pet') ?? ($data->CharPets->first()->ID ?? null)), 'min' => 0, 'max' => 195])

                                        <form method="GET" action="">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <select class="form-select" name="pet" aria-label="Default select example" onchange="this.form.submit()">
                                                        @foreach($data->CharPets as $row)
                                                            <option value="{{ $row->ID }}">{{ $row->CharName ?? $row->ID }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @if(config('global.server.version') !== 'vSRO')
                            <div class="tab-pane fade" id="chest" role="tabpanel">
                                <div class="card">
                                    <div id="display-Chest" class="card-body p-3 d-flex flex-column justify-content-center align-items-center">
                                        <h2 class="text-center">Chest</h2>
                                        <div class="table-responsive_">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Qty.</th>
                                                    <th scope="col">Date Registered</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($data->charChestItems as $key => $row)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            <img src="{{ asset('images/sro/'.$row->IconPath.'.png') }}" alt="" width="32" height="32" class="">
                                                            {{ $row->ItemName }}
                                                        </td>
                                                        <td>{{ $row->ItemCount }}</td>
                                                        <td>{{ $row->RegDate }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        {{ $data->charChestItems->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card p-0 mt-4">
                    <div class="card-header">
                        <h4 class="text-center">Logged in history</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="min-height: auto !important;">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($data->getCharStatus()->take(5)->get() as $row)
                                    <tr>
                                        @if($row->EventID == 4)
                                            <td><span class="text-success">Login</span></td>
                                        @else
                                            <td><span class="text-danger">Logout</span></td>
                                        @endif
                                        <td>{{ \Carbon\Carbon::parse($row->EventTime)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No Records Found!</td>
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
                        <h4 class="text-center">Unstuck</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div id="player-map"></div>
                        </div>

                        <ul class="list-unstyled w-50 m-auto p-3">
                            <li>
                                <span>Current X:</span>
                                <span class="float-end">{{ round($data->PosX, 2) }}</span>
                            </li>
                            <li>
                                <span>Current Y:</span>
                                <span class="float-end">{{ round($data->PosY, 2) }}</span>
                            </li>
                            <li>
                                <span>Current Z:</span>
                                <span class="float-end">{{ round($data->PosZ, 2) }}</span>
                            </li>
                        </ul>
                        <hr>

                        <form method="POST" action="{{ route('admin.characters.unstuck', $data) }}" onsubmit="return confirm('Are you sure you want to Unstuck this character?');">
                            @csrf
                            @method('PUT')

                            <div class="row mb-0">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-danger w-100">{{ __('Unstuck') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        .sro-item-detail .tooltip {
            text-align: left !important;
            font-size: 12px;
            width: 250px;
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
        /* Style for paginated layout */
        #display-inventory .page-grid {
            width: 176px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        /* Hide all pages initially */
        #display-inventory .page-hidden {
            display: none !important;
        }

        /* Buttons */
        #display-inventory .page-buttons {
            text-align: center;
            margin-top: 20px;
        }

        #display-inventory .page-buttons button {
            padding: 8px 16px;
            margin: 0 5px;
            cursor: pointer;
            font-weight: bold;
        }
        #display-inventory .sro-item-detail {
            background: #808080;
            width: 38px;
            height: 38px;
            margin: 0 auto;
        }
    </style>
    <style>
        #display-storage .page-grid {
            width: 268px;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        .page-hidden {
            display: none !important;
        }
        .page-arrows {
            text-align: center;
            margin-top: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .page-arrows button {
            font-size: 16px;
        }
        .page-number {
            font-size: 16px;
            font-weight: bold;
            padding: 6px 12px;
            background: none;
            border: 1px solid var(--bs-card-border-color);
            border-radius: 6px;
            min-width: 40px;
            text-align: center;
        }
    </style>
    <style>
        #display-pet .page-grid {
            width: 314px;
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }
    </style>
    <style>
        #display-Chest nav p.small {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/function.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('display-inventory');
            const itemElements = Array.from(container.querySelectorAll('.sro-item-detail'));

            const remainingItems = itemElements.slice(0);

            const tableWrapper = container.querySelector('.table-responsive');
            if (tableWrapper) tableWrapper.remove();

            const itemsPerPage = 32;
            const totalPages = Math.ceil(remainingItems.length / itemsPerPage);
            const pages = [];

            for (let i = 0; i < totalPages; i++) {
                const pageDiv = document.createElement('div');
                pageDiv.classList.add('page-grid');
                if (i !== 0) pageDiv.classList.add('page-hidden');

                const chunk = remainingItems.slice(i * itemsPerPage, (i + 1) * itemsPerPage);
                chunk.forEach(item => pageDiv.appendChild(item));

                container.appendChild(pageDiv);
                pages.push(pageDiv);
            }

            const btnContainer = document.createElement('div');
            btnContainer.className = 'page-buttons';

            pages.forEach((page, i) => {
                const btn = document.createElement('button');
                btn.textContent = `Page ${i + 1}`;
                btn.className = 'btn btn-primary';
                btn.addEventListener('click', () => {
                    pages.forEach(p => p.classList.add('page-hidden'));
                    page.classList.remove('page-hidden');
                });
                btnContainer.appendChild(btn);
            });

            container.appendChild(btnContainer);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('display-storage');
            const itemElements = Array.from(container.querySelectorAll('.sro-item-detail'));

            const remainingItems = itemElements.slice(0);

            const tableWrapper = container.querySelector('.table-responsive');
            if (tableWrapper) tableWrapper.remove();

            const itemsPerPage = 30;
            const totalPages = Math.ceil(remainingItems.length / itemsPerPage);
            const pages = [];
            let currentPage = 0;

            for (let i = 0; i < totalPages; i++) {
                const pageDiv = document.createElement('div');
                pageDiv.classList.add('page-grid');
                if (i !== 0) pageDiv.classList.add('page-hidden');

                const chunk = remainingItems.slice(i * itemsPerPage, (i + 1) * itemsPerPage);
                chunk.forEach(item => pageDiv.appendChild(item));

                container.appendChild(pageDiv);
                pages.push(pageDiv);
            }

            const arrowContainer = document.createElement('div');
            arrowContainer.className = 'page-arrows';

            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '◀';
            prevBtn.className = 'btn btn-primary';

            const pageNumber = document.createElement('span');
            pageNumber.className = 'page-number';
            pageNumber.textContent = `${currentPage + 1}`;

            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '▶';
            nextBtn.className = 'btn btn-primary';

            function updatePage(newPage) {
                if (newPage < 0 || newPage >= totalPages) return;
                pages[currentPage].classList.add('page-hidden');
                pages[newPage].classList.remove('page-hidden');
                currentPage = newPage;
                pageNumber.textContent = `${currentPage + 1}`;
            }

            prevBtn.addEventListener('click', () => updatePage(currentPage - 1));
            nextBtn.addEventListener('click', () => updatePage(currentPage + 1));

            arrowContainer.appendChild(prevBtn);
            arrowContainer.appendChild(pageNumber);
            arrowContainer.appendChild(nextBtn);
            container.appendChild(arrowContainer);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('display-pet');
            const itemElements = Array.from(container.querySelectorAll('.sro-item-detail'));

            const remainingItems = itemElements.slice(0);

            const tableWrapper = container.querySelector('.table-responsive');
            if (tableWrapper) tableWrapper.remove();

            const itemsPerPage = 28;
            const totalPages = Math.ceil(remainingItems.length / itemsPerPage);
            const pages = [];
            let currentPage = 0;

            for (let i = 0; i < totalPages; i++) {
                const pageDiv = document.createElement('div');
                pageDiv.classList.add('page-grid');
                if (i !== 0) pageDiv.classList.add('page-hidden');

                const chunk = remainingItems.slice(i * itemsPerPage, (i + 1) * itemsPerPage);
                chunk.forEach(item => pageDiv.appendChild(item));

                container.appendChild(pageDiv);
                pages.push(pageDiv);
            }

            const arrowContainer = document.createElement('div');
            arrowContainer.className = 'page-arrows';

            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '◀';
            prevBtn.className = 'btn btn-primary';

            const pageNumber = document.createElement('span');
            pageNumber.className = 'page-number';
            pageNumber.textContent = `${currentPage + 1}`;

            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '▶';
            nextBtn.className = 'btn btn-primary';

            function updatePage(newPage) {
                if (newPage < 0 || newPage >= totalPages) return;
                pages[currentPage].classList.add('page-hidden');
                pages[newPage].classList.remove('page-hidden');
                currentPage = newPage;
                pageNumber.textContent = `${currentPage + 1}`;
            }

            prevBtn.addEventListener('click', () => updatePage(currentPage - 1));
            nextBtn.addEventListener('click', () => updatePage(currentPage + 1));

            arrowContainer.appendChild(prevBtn);
            arrowContainer.appendChild(pageNumber);
            arrowContainer.appendChild(nextBtn);
            container.appendChild(arrowContainer);
        });
    </script>

    <script src="{{ asset('xSROMap/assets/js/minimap.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            createMinimapCanvas(
                '{{ asset('https://raw.githubusercontent.com/JellyBitz/xSROMap/master/assets/img/silkroad/minimap/8') }}/',
                'player-map',
                206,
                206,
                {{ $data->PosX }},
                {{ $data->PosZ }},
                {{ $data->PosY }},
                {{ $data->LatestRegion }}
            );
            addMinimapCursor(
                'player-map',
                '{{ asset('https://raw.githubusercontent.com/JellyBitz/xSROMap/master/assets/img/silkroad/minimap/icon/mm_sign_otherplayer.png') }}',
                6,
                6
            );
        });
    </script>
@endpush
