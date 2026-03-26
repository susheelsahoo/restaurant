<?php
// app/Http/Controllers/GalleryImageController.php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Services\GalleryImageVariantService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class GalleryImageController extends Controller
{
    public function __construct(private GalleryImageVariantService $galleryImageVariantService)
    {
    }

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
            'image' => 'required|image|max:10000', // 10MB in KB
        ]);

        $path = $request->file('image')->store('gallery_images', 'public');
        $dimensions = @getimagesize($request->file('image')->getRealPath()) ?: [null, null];

        GalleryImage::create([
            'title' => $request->title,
            'image_path' => $path,
            'image_width' => $dimensions[0],
            'image_height' => $dimensions[1],
            'is_active' => $request->has('is_active'),
        ]);

        $this->galleryImageVariantService->getDisplayVariant($path);

        return redirect()->route('admin.gallery.index')->with('success', 'Image added successfully!');
    }

    public function edit($id)
    {
        $galleryImage = GalleryImage::findOrFail($id);
        return view('pages.gallery.create', compact('galleryImage'));
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:10000', // 10MB in KB
        ]);

        $files = [];

        foreach ($request->file('images', []) as $image) {
            $path = $image->store('gallery', 'public');
            $dimensions = @getimagesize($image->getRealPath()) ?: [null, null];
            $this->galleryImageVariantService->getDisplayVariant($path);

            $files[] = [
                'name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'url'  => asset('storage/' . $path),
                'width' => $dimensions[0],
                'height' => $dimensions[1],
            ];
        }

        return response()->json([
            'files' => $files
        ]);
    }

    public function update(Request $request, $id)
    {
        $image = GalleryImage::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:10000', // 10MB in KB
        ]);

        if ($request->hasFile('image')) {
            $this->galleryImageVariantService->deleteDisplayVariants($image->image_path);
            Storage::disk('public')->delete($image->image_path);
            $path = $request->file('image')->store('gallery_images', 'public');
            $image->image_path = $path;
            $dimensions = @getimagesize($request->file('image')->getRealPath()) ?: [null, null];
            $image->image_width = $dimensions[0];
            $image->image_height = $dimensions[1];
            $this->galleryImageVariantService->getDisplayVariant($path);
        }

        $image->title = $request->title;
        $image->is_active = $request->has('is_active');
        $image->home_display = $request->has('home_display');
        $image->gallery_display = $request->has('gallery_display');
        $image->save();

        return redirect()->route('admin.gallery.index')->with('success', 'Image updated successfully!');
    }
    public function toggle(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:gallery_images,id',
            'field' => 'required|in:home_display,gallery_display,is_active',
        ]);

        $image = GalleryImage::findOrFail($request->id);

        $image->{$request->field} = !$image->{$request->field};
        $image->save();

        return response()->json([
            'status' => true,
            'value' => $image->{$request->field}
        ]);
    }


    public function destroy($id)
    {
        $image = GalleryImage::findOrFail($id);
        $this->galleryImageVariantService->deleteDisplayVariants($image->image_path);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Image deleted!');
    }
}
