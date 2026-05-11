@extends('layout.mobile')

@section('title', 'My Profile')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@php
    $roleNames = $user->roles
        ->whereIn('guard_name', ['mobile', 'web'])
        ->pluck('name')
        ->unique()
        ->values();
@endphp

@section('mobile-content')
<div class="app-container">
    <header class="header">
        <div class="header-text">
            <h3>My Profile</h3>
            <p>Logged-in user details</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main class="content">
        <section class="profile-summary-card">
            <div class="profile-summary-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
        </section>

        <section class="card profile-detail-card">
            <div class="profile-detail-row">
                <span>Name</span>
                <strong>{{ $user->name }}</strong>
            </div>
            <div class="profile-detail-row">
                <span>Email</span>
                <strong>{{ $user->email }}</strong>
            </div>
            <div class="profile-detail-row">
                <span>Department</span>
                <strong>{{ $user->department?->name ?? 'Not assigned' }}</strong>
            </div>
            <div class="profile-detail-row">
                <span>Roles</span>
                <strong>{{ $roleNames->isNotEmpty() ? $roleNames->join(', ') : 'Not assigned' }}</strong>
            </div>
            <div class="profile-detail-row">
                <span>Last login</span>
                <strong>{{ $user->last_login_at?->format('d M Y, h:i A') ?? 'Not available' }}</strong>
            </div>
        </section>
    </main>

    @include('mobile.partials.bottom-nav')
</div>
@endsection
