@extends('frontend.layouts.app')

@section('title', 'Blogs')

@section('content')
@include(config('settings.FRONTED_PAGE_DIR').'/layouts/_menu')
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
              <a
                href="#blog-list"
                class="btn btn-primary-gold rounded-pill px-4"
              >
                Explore Blogs
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

@if($featured)

@endif

<!-- Recent Posts -->
<!-- Recent Posts -->
<!-- BLOG GRID -->
<section class="blog-grid-section">
    <div class="container">

        <div class="row g-4">

            @foreach($blogs as $blog)
                <div class="col-lg-4 col-md-6">
                    <article class="blog-page-card">

                        <img
                            src="{{ asset('storage/' . $blog->image) }}"
                            alt="{{ $blog->title }}"
                            loading="lazy"
                        />

                        <div class="blog-card-body">
                            <span>
                                {{ $blog->created_at->format('d M Y') }}
                            </span>

                            <h5>{{ $blog->title }}</h5>

                            <p>
                                {{ Str::limit(strip_tags($blog->content), 100) }}
                            </p>

                            <a href="{{ route('blog.show', $blog->slug) }}">
                                Read More →
                            </a>
                        </div>

                    </article>
                </div>
            @endforeach

        </div>

        <!-- Pagination -->
        @if($blogs->hasPages())
            <div class="blog-pagination">
                {{ $blogs->links('pagination::bootstrap-4') }}
            </div>
        @endif

    </div>
</section>


<!-- Call to Action -->
<section>
    <div class="custom_container_full">
        <div class="seventh_section">
            <div class="common_container">
                <div class="final_headings">
                    <h3>Ready to transform your hotel Management?</h3>
                    <div class="final_btn_section">
                        <p>Only 200 GEL/Month</p>
                        <a href="#">Let’s Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection