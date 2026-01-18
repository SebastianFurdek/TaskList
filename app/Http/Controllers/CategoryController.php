<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $data['user_id'] = auth()->id();

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategória vytvorená.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategória upravená.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if($category->user_id !== auth()->id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategória odstránená.');
    }
}
