<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WineCategoryController extends Controller
{
    public function index()
    {
        $categories = WineCategory::latest()->get();
        return view('admin.wine_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.wine_categories.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:wine_categories,slug',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        WineCategory::create($data);

        return redirect()->route('admin.wine-categories.index')->with('success', 'Wine category created successfully.');
    }

    public function edit(WineCategory $wineCategory)
    {
        return view('admin.wine_categories.form', compact('wineCategory'));
    }

    public function show(WineCategory $wineCategory)
    {
        return view('admin.wine_categories.show', compact('wineCategory'));
    }

    public function update(Request $request, WineCategory $wineCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:wine_categories,slug,' . $wineCategory->id,
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        $wineCategory->update($data);

        return redirect()->route('admin.wine-categories.index')->with('success', 'Wine category updated successfully.');
    }

    public function destroy(WineCategory $wineCategory)
    {
        $wineCategory->delete();
        return redirect()->route('admin.wine-categories.index')->with('success', 'Wine category deleted successfully.');
    }
}
