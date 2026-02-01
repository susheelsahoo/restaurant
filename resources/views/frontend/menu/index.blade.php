@extends('frontend.layouts.app')

@section('title', 'Menu')

@section('content')
@include(config('settings.FRONTED_PAGE_DIR')."/layouts/_menu")

<section class="menu-hero">
    <div class="menu-hero-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="menu-hero-content">
                    <h1>Our Menu</h1>
                    <p>Explore our delicious selection crafted with fresh local ingredients and Georgian flavors.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="menu-filter">
    <div class="container">
        <div class="filter-tabs">
            <a href="{{ route('menu.index') }}" class="{{ !isset($category) ? 'active' : '' }}">All</a>
            @foreach($menu_categories as $cat)
            <a href="{{ route('menu.category', $cat->slug) }}" class="{{ (isset($category) && $category->id == $cat->id) ? 'active' : '' }}">{{ $cat->name }}</a>
            @endforeach
        </div>
    </div>
</section>

<section class="menu-grid-section">
    <div class="container">
        <div class="row g-4" id="menuDishGrid">
            @forelse($menus as $menu)
            <div class="col-lg-6 col-md-6">
                <div class="menu-card">
                    <a href="{{ route('menu.show', $menu->slug) }}">
                        <img class="menu-main-img" src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/no-image.jpg') }}" alt="{{ $menu->title }}" loading="lazy">
                    </a>

                    <div class="menu-block pt-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <a href="{{ route('menu.show', $menu->slug) }}">
                                    <h5 class="menu-title mb-1">{{ $menu->title }}</h5>
                                </a>
                                <p class="menu-desc mb-0">{{ Str::limit(strip_tags($menu->description), 120) }}</p>
                            </div>
                            <div class="text-end">
                                <h6 class="menu-price">{{$menu->price}} {{ config('app.price_sign') }}</h6>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p>No menu items found.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($menus->hasPages())
        <div class="menu-pagination text-center mt-4">
            {{ $menus->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</section>

@endsection