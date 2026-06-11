<?php

namespace ExoAddons\WebMarket\Controllers;

use App\Http\Controllers\Controller;
use ExoAddons\WebMarket\Services\MarketItemService;
use ExoAddons\WebMarket\Models\MarketListing;
use ExoAddons\WebMarket\Models\MarketClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebMarketController extends Controller
{
    public function __construct(protected MarketItemService $service) {}

    /**
     * Show all active listings with search & filters.
     */
    public function index(Request $request)
    {
        // Check if market is disabled
        if (!config('webmarket.enabled', true)) {
            abort(404, 'Web Market is currently disabled.');
        }

        $query = MarketListing::where('status', 'active')
            ->where('expires_at', '>', now());

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            // We search in serialization or character name, let's search character name or item name inside JSON
            $query->where(function ($q) use ($search) {
                $q->where('char_name', 'like', "%{$search}%")
                  ->orWhere('item_data_json->ItemName', 'like', "%{$search}%")
                  ->orWhere('item_data_json->CodeName128', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            // Type ID1 representation (1 = Weapon, 2 = Shield/Armor, 3 = Access/Scroll)
            if ($type === 'weapon') {
                $query->where('item_data_json->TypeID1', 1)
                      ->where('item_data_json->TypeID2', 6);
            } elseif ($type === 'shield') {
                $query->where('item_data_json->TypeID1', 1)
                      ->where('item_data_json->TypeID2', 7);
            } elseif ($type === 'armor') {
                $query->where('item_data_json->TypeID1', 1)
                      ->whereIn('item_data_json->TypeID2', [1, 2, 3, 4, 5]);
            } elseif ($type === 'accessory') {
                $query->where('item_data_json->TypeID1', 1)
                      ->where('item_data_json->TypeID2', 8);
            }
        }

        if ($request->filled('plus')) {
            $query->where('plus_opt', '>=', $request->integer('plus'));
        }

        if ($request->filled('sox')) {
            $query->where('item_data_json->SoxType', '!=', 'Normal');
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->input('currency'));
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'plus_desc') {
            $query->orderBy('plus_opt', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $listings = $query->paginate(16);

        // Pre-load buyer characters for Gold purchase modals (avoid N+1 in Blade)
        $buyerChars = collect();
        if (auth()->check()) {
            $buyerChars = $this->service->getUserCharacters(auth()->user()->jid);
        }

        $pendingClaimsCount = $this->getPendingClaimsCount();

        return view('web-market::index', compact('listings', 'buyerChars', 'pendingClaimsCount'));
    }

    /**
     * Show character and listing placement screen.
     */
    public function sell()
    {
        $userJid = Auth::user()->jid;
        $characters = $this->service->getUserCharacters($userJid);

        // Load active listings of user
        $myListings = MarketListing::where('account_id', $userJid)
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingClaimsCount = $this->getPendingClaimsCount();

        return view('web-market::sell', compact('characters', 'myListings', 'pendingClaimsCount'));
    }

    /**
     * Get inventory items for a character (JSON API).
     */
    public function getInventory(int $charId)
    {
        try {
            $userJid = Auth::user()->jid;
            // Security check
            $chars = $this->service->getUserCharacters($userJid);
            if (!$chars->contains('CharID', $charId)) {
                return response()->json(['error' => 'Unauthorized character access.'], 403);
            }

            $inventory = $this->service->getRealtimeInventory($charId);
            return response()->json(['inventory' => $inventory]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Put an item up for sale.
     */
    public function list(Request $request)
    {
        $validated = $request->validate([
            'char_id'  => 'required|integer',
            'slot'     => 'required|integer',
            'price'    => 'required|integer|min:1',
            'currency' => 'required|string|in:gold,silk',
        ]);

        $userJid = Auth::user()->jid;

        // Check listing limit per character
        $activeListings = MarketListing::where('char_id', $validated['char_id'])
            ->where('status', 'active')
            ->count();

        $maxListings = config('webmarket.max_active_listings', 5);
        if ($activeListings >= $maxListings) {
            return back()->with('error', "Maximum {$maxListings} active listings per character reached.");
        }

        try {
            $this->service->listItem(
                $validated['char_id'],
                $validated['slot'],
                $validated['price'],
                $validated['currency'],
                $userJid
            );

            return redirect()->route('market.sell')->with('success', 'Item listed successfully in the web market!');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel an active listing.
     */
    public function cancel(Request $request, int $id)
    {
        try {
            $userJid = Auth::user()->jid;
            $this->service->cancelListing($id, $userJid);
            return back()->with('success', 'Listing cancelled successfully. The item has been moved to your Pending Claims.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Buy an item.
     */
    public function buy(Request $request, int $id)
    {
        $buyerUserJid = Auth::user()->jid;
        $buyerCharId = $request->input('buyer_char_id'); // Optional, required only for Gold payment

        try {
            $claim = $this->service->buyItem($id, $buyerUserJid, $buyerCharId);
            return redirect()->route('market.claims')->with('success', 'Purchase complete! Your item has been queued for delivery via the Vanguard Filter.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display pending claims page.
     */
    public function claims()
    {
        $userJid = Auth::user()->jid;
        $claims = MarketClaim::where('account_id', $userJid)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $characters = $this->service->getUserCharacters($userJid);

        // Note: item parsing is handled by MarketClaim::getParsedItemAttribute()
        // which wraps item_data_json in Fluent for proper array+object access.

        $pendingClaimsCount = $this->getPendingClaimsCount();

        return view('web-market::claims', compact('claims', 'characters', 'pendingClaimsCount'));
    }

    /**
     * Claim a pending item/gold back to character inventory.
     */
    /**
     * Get pending claims count for the authenticated user (for the subnav badge).
     */
    protected function getPendingClaimsCount(): int
    {
        if (!Auth::check()) return 0;
        return MarketClaim::where('account_id', Auth::user()->jid)
            ->where('status', 'pending')
            ->count();
    }

    public function claim(Request $request, int $id)
    {
        $validated = $request->validate([
            'char_id' => 'required|integer',
        ]);

        $userJid = Auth::user()->jid;

        try {
            $this->service->claimItem($id, $validated['char_id'], $userJid);
            return back()->with('success', 'Claim completed successfully! The item/gold has been added to your character.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
