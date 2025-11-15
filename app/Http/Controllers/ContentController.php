<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Contents/Index', [
            'contents' => Content::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Contents/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        Content::create($validated);

        return Redirect::route('contents.index')->with('success', 'Content created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content)
    {
        // Not typically used for this kind of resource, but can be implemented if needed.
        return Inertia::render('Contents/Show', ['content' => $content]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content)
    {
        return Inertia::render('Contents/Edit', [
            'content' => $content,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        $content->update($validated);

        return Redirect::route('contents.index')->with('success', 'Content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $content->delete();

        return Redirect::route('contents.index')->with('success', 'Content deleted successfully.');
    }
}
