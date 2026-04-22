@extends('layout.mobile')

@section('title', 'Purchasing')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@push('styles')
<link rel="stylesheet" href="{{ asset('mobile-login/style.css') }}">
@endpush

@section('mobile-content')
<div class="app-container">
    <header class="header">
        <div class="header-text">
            <h1>Purchasing</h1>
            <p>Manage purchasing process</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main class="content">
        <section class="card templates-card">
            <div class="templates-header">
                <h3>Purchase Orders</h3>
            </div>
            <div class="template-item">
                <p>Page Content under construction...</p>
            </div>
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="{{ url('/mobile/dashboard') }}" class="nav-item">Home</a>
        <a href="{{ url('/mobile/request-detail') }}" class="nav-item">Requests</a>
        <a href="{{ url('/mobile/quick-add') }}" class="nav-item">Templates</a>
        <a href="{{ url('/mobile/purchasing') }}" class="nav-item active">Purchasing</a>
    </nav>
</div>
@endsection
