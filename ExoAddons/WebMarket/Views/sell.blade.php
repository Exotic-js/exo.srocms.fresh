@extends('layouts.full')

@section('title', 'List Item')

@push('styles')
<link rel="stylesheet" href="{{ asset('ExoAddons/WebMarket/css/market.css') }}">
@endpush

@section('content')
<div class="container py-4">
@include('web-market::partials.subnav', ['activeTab' => 'sell'])

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="inventory-container">
        <!-- Left Column: Character List -->
        <div class="character-panel">
            <h4 class="text-warning mb-3 border-bottom pb-2 border-secondary"><i class="fas fa-users"></i> Characters</h4>
            @if($characters->isEmpty())
                <p class="text-muted">No characters found on your account.</p>
            @else

                @foreach($characters as $char)
                    <div class="char-select-item" data-id="{{ $char->CharID }}">
                        <div class="char-avatar">{{ strtoupper(substr($char->CharName16, 0, 1)) }}</div>
                        <div>
                            <div class="text-white fw-bold">{{ $char->CharName16 }}</div>
                            <div class="text-muted small">Lv. {{ $char->CurLevel }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Right Column: Interactive Inventory Grid -->
        <div class="inventory-grid-panel">
            <h4 class="text-warning mb-3 border-bottom pb-2 border-secondary"><i class="fas fa-th"></i> Character Inventory</h4>
            
            <div id="select-char-prompt" class="text-center py-5">
                <i class="fas fa-hand-pointer fa-2x text-muted mb-2"></i>
                <p class="text-muted">Please select a character on the left to view inventory slots.</p>
            </div>

            <div id="inventory-loading" class="text-center py-5 d-none">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Reading game databases, please wait...</p>
            </div>

            <div id="inventory-grid-wrapper" class="d-none text-center">
                <div class="market-notice" style="padding:8px 14px; font-size:0.8rem; margin-bottom:12px;">
                    <i class="fas fa-hand-pointer" style="color:var(--gold-primary);"></i>
                    Click on any occupied item slot to put it up for sale.
                </div>
                <!-- 8-column layout -->
                <div id="sro-grid" class="sro-inventory-grid">
                    <!-- Javascript populates 96 slots dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Active Listings Manager -->
    <div class="mt-5">
        <h4 class="text-warning mb-3 border-bottom pb-2 border-secondary"><i class="fas fa-list"></i> My Current Listings</h4>
        @if($myListings->isEmpty())
            <p class="text-muted">You do not have any active listings.</p>
        @else
            <div class="table-responsive">
                <table class="table table-dark table-hover border border-secondary align-middle">
                    <thead>
                        <tr class="border-secondary text-warning">
                            <th>Item Details</th>
                            <th>Seller Char</th>
                            <th>Price</th>
                            <th>Currency</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myListings as $listing)
                            @php
                                $parsed      = (object) ($listing->item_data_json ?? []);
                                $displayName = $parsed->ItemName ?? $parsed->CodeName128 ?? $parsed->NameStrID128 ?? 'Unknown Item';
                            @endphp
                            <tr class="border-secondary">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-black border border-secondary p-1" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                                            @php
                                                $iconPath = str_replace('\\', '/', trim($parsed->AssocFileIcon128 ?? ''));
                                                $iconPath = preg_replace('/\.ddj$/i', '', $iconPath);
                                                $iconPath = strtolower($iconPath . '.png');
                                                $fullIconPath = public_path('images/sro/'.$iconPath);
                                                $imgUrl = file_exists($fullIconPath) ? asset('images/sro/'.$iconPath) : asset('images/sro/icon_default.png');
                                            @endphp
                                            <img src="{{ $imgUrl }}" style="max-width:100%; max-height:100%; image-rendering:pixelated;">
                                        </div>
                                        <div>
                                            <span class="text-white fw-bold">{{ $displayName }}</span>
                                            @if($listing->plus_opt > 0)
                                                <span class="badge bg-warning text-black ms-1">+{{ $listing->plus_opt }}</span>
                                            @endif
                                            @if(isset($parsed->SoxType) && $parsed->SoxType !== 'Normal')
                                                <span class="badge bg-info text-black ms-1">{{ $parsed->SoxType }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-info">{{ $listing->char_name }}</td>
                                <td>{{ number_format($listing->price) }}</td>
                                <td><span class="badge bg-secondary text-capitalize">{{ $listing->currency }}</span></td>
                                <td>{{ $listing->expires_at->diffForHumans() }}</td>
                                <td>
                                    <span class="badge bg-{{ $listing->status === 'active' ? 'success' : ($listing->status === 'sold' ? 'warning text-black' : 'secondary') }}">
                                        {{ strtoupper($listing->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($listing->status === 'active')
                                        <form method="POST" action="{{ route('market.cancel', $listing->id) }}" onsubmit="return confirm('Cancel this listing?')">
                                            @csrf
                                            <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash-alt"></i> Cancel</button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- List Item Modal overlay -->
<div id="list-modal-overlay" class="modal-overlay d-none"></div>
<div id="list-item-modal" class="list-item-modal d-none">
    <h5 class="text-warning mb-3 border-bottom pb-2 border-secondary"><i class="fas fa-tags"></i> List Item For Sale</h5>
    <form method="POST" action="{{ route('market.list') }}">
        @csrf
        <input type="hidden" name="char_id" id="modal-char-id">
        <input type="hidden" name="slot" id="modal-slot">
        
        <div class="d-flex align-items-center gap-3 mb-3 p-2 bg-black border border-secondary rounded">
            <div id="modal-icon-box" style="width:48px; height:48px; display:flex; align-items:center; justify-content:center; background:#111;">
                <img id="modal-item-img" src="" style="max-width:100%; max-height:100%; image-rendering:pixelated;">
            </div>
            <div>
                <h6 id="modal-item-name" class="text-white m-0"></h6>
                <span id="modal-item-desc" class="text-muted small"></span>
            </div>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label text-warning">Price</label>
            <input type="number" name="price" id="price" class="form-control bg-black text-white border-secondary" required min="1" placeholder="e.g. 500">
        </div>

        <div class="mb-3">
            <label for="currency" class="form-label text-warning">Currency</label>
            <select name="currency" id="currency" class="form-select bg-black text-white border-secondary" required>
                <option value="gold">Gold (Character Gold via Filter)</option>
                <option value="silk">Silk (Account Silk via Filter)</option>
            </select>
        </div>

        <div class="market-notice" style="padding: 8px 14px; font-size: 0.8rem; margin-bottom: 12px;">
            <i class="fas fa-percentage" style="color: var(--gold-primary);"></i>
            A tax of <strong>{{ config('webmarket.tax', 5) }}%</strong> is deducted from completed sales.
        </div>

        <div style="display:flex; gap:8px; margin-top:4px;">
            <button type="submit" class="btn-golden-buy" style="flex:1; clip-path:none;"><i class="fas fa-paper-plane"></i> Publish Listing</button>
            <button type="button" id="close-modal-btn" class="btn-market-reset" style="padding:9px 14px;"><i class="fas fa-times"></i></button>
        </div>
    </form>
</div>

<!-- SRO style hover tooltip box -->
<div id="sro-tooltip-box" class="sro-tooltip"></div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var selectedCharId = null;
        var tooltip = $('#sro-tooltip-box');
        var inventoryItems = {};

        // Click character
        $('.char-select-item').click(function() {
            $('.char-select-item').removeClass('active');
            $(this).addClass('active');
            
            selectedCharId = $(this).data('id');
            loadInventory(selectedCharId);
        });

        // Ajax inventory loader
        function loadInventory(charId) {
            $('#select-char-prompt').addClass('d-none');
            $('#inventory-grid-wrapper').addClass('d-none');
            $('#inventory-loading').removeClass('d-none');

            $.ajax({
                url: '/market/sell/inventory/' + charId,
                method: 'GET',
                success: function(response) {
                    $('#inventory-loading').addClass('d-none');
                    $('#inventory-grid-wrapper').removeClass('d-none');
                    
                    buildGrid(response.inventory);
                },
                error: function(xhr) {
                    $('#inventory-loading').addClass('d-none');
                    $('#select-char-prompt').removeClass('d-none');
                    alert('Error: ' + (xhr.responseJSON?.error || 'Failed to connect to database.'));
                }
            });
        }

            // Build 8x12 grid (96 slots starting from slot 13 up to 108)
        function buildGrid(items) {
            var grid = $('#sro-grid');
            grid.empty();

            // Build dictionary of items indexed by slot
            inventoryItems = {};
            items.forEach(function(item) {
                inventoryItems[item.Slot] = item;
            });

            // SRO has 96 inventory slots (slots 13 to 108)
            for (var slot = 13; slot <= 108; slot++) {
                var item = inventoryItems[slot];
                var cell = $('<div>').addClass('sro-slot').attr('data-slot', slot);

                if (item) {
                    cell.addClass('occupied');
                    if (item.SoxType && item.SoxType !== 'Normal') {
                        cell.addClass('is-sox');
                    }

                    var iconUrl = '/images/sro/' + item.ImgPath;
                    var img = $('<img>').attr('src', iconUrl).attr('onerror', "this.src='/images/sro/icon_default.png'");
                    cell.append(img);

                    // Build tooltip HTML
                    var tooltipHtml = buildTooltipHtml(item);
                    cell.attr('data-tooltip', tooltipHtml);
                }

                grid.append(cell);
            }

            // Re-bind hover & click events
            bindGridEvents();
        }

        // Build item details tooltip matching SRO style
        function buildTooltipHtml(item) {
            var html = '<div class="tooltip-header">';
            html += '<div class="tooltip-item-name text-warning">' + item.ItemName + (item.OptLevel > 0 ? ' (+' + item.OptLevel + ')' : '') + '</div>';
            if (item.SoxType && item.SoxType !== 'Normal') {
                html += '<div class="text-info fw-bold">' + item.SoxType + '</div>';
            }
            html += '</div>';
            html += '<div class="tooltip-white-info">';
            if (item.Degree) html += '<div>Degree: ' + item.Degree + ' Deg</div>';
            if (item.ReqLevel1) html += '<div>Required Lv.: ' + item.ReqLevel1 + '</div>';

            // White properties
            var w = item.WhiteInfo || {};
            if (w.PAtack)    html += '<div class="text-light">' + w.PAtack + '</div>';
            if (w.MAtack)    html += '<div class="text-light">' + w.MAtack + '</div>';
            if (w.PDefance)  html += '<div class="text-light">' + w.PDefance + '</div>';
            if (w.MDefance)  html += '<div class="text-light">' + w.MDefance + '</div>';
            if (w.Durability)html += '<div class="text-light">' + w.Durability + '</div>';
            if (w.Critical)  html += '<div class="text-light">' + w.Critical + '</div>';
            html += '</div>';

            // Blue (magic) options
            var blues = item.BlueInfo;
            if (blues && blues.length > 0) {
                html += '<div class="tooltip-blue-info border-top border-secondary pt-1 mt-1">';
                blues.forEach(function(b) {
                    html += '<div>• ' + (b.name || b.code) + '</div>';
                });
                html += '</div>';
            }

            return html;
        }

        function bindGridEvents() {
            $('.sro-slot').hover(function(e) {
                var html = $(this).attr('data-tooltip');
                if (html) {
                    tooltip.html(html).show();
                }
            }, function() {
                tooltip.hide();
            }).mousemove(function(e) {
                tooltip.css({
                    top: (e.pageY + 15) + 'px',
                    left: (e.pageX + 15) + 'px'
                });
            });

            // Click slot to list
            $('.sro-slot.occupied').click(function() {
                var slot = $(this).data('slot');
                var item = inventoryItems[slot];
                if (item) {
                    // Open List Modal
                    $('#modal-char-id').val(selectedCharId);
                    $('#modal-slot').val(slot);
                    var nameLabel = item.ItemName + (item.OptLevel > 0 ? ' (+' + item.OptLevel + ')' : '');
                    var descLabel = (item.Degree ? 'Degree ' + item.Degree : '') + (item.ReqLevel1 ? ' / Req. Lv. ' + item.ReqLevel1 : '');
                    $('#modal-item-name').text(nameLabel);
                    $('#modal-item-desc').text(descLabel);
                    $('#modal-item-img').attr('src', '/images/sro/' + item.ImgPath);

                    $('#list-modal-overlay').removeClass('d-none');
                    $('#list-item-modal').removeClass('d-none');
                }
            });
        }

        // Close modal
        $('#close-modal-btn, #list-modal-overlay').click(function() {
            $('#list-modal-overlay').addClass('d-none');
            $('#list-item-modal').addClass('d-none');
        });
    });
</script>
@endpush
