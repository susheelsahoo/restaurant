<?php
// app/Http/Controllers/GalleryImageController.php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryImageController extends Controller
{
    public function index()
    {
        $galleryImages = GalleryImage::latest()->get();
        // $galleryImages = GalleryImage::orderBy('id', 'desc')->get();

        return view('pages.gallery.index', compact('galleryImages'));
    }

    public function create()
    {
        return view('pages.gallery.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('gallery_images', 'public');

        GalleryImage::create([
            'title' => $request->title,
            'image_path' => $path,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.gallery.index')->with('success', 'Image added successfully!');
    }

    public function edit($id)
    {
        $galleryImage = GalleryImage::findOrFail($id);
        return view('pages.gallery.create', compact('galleryImage'));
    }

    public function update(Request $request, $id)
    {
        $image = GalleryImage::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($image->image_path);
            $path = $request->file('image')->store('gallery_images', 'public');
            $image->image_path = $path;
        }

        $image->title = $request->title;
        $image->is_active = $request->has('is_active');
        $image->save();

        return redirect()->route('admin.gallery.index')->with('success', 'Image updated successfully!');
    }

    public function destroy($id)
    {
        $image = GalleryImage::findOrFail($id);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Image deleted!');
    }
}
