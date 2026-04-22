@extends('layout.mobile')

@section('title', 'Dashboard')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@push('styles')
<link rel="stylesheet" href="{{ asset('mobile-login/style.css') }}">
@endpush

@section('mobile-content')
<div class="app-container">
    <header class="header">
        <div class="header-text">
            <h1>{{ $greeting }}, {{ auth()->user()->name }}</h1>
            <p>Kitchen team &middot; Tuesday overview</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main class="content">
        <section class="banner-card">
            <h2>Create requests in seconds</h2>
            <p>Use templates, favorites, recent items, or scan products to submit daily ingredient orders quickly.</p>
            <div class="banner-stats">
                <div class="stat-box">
                    <span class="stat-number">5</span>
                    <span class="stat-label">Open requests</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">2</span>
                    <span class="stat-label">Awaiting approval</span>
                </div>
            </div>
        </section>

        <button class="primary-btn" type="button" onclick="window.location.href='{{ url('/mobile/quick-add') }}'">
            + New Request
        </button>

        <section class="stats-grid">
            @foreach($stats as $stat)
                <div class="card stat-card">
                    <span class="stat-number-dark">{{ $stat['value'] }}</span>
                    <span class="stat-label-dark">{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </section>

        <section class="card templates-card">
            <div class="templates-header">
                <h3>Favorite Templates</h3>
                <a href="{{ url('/mobile/quick-add') }}" class="see-all">See all</a>
            </div>

            <div class="template-item">
                <div class="template-info">
                    <h4>Daily Kitchen Essentials</h4>
                    <p>Vegetables, dairy, herbs, bread basics</p>
                </div>
                <div class="badge badge-blue">
                    12 items
                </div>
            </div>

            <hr class="divider">

            <div class="template-item">
                <div class="template-info">
                    <h4>Weekend Prep Order</h4>
                    <p>Higher quantities for Friday-Sunday service</p>
                </div>
                <div class="badge badge-yellow">
                    Urgent
                </div>
            </div>
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="{{ url('/mobile/dashboard') }}" class="nav-item active">Home</a>
        <a href="{{ url('/mobile/request-detail') }}" class="nav-item">Requests</a>
        <a href="{{ url('/mobile/quick-add') }}" class="nav-item">Templates</a>
        <a href="{{ url('/mobile/purchasing') }}" class="nav-item">Purchasing</a>
    </nav>
</div>
@endsection
