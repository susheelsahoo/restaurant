<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('pages.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('pages.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|max:255',
            'content' => 'required',
            'slug' => 'nullable|unique:banners,slug',
        ]);
        $validated['slug'] = Str::slug($validated['title']);

        $validated['image_path'] = 'NA';
        Banner::create($validated);
        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully!');
    }


    public function edit(Banner $banner)
    {
        return view('pages.banners.create', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {

        $validated = $request->validate([
            'title' => 'nullable|max:255',
            'slug' => 'nullable|unique:pages,slug,' . $banner->id,
            'content' => 'required',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expiry_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path');
            $imagePath = $image->store('banners', 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');
        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        if (file_exists(public_path('storage/' . $banner->image_path))) {
            unlink(public_path('storage/' . $banner->image_path));
        }

        $banner->delete();
        return redirect()->route('pages.banners.index')->with('success', 'Banner deleted!');
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return view('pages.frontend.banner', compact('banner'));
    }
}
