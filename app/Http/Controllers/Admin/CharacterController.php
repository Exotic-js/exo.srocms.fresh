<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SRO\Shard\Char;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function index(Request $request)
    {
        $data = Char::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('CharName16', 'like', "%{$request->search}%");
            })
            ->paginate(20);

        return view('admin.characters.index', compact('data'));
    }

    public function view(Char $char)
    {
        return view('admin.characters.view', ['data' => $char]);
    }

    public function update()
    {
        return back()->with('success', 'Test!');
    }

    public function unstuck(Char $char)
    {
        if ($char->isOnline) {
            return back()->with('error', 'This char is still logged in.');
        }

        if (!$char->isOffline) {
            return back()->with('error', 'Cannot unstuck this char at the moment.');
        }

        if ($char->hasJobSuit) {
            return back()->with('error', 'This char is wearing a Job Suit.');
        }

        $char->setCharUnstuckPosition();

        return back()->with('success', 'Your action was successful.');
    }
}
