@extends('frontend.layouts.app')

@section('title', 'Blogs')

@section('content')
@include(config('settings.FRONTED_PAGE_DIR').'/layouts/_menu')
<section>
    <div class="custom_container_full">
        <div class="hero_section blogs_hero">
            <div class="common_container">
                <div class="col-lg-12">
                    <div class="row align-items-center m-0">
                        <div class="col-lg-7">
                            <h1>
                                Insights, Strategies & Trends to Elevate Guest<br>
                                Experience and Drive Hotel Growth in Today’s<br>
                                Hospitality Landscape
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($featured)
<section>
    <div class="custom_container_full">
        <div class="blogs_main">
            <div class="common_container">
                <div class="col-lg-12">
                    <div class="row m-0">
                        <!-- Featured Blog -->
                        <div class="col-lg-8">
                            <div class="blogs_main_left">
                                <div class="blogs_main_image">
                                    <img src="{{ asset('storage/' . $featured->image) }}" alt="{{ $featured->title }}">
                                </div>
                                <div class="blogs_main_description">
                                    <h2>
                                        <a href="{{ route('blog.show', $featured->slug) }}">
                                            {{ $featured->title }}
                                        </a>
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <!-- Side Posts -->
                        <div class="col-md-4">
                            <div class="blogs_main_right">
                                @foreach($sidePosts as $post)
                                <div class="common_sidepost">
                                    <div class="sidepost_image">
                                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                                    </div>
                                    <div class="sidepost_description">
                                        <h3>
                                            <a href="{{ route('blog.show', $post->slug) }}">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Recent Posts -->
<!-- Recent Posts -->
<section>
    <div class="custom_container_full">
        <div class="blog_listing">
            <div class="common_container">
                <h3>Recent POSTS</h3>
                <div class="col-lg-12 p-0">
                    <div class="row m-0">
                        @foreach($blogs as $blog)
                        <div class="col-md-4">
                            <div class="blog_list_item">
                                <div class="blog_list_image">
                                    <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}">
                                </div>
                                <div class="blog_date_time">
                                    <p>{{ $blog->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <div class="blog_list_description">
                                    <h4>
                                        <a href="{{ route('blog.show', $blog->slug) }}">
                                            {{ $blog->title }}
                                        </a>
                                    </h4>
                                    <p>{{ Str::limit(strip_tags($blog->content), 150, '...') }}</p>
                                    <a href="{{ route('blog.show', $blog->slug) }}">Read More</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $blogs->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
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