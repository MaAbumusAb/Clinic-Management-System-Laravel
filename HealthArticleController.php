<?php

namespace App\Http\Controllers;

use App\Models\HealthArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:admin|doctor')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = HealthArticle::paginate(10);
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        HealthArticle::create($request->all());

        return redirect()->route('articles.index')->with('success', 'Health Article created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthArticle $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthArticle $article)
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HealthArticle $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $article->update($request->all());

        return redirect()->route('articles.index')->with('success', 'Health Article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthArticle $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Health Article deleted successfully.');
    }
}