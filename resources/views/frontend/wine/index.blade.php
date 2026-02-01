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
                    <h1>Our Wines</h1>
                    <p>Explore our delicious selection crafted with fresh local ingredients and Georgian flavors.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="menu-filter">
    <div class="container">
        <div class="filter-tabs">
            <a href="{{ route('wines.index') }}" class="{{ !isset($category) ? 'active' : '' }}">All</a>
            @foreach($wine_categories as $cat)
            <a href="{{ route('wines.category', $cat->slug) }}" class="{{ (isset($category) && $category->id == $cat->id) ? 'active' : '' }}">{{ $cat->name }}</a>
            @endforeach
        </div>
    </div>
</section>

<section class="menu-grid-section">
    <div class="container">
        <div class="row g-4" id="wineList">
            @forelse($wines as $wine)
            <div class="col-lg-4 col-md-6">
                <div class="menu-card">
                    <a href="{{ route('wines.show', $wine->slug) }}">
                        <img class="menu-main-img" src="{{ $wine->image ? asset('storage/' . $wine->image) : asset('images/no-image.jpg') }}" alt="{{ $wine->title }}" loading="lazy">
                    </a>

                    <div class="menu-block pt-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="menu-title mb-1">{{ $wine->title }}</h5>
                                <p class="menu-desc mb-0">{{ Str::limit(strip_tags($wine->description), 120) }}</p>
                            </div>
                            <div class="text-end">
                                <h6 class="menu-price">{{ $wine->price ? number_format($wine->price, 2) . ' GEL' : '' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p>No wines found.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($wines->hasPages())
        <div class="menu-pagination text-center mt-4">
            {{ $wines->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</section>

@endsection