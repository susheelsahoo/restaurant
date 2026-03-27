<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\GalleryImageVariantService;
use App\Models\Banner;
use App\Models\Page;
use App\Models\Blog;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Wine;
use App\Models\WineCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function __construct(private GalleryImageVariantService $galleryImageVariantService)
    {
    }

    public function index($slug = 'home')
    {
        $page = Page::notDeleted()->where('slug', $slug)->where('is_active', true)->first();

        if (!$page) {
            abort(404, 'Page or Banner not found');
        }

        return view('frontend.page', compact('page'));
    }

    public function homePageGallery(): JsonResponse
    {
        $images = GalleryImage::where('home_display', 1)
            ->where('is_active', 1)
            ->latest()
            ->select('id', 'image_path', 'image_width', 'image_height')
            ->get();

        $images = $images->map(function (GalleryImage $image) {
            $variant = $this->galleryImageVariantService->getDisplayVariant($image->image_path);

            return [
                'id' => $image->id,
                'image_path' => $image->image_path,
                'display_url' => $variant['url'] ?? asset('storage/' . $image->image_path),
                'width' => $variant['width'] ?? $image->image_width,
                'height' => $variant['height'] ?? $image->image_height,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data'   => $images
        ]);
    }
    public function homePageBlog(): JsonResponse
    {
        $blogs = Blog::published()
            ->latest()
            ->take(3)
            ->get(['id', 'title', 'slug', 'content', 'image', 'created_at']);

        return response()->json([
            'status' => true,
            'data'   => $blogs
        ]);
    }

    public function blogs()
    {
        // Recent posts (all except featured and side posts)
        $blogs = Blog::published()
            ->latest()
            ->paginate(9);
        return view('frontend.blog.index', compact('blogs'));
    }

    public function showBlog($slug)
    {
        $blog = Blog::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related posts (same category, excluding current)
        $relatedBlogs = Blog::published()
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

        $featured = Blog::published()
            ->where('category_id', $category->id)
            ->latest()
            ->first();

        $sidePosts = Blog::published()
            ->where('category_id', $category->id)
            ->latest()
            ->skip(1)
            ->take(3)
            ->get();

        $blogs = Blog::published()
            ->where('category_id', $category->id)
            ->latest()
            ->skip(4)
            ->paginate(9);

        return view('frontend.blog.index', compact('featured', 'sidePosts', 'blogs', 'category'));
    }


    public function menu()
    {
        $menus = Menu::where('is_active', true)
            ->orderBy('menu_category_id', 'asc')
            ->paginate(12);

        $menu_categories = MenuCategory::where('is_active', true)->get();
        return view('frontend.menu.index', compact('menus', 'menu_categories'));
    }

    // Filter menus by category
    public function menuByCategory($slug)
    {
        $category = MenuCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $menus = Menu::where('is_active', true)
            ->where('menu_category_id', $category->id)
            ->orderBy('position', 'asc')
            ->paginate(12);

        $menu_categories = MenuCategory::where('is_active', true)->get();
        return view('frontend.menu.index', compact('menus', 'menu_categories', 'category'));
    }

    // Show single menu item
    public function showMenu($slug)
    {
        $menu = Menu::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('frontend.menu.show', compact('menu'));
    }
    public function wines()
    {
        $wines = Wine::where('is_active', true)
            ->orderBy('wine_category_id', 'asc')
            ->paginate(12);

        $wine_categories = WineCategory::where('is_active', true)->get();
        return view('frontend.wine.index', compact('wines', 'wine_categories'));
    }

    // Filter wines by category
    public function winesByCategory($slug)
    {
        $category = WineCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $wines = Wine::where('is_active', true)
            ->where('wine_category_id', $category->id)
            ->orderBy('position', 'asc')
            ->paginate(12);

        $wine_categories = WineCategory::where('is_active', true)->get();
        return view('frontend.wine.index', compact('wines', 'wine_categories', 'category'));
    }

    // Show single wine item
    public function showWine($slug)
    {
        $wine = Wine::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $wine_categories = WineCategory::where('is_active', true)->get();
        return view('frontend.wine.show', compact('wine', 'wine_categories'));
    }





    public function show($slug)
    {
        $page = Page::notDeleted()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('frontend.page', compact('page'));
    }
}
