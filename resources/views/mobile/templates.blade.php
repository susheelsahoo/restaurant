@extends('layout.mobile')

@section('title', 'Templates')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@push('styles')
<link rel="stylesheet" href="{{ asset('mobile-login/style.css') }}">
@endpush

@section('mobile-content')
<div class="app-container">
    <header class="header">
        <div class="header-text">
            <h1>Templates</h1>
            <p>Manage templates</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main class="content">
        <section class="card templates-card">
            <div class="templates-header">
                <h3>Template Orders</h3>
            </div>
            <div class="template-item">
                <p>Page Content under construction...</p>
            </div>
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="{{ url('/mobile/dashboard') }}" class="nav-item">Home</a>
        <a href="{{ url('/mobile/request-detail') }}" class="nav-item">Requests</a>
        <a href="{{ url('/mobile/templates') }}" class="nav-item active">Templates</a>
        <a href="{{ url('/mobile/purchasing') }}" class="nav-item">Purchasing</a>
    </nav>
</div>
@endsection
