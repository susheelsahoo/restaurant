@extends('frontend.layouts.app')

@section('title', 'Blogs')

@section('content')
@include(config('settings.FRONTED_PAGE_DIR').'/layouts/_menu')

{{-- HERO --}}
<section class="blog-hero">
    <div class="blog-hero-overlay"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="blog-hero-card">
                    <span class="blog-hero-tag">Our Blog</span>
                    <h1>Delicious Stories for Food Lovers</h1>
                    <p>
                        Discover recipes, traditions, and behind-the-scenes moments from
                        our Georgian kitchen.
                    </p>
                    <a href="#blog-list" class="btn btn-primary-gold rounded-pill px-4">
                        Explore Blogs
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- BLOG GRID --}}
<section id="blog-list" class="blog-grid-section">
    <div class="container">

        {{-- CATEGORY HEADER (only if used later) --}}
        @isset($category)
        <div class="row mb-4">
            <div class="col-12">
                <h2>Category: {{ $category->name }}</h2>
                @if(!empty($category->description))
                <p>{{ $category->description }}</p>
                @endif
            </div>
        </div>
        @endisset

        <div class="row g-4">

            @forelse($blogs as $blog)
            <div class="col-lg-4 col-md-6">
                <article class="blog-page-card">

                    <img
                        src="{{ $blog->image ? asset('storage/'.$blog->image) : asset('frontend/images/no-image.jpg') }}"
                        alt="{{ $blog->title }}"
                        loading="lazy" />

                    <div class="blog-card-body">
                        <span class="blog-date">
                            {{ $blog->created_at->format('d M Y') }}
                        </span>

                        <h5><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h5>

                        <p>
                            {{ Str::limit(strip_tags($blog->content), 100) }}
                        </p>

                        <a href="{{ route('blog.show', $blog->slug) }}">
                            Read More →
                        </a>
                    </div>

                </article>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No blog posts found.</p>
            </div>
            @endforelse

        </div>

        {{-- PAGINATION --}}
        @if($blogs->hasPages())
        <div class="blog-pagination mt-5">
            {{ $blogs->links('pagination::bootstrap-4') }}
        </div>
        @endif

    </div>
</section>
@endsection