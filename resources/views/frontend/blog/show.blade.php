@extends('frontend.layouts.app')

@section('title', $blog->meta_title ?? $blog->title)

@section('meta_description')
{!! $blog->meta_description ?? '' !!}
@endsection

@section('content')
@include(config('settings.FRONTED_PAGE_DIR').'/layouts/_menu')

{{-- Hero Section --}}
<section>
    <div class="custom_container_full">
        <div class="hero_section blogs_hero">
            <div class="common_container">
                <div class="col-lg-12">
                    <div class="row align-items-center m-0">
                        <div class="col-lg-7">
                            <h1>{{ $blog->title }}</h1>
                            <p class="text-muted">
                                Published on {{ $blog->created_at->format('d.m.y H:i') }}
                                @if($blog->category)
                                | Category: {{ $blog->category->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Feature Image --}}
<section>
    <div class="custom_container_full">
        <div class="common_container">
            <div class="col-lg-12 blog_details_image">
                @if($blog->image)
                <img class="w-100 mb-4" src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}">
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Blog Content --}}
<section>
    <div class="custom_container_full">
        <div class="common_container">
            <div class="col-lg-12">
                <div class="blogs_main_description">
                    {!! $blog->content !!}
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Related Posts --}}
@if($relatedBlogs->count() > 0)
<section>
    <div class="custom_container_full">
        <div class="common_container">
            <h3 class="mt-5 mb-4">Related Posts</h3>
            <div class="row">
                @foreach($relatedBlogs as $related)
                <div class="col-md-4">
                    <div class="blog_list_item">
                        <div class="blog_list_image">
                            @if($related->image)
                            <a href="{{ route('blog.show', $related->slug) }}">
                                <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->title }}">
                            </a>
                            @endif
                        </div>
                        <div class="blog_date_time">
                            <p>{{ $related->created_at->format('d.m.y H:i') }}</p>
                        </div>
                        <div class="blog_list_description">
                            <h4>
                                <a href="{{ route('blog.show', $related->slug) }}">
                                    {{ $related->title }}
                                </a>
                            </h4>
                            <p>{{ Str::limit(strip_tags($related->content), 120) }}</p>
                            <a href="{{ route('blog.show', $related->slug) }}">Read More</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endsection