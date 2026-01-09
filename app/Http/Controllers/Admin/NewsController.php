<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $data = News::latest()->paginate(20);

        return view('admin.news.index', compact('data'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:news,event,update',
            'image' => 'nullable|string|max:2048',
            'published_at' => 'required|date',
            'content' => 'required|string',
        ]);

        $validated['author_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'News created successfully!');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', ['data' => $news]);
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:news,event,update',
            'image' => 'nullable|string|max:2048',
            'published_at' => 'required|date',
            'content' => 'required|string',
        ]);

        if ($validated['title'] !== $news->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')->with('success', 'News updated successfully.');
    }

    public function confirmDelete(News $news)
    {
        return view('admin.news.delete', ['data' => $news]);
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'News deleted successfully.');
    }
}
