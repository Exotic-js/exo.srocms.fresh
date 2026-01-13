<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\News;
use App\Models\Pages;

class HomeController extends Controller
{
    public function index()
    {
        $data = News::getPosts();
        return view('pages.index', compact('data'));
    }

    public function news()
    {
        $data = News::getPosts();
        return view('pages.news', compact('data'));
    }

    public function locale($locale)
    {
        if (isset(config('global.languages')[$locale])) {
            session(['locale' => $locale]);
        }
        return back();
    }

    public function post($slug)
    {
        $data = News::getPost($slug);

        abort_if(!$data, 404);

        return view('pages.post', compact('data'));
    }

    public function page($slug)
    {
        $data = Pages::getPage($slug);

        abort_if(!$data, 404);

        return view('pages.page', compact('data'));
    }

    public function download()
    {
        $data = Download::getDownloads();
        return view('pages.download', compact('data'));
    }
}
