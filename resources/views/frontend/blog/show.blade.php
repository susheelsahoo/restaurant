@extends('frontend.layouts.app')

@section('title', $blog->meta_title ?? $blog->title)

@section('meta_description')
{!! $blog->meta_description ?? '' !!}
@endsection

@section('content')

@include(config('settings.FRONTED_PAGE_DIR').'/layouts/_menu')

<!-- BLOG DETAIL HERO -->
<section class="blog-detail-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <span class="blog-detail-date">
                    {{ $blog->created_at->format('d M Y') }}
                    @if($blog->category)
                    | {{ $blog->category->name }}
                    @endif
                </span>

                <h1>{{ $blog->title }}</h1>

                @if($blog->excerpt)
                <p class="blog-detail-subtitle">
                    {{ $blog->excerpt }}
                </p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- BLOG CONTENT -->
<section class="blog-detail-content">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-9">

                @if($blog->image)
                <img
                    src="{{ asset('storage/' . $blog->image) }}"
                    class="blog-feature-img"
                    alt="{{ $blog->title }}"
                    loading="lazy" />
                @endif

                <article class="blog-article">
                    {!! $blog->content !!}
                </article>

                <!-- TAGS -->
                @if(!empty($blog->tags))
                <div class="blog-tags">
                    @foreach(explode(',', $blog->tags) as $tag)
                    <a href="{{ route('blogs') }}?tag={{ trim($tag) }}">
                        {{ trim($tag) }}
                    </a>
                    @endforeach
                </div>
                @endif

            </div>
        </div>

    </div>
</section>

<!-- RELATED POSTS -->
@if($relatedBlogs->count())
<section class="related-posts">
    <div class="container">

        <h2 class="text-center mb-5">Related Articles</h2>

        <div class="row g-4">

            @foreach($relatedBlogs as $related)
            <div class="col-lg-4 col-md-6">
                <article class="blog-page-card">

                    <img
                        src="{{ asset('storage/' . $related->image) }}"
                        alt="{{ $related->title }}"
                        loading="lazy">

                    <div class="blog-card-body">
                        <span>{{ $related->created_at->format('d M Y') }}</span>

                        <h5>{{ $related->title }}</h5>

                        <p>{{ Str::limit(strip_tags($related->content), 100) }}</p>

                        <a href="{{ route('blog.show', $related->slug) }}">
                            Read More →
                        </a>
                    </div>

                </article>
            </div>
            @endforeach

        </div>

    </div>
</section>
@endif

@endsection