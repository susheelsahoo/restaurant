<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Page;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class PageController extends Controller
{

    public function index($slug = 'home')
    {

        $page = Page::where('slug', $slug)->where('is_active', true)->first();

        if (!$page) {
            abort(404, 'Page or Banner not found');
        }

        return view('frontend.page', compact('page'));
    }

    public function blogs()
    {
        // Featured (latest) post
        $featured = Blog::where('is_published', true)
            ->latest()
            ->first();

        // Side posts (next 3 latest after featured)
        $sidePosts = Blog::where('is_published', true)
            ->latest()
            ->skip(1)
            ->take(3)
            ->get();

        // Recent posts (all except featured and side posts)
        $blogs = Blog::where('is_published', true)
            ->latest()
            ->skip(4)
            ->paginate(9);

        return view('frontend.blog.index', compact('featured', 'sidePosts', 'blogs'));
    }

    public function showBlog($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Get related posts (same category, excluding current)
        $relatedBlogs = Blog::where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->when($blog->category_id, function ($query) use ($blog) {
                $query->where('category_id', $blog->category_id);
            })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('frontend.blog.show', compact('blog', 'relatedBlogs'));
    }

    // Filter blogs by category
    public function blogsByCategory($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $featured = Blog::where('is_published', true)
            ->where('category_id', $category->id)
            ->latest()
            ->first();

        $sidePosts = Blog::where('is_published', true)
            ->where('category_id', $category->id)
            ->latest()
            ->skip(1)
            ->take(3)
            ->get();

        $blogs = Blog::where('is_published', true)
            ->where('category_id', $category->id)
            ->latest()
            ->skip(4)
            ->paginate(9);

        return view('frontend.blog.index', compact('featured', 'sidePosts', 'blogs', 'category'));
    }







    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('frontend.page', compact('page'));
    }
}
