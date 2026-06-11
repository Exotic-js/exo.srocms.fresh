@if(config('widgets.unique_history.enabled'))
    <div class="golden-widget cinematic-panel mb-5 position-relative overflow-hidden" style="border: 1px solid rgba(201, 160, 91, 0.2); border-radius: 12px; background: rgba(15, 12, 10, 0.85); box-shadow: 0 10px 30px rgba(0,0,0,0.5); backdrop-filter: blur(10px);">
        <!-- Cinematic dark glow -->
        <div class="position-absolute top-0 start-50 translate-middle-x" style="width: 80%; height: 2px; background: radial-gradient(circle, rgba(201, 160, 91, 0.8) 0%, transparent 100%);"></div>
        
        <div class="card-header text-center pt-4 pb-3 border-0 bg-transparent">
            <img src="{{ asset('themes/global/assets/images/widget-icon-unique.png') }}" alt="Unique History" height="31" class="mb-2" style="filter: drop-shadow(0 0 5px rgba(201, 160, 91, 0.5));">
            <h3 class="font-cinzel text-uppercase fw-bold m-0" style="color: #ebd197; letter-spacing: 2px; font-size: 1.2rem;">
                ✦ {{ __('Unique History') }} ✦
            </h3>
        </div>
        
        <div class="card-body p-0">
            <div class="unique-list-container custom-scrollbar pb-3" style="max-height: 380px; overflow-y: auto; padding-left: 15px; padding-right: 15px;">
                <ul class="list-unstyled m-0">
                    @forelse($uniqueHistory as $row)
                        <li class="unique-history-item p-3 mt-2 mb-2 d-flex align-items-center position-relative transition-all" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 8px;">
                            <!-- Hover state glow -->
                            <div class="hover-glow position-absolute top-0 start-0 w-100 h-100" style="border-radius: 8px; box-shadow: inset 0 0 15px rgba(201, 160, 91, 0); border: 1px solid rgba(201, 160, 91, 0); opacity: 0; transition: all 0.3s ease; pointer-events: none;"></div>
                            
                            <div class="unique-image me-3 position-relative" style="min-width: 60px;">
                                <div class="img-ring position-absolute top-50 start-50 translate-middle rounded-circle" style="width: 64px; height: 64px; border: 1px solid rgba(201, 160, 91, 0.3); transition: all 0.3s ease;"></div>
                                <img src="{{ asset('themes/global/assets/images/Uniques_CODE/'.$row->Value.'.png') }}" alt="{{ config('ranking.uniques')[$row->Value]['name'] }}" height="60" class="position-relative z-index-1" style="filter: drop-shadow(0 2px 5px rgba(0,0,0,0.7));">
                            </div>
                            <div class="unique-content flex-grow-1 position-relative z-index-2">
                                <h4 class="font-cinzel fw-bold mb-1" style="color: #dfcdb8; font-size: 1.1rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">{{ config('ranking.uniques')[$row->Value]['name'] }}</h4>
                                <p class="unique-killer mb-1" style="font-size: 0.85rem; color: #a39581;">
                                    {{ __('Killed by:') }}
                                    @if(!empty($row->CharName16))
                                        <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none fw-bold" style="color: #c9a05b; transition: all 0.3s ease;">{{ $row->CharName16 }}</a>
                                    @else
                                        <span style="color: #888;">{{ __('None') }}</span>
                                    @endif
                                </p>
                                <p class="m-0" style="font-size: 0.75rem; color: rgba(255,255,255,0.4);">
                                    <i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-center">
                            <p class="m-0" style="color: #a39581;">{{ __('No Records Found!') }}</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Inline styles for the widget -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.4);
            border-radius: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(201, 160, 91, 0.4);
            border-radius: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(201, 160, 91, 0.8);
        }
        .unique-history-item {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), background 0.3s ease;
        }
        .unique-history-item:hover {
            transform: translateX(8px);
            background: rgba(255,255,255,0.04) !important;
        }
        .unique-history-item:hover .hover-glow {
            opacity: 1 !important;
            box-shadow: inset 0 0 15px rgba(201, 160, 91, 0.15) !important;
            border-color: rgba(201, 160, 91, 0.4) !important;
        }
        .unique-history-item a:hover {
            color: #ffdc87 !important;
            text-shadow: 0 0 10px rgba(201, 160, 91, 0.5);
        }
        .unique-history-item:hover .img-ring {
            transform: translate(-50%, -50%) scale(1.15) !important;
            border-color: rgba(201, 160, 91, 0.7) !important;
        }
    </style>
@endif
