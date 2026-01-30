<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        
        $blogs = Blog::where('status', 'published')
            ->latest()
            ->paginate(9);
        
        return view('blog.index', compact('blogs'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.blogs.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|unique:blogs,slug',
            'category_id'  => 'nullable|exists:categories,id',
            'tags'         => 'nullable|array',
            'tags.*'       => 'exists:tags,id',
            'content'      => 'required',
            'image'        => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
            'meta_title'   => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog = Blog::create($data);
        $blog->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully.');
    }


    public function show(Blog $blog)
    {
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.blogs.create', compact('blog', 'categories', 'tags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|unique:blogs,slug,' . $blog->id,
            'category_id'  => 'required|exists:categories,id',
            'tags'         => 'nullable|array',
            'tags.*'       => 'exists:tags,id',
            'content'      => 'required',
            'image'        => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
            'meta_title'   => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);
        $blog->tags()->sync($request->tags);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->tags()->detach();
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted.');
    }

    // Public blog view by slug
    public function showPublic($slug)
    {
        $blog = Blog::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('frontend.blog.show', compact('blog'));
    }
}
