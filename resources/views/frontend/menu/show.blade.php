@extends('frontend.layouts.app')

@section('title', $menu->title)

@section('content')
@include(config('settings.FRONTED_PAGE_DIR')."/layouts/_menu")

<section class="menu-hero">
    <div class="menu-hero-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="menu-hero-content">
                    <h1>{{ $menu->title }}</h1>
                    <p>{{ $menu->category ? $menu->category->name : '' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="menu-detail-section py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 text-center">
                <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/no-image.jpg') }}" alt="{{ $menu->title }}" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h2>{{ $menu->title }}</h2>
                <h5 class="text-muted">{{ $menu->price ? number_format($menu->price,2) . ' GEL' : '' }}</h5>
                <div class="mt-3">
                    {!! nl2br(e($menu->description)) !!}
                </div>
            </div>
        </div>
    </div>
</section>

@endsection