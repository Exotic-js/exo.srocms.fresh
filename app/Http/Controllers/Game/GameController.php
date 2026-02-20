<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        return view('game.index');
    }

    public function webmall(Request $request)
    {
        return view('game.webmall');
    }

    public function ranking(Request $request)
    {
        return view('game.ranking');
    }

    public function survey(Request $request)
    {
        return view('game.survey');
    }

    public function fortress(Request $request)
    {
        return view('game.fortress');
    }

    public function banner(Request $request)
    {
        return view('game.banner');
    }
}
