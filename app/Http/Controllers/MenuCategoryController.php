<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::latest()->get();
        return view('admin.menu_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.menu_categories.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:menu_categories,slug',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        MenuCategory::create($data);

        return redirect()->route('admin.menu-categories.index')->with('success', 'Menu category created successfully.');
    }

    public function edit(MenuCategory $menuCategory)
    {
        return view('admin.menu_categories.form', compact('menuCategory'));
    }

    public function show(MenuCategory $menuCategory)
    {
        return view('admin.menu_categories.show', compact('menuCategory'));
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:menu_categories,slug,' . $menuCategory->id,
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');

        $menuCategory->update($data);

        return redirect()->route('admin.menu-categories.index')->with('success', 'Menu category updated successfully.');
    }

    public function destroy(MenuCategory $menuCategory)
    {
        $menuCategory->delete();
        return redirect()->route('admin.menu-categories.index')->with('success', 'Menu category deleted successfully.');
    }
}
