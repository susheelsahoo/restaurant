<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Wine;
use App\Models\WineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class WinesController extends Controller
{
    public function index(Request $request)
    {
        $query = Wine::query()->with('category');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $wines = $query->orderBy('wine_category_id', 'asc')->paginate(20)->withQueryString();

        return view('admin.wines.index', compact('wines'));
    }

    public function create()
    {
        $categories = WineCategory::all();
        return view('admin.wines.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:wines,slug',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|max:4096',
            'wine_category_id' => 'nullable|exists:wine_categories,id',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('wines', 'public');
        }

        Wine::create($data);

        return redirect()->route('admin.wines.index')->with('success', 'Wine item created successfully.');
    }

    public function show(Wine $wine)
    {
        return view('admin.wines.show', compact('wine'));
    }

    public function edit(Wine $wine)
    {
        $categories = WineCategory::all();
        return view('admin.wines.form', compact('wine', 'categories'));
    }

    public function update(Request $request, Wine $wine)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:wines,slug,' . $wine->id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|max:4096',
            'wine_category_id' => 'nullable|exists:wine_categories,id',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($wine->image) {
                Storage::disk('public')->delete($wine->image);
            }
            $data['image'] = $request->file('image')->store('wines', 'public');
        }

        $wine->update($data);

        return redirect()->route('admin.wines.index')->with('success', 'Wine item updated successfully.');
    }

    public function destroy(Wine $wine)
    {
        if ($wine->image) {
            Storage::disk('public')->delete($wine->image);
        }

        $wine->delete();

        return redirect()->route('admin.wines.index')->with('success', 'Wine item deleted successfully.');
    }
}
