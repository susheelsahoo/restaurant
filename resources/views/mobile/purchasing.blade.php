@extends('layout.mobile')

@section('title', 'Purchasing')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@section('mobile-content')
<div class="app-container">
    <header class="header">
        <div class="header-text">
            <h2>Purchasing</h2>
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

    @include('mobile.partials.bottom-nav')
</div>
@endsection
