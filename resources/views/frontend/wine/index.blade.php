@extends('frontend.layouts.app')

@section('title', 'Menu')

@section('content')
@include(config('settings.FRONTED_PAGE_DIR')."/layouts/_menu")

{{-- HERO --}}
<section class="menu-hero">
    <div class="menu-hero-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="blog-hero-card">
                    <span class="blog-hero-tag">Wine Section</span>
                    <h1>Georgian Wine &amp; Chacha – A Perfect Pairing</h1>
                    <p>
                        Complete your dining experience with our carefully selected Georgian wines,
                        sourced from traditional vineyards and paired perfectly with our cuisine.
                        We also serve authentic Georgian chacha so guests can fully experience
                        Georgian dining culture.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FILTER --}}
<section class="menu-filter">
    <div class="container">
        <div class="filter-tabs">
            <a href="{{ route('wines.index') }}"
                class="{{ !isset($category) ? 'active' : '' }}">
                All
            </a>

            @foreach($wine_categories as $cat)
            <a href="{{ route('wines.category', $cat->slug) }}"
                class="{{ isset($category) && $category->id === $cat->id ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- WINE LIST --}}
<section class="wine-menu-section">
    <div class="container">

        @forelse($wines as $wine)

        <div class="row align-items-center gy-5 wine-menu-block">
            {{-- IMAGE --}}
            <div class="col-lg-2 wine-img-col">
                <img
                    src="{{ $wine->image ? asset('storage/' . $wine->image) : asset('images/no-image.jpg') }}"
                    class="wine-menu-img"
                    alt="{{ $wine->title }}">
            </div>

            {{-- CONTENT --}}
            <div class="col-lg-10 wine-content">
                <h3 class="wine-menu-title">{{ $wine->title }}</h3>

                <p class="wine-menu-desc">
                    {{ Str::limit(strip_tags($wine->description), 260) }}
                </p>

                <div class="wine-options mt-3">
                    <span class="price">{{ number_format($wine->price, 2) }} HUF</span>
                </div>
            </div>
        </div>

        @empty
        <div class="row">
            <div class="col-12 text-center">
                <p>No wines found.</p>
            </div>
        </div>
        @endforelse

        {{-- PAGINATION --}}
        @if($wines->hasPages())
        <div class="menu-pagination text-center mt-5">
            {{ $wines->links('pagination::bootstrap-4') }}
        </div>
        @endif

    </div>
</section>

@endsection