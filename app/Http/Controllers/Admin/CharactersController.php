<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SRO\Log\LogEventChar;
use App\Models\SRO\Shard\Char;
use App\Models\SRO\Shard\InvCOS;
use App\Models\SRO\Shard\Inventory;
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

        $petNames = InvCOS::getPetNames($char->CharID);
        $PetID = $request->input('pet') ?? optional($petNames->first())->ID;
        $petItems = $PetID ? $inventoryService->getPetItems($char->CharID, $PetID, 196, 0) : collect();

        $status = config("ranking.extra.character_status") ? LogEventChar::getCharStatus($char->CharID)->take(5) : collect();

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
        $status = LogEventChar::getCharStatus($char->CharID)->first();

        if (!$status) {
            return back()->with('error', "This char has no OnlineOffline Status.");
        }

        switch ($status->EventID) {
            case 4:
                return back()->with('error', "This char is still logged in.");
            case 6:
                $jobItem = Inventory::getInventorySlot($char->CharID, 8);
                if ($jobItem) {
                    return back()->with('error', "This char is wearing a Job Suite, so no unstuck!");
                }

                $char->setCharUnstuckPosition();

                return back()->with('success', "Your action was successfully.");
            default:
                return back()->with('error', "Cannot unstuck this char at the moment.");
        }
    }
}
