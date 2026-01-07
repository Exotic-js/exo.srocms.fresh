<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SRO\Shard\Char;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class CharactersController extends Controller
{
    public function index(Request $request)
    {
        $query = Char::query();

        $query->when($request->filled('search'), fn($q) =>
            $q->where('CharName16', 'like', "%{$request->search}%")
        );

        $data = $query->paginate(20);

        return view('admin.characters.index', compact('data'));
    }

    public function view(Char $char, InventoryService $inventoryService, Request $request)
    {
        $inventorySet = $inventoryService->getInventorySet($char->CharID, 108, 13, 0);
        $storageItems = $inventoryService->getStorageItems($char->user?->UserJID ?? 0, 180, 0);

        $petNames = $char->getPetNames();
        $PetID = $request->input('pet') ?? optional($petNames->first())->ID;
        $petItems = $PetID ? $inventoryService->getPetItems($char->CharID, $PetID, 196, 0) : collect();

        $status = config("ranking.extra.character_status") ? $char->getCharStatus()->take(5)->get() : collect();

        return view('admin.characters.view', [
            'data' => $char,
            'status' => $status,
            'inventorySet' => $inventorySet,
            'storageItems' => $storageItems,
            'petNames' => $petNames,
            'PetID' => $PetID,
            'petItems' => $petItems,
        ]);
    }

    public function update(Request $request, Char $char)
    {
        return back()->with('success', 'Test!');
    }

    public function unstuck(Char $char)
    {
        if ($char->isOnline()) {
            return back()->with('error', 'This char is still logged in.');
        }

        if (!$char->isOffline()) {
            return back()->with('error', 'Cannot unstuck this char at the moment.');
        }

        if ($char->hasJobSuit()) {
            return back()->with('error', 'This char is wearing a Job Suit.');
        }

        $char->setCharUnstuckPosition();

        return back()->with('success', 'Your action was successful.');
    }
}
