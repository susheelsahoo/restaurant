@extends('layout.mobile')

@section('title', 'Dashboard')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@push('styles')
<link rel="stylesheet" href="{{ asset('mobile-login/style.css') }}">
@endpush

@php
    $approvalLabel = static fn (string $status): string => match ($status) {
        'approved', 'ordered' => 'Approved',
        'rejected' => 'Rejected',
        'returned' => 'Needs Edit',
        default => 'Pending',
    };

    $approvalTone = static fn (string $status): string => match ($status) {
        'approved', 'ordered' => 'success',
        'rejected', 'returned' => 'danger',
        default => 'secondary',
    };
@endphp

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
            <h2>Purchase request overview</h2>
            <p>Track open kitchen requests, approvals, and urgent items from today’s purchasing workflow.</p>
            <div class="banner-stats">
                <div class="stat-box">
                    <span class="stat-number">{{ $openRequestsCount }}</span>
                    <span class="stat-label">Open requests</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number">{{ $awaitingApprovalCount }}</span>
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
                <h3>Recent Requests</h3>
                <a href="{{ url('/mobile/request-detail') }}" class="see-all">See all</a>
            </div>

            @forelse($recentRequests as $request)
                <a href="{{ $request['detail_url'] }}" class="template-item recent-request-item request-list-link">
                    <div class="template-info">
                        <div class="recent-request-title">
                            <h4>{{ $request['request_no'] }}</h4>
                            <div class="badge {{ $request['is_urgent'] ? 'badge-yellow' : 'badge-blue' }}">
                                {{ $request['priority'] }}
                            </div>
                        </div>
                        <p>{{ $request['summary'] }} &middot; {{ $request['department'] }} &middot; Needed {{ $request['needed_by'] }}</p>
                    </div>
                    <div class="recent-request-meta">
                        <span class="badge badge-light-{{ $approvalTone($request['status']) }}">
                            {{ $approvalLabel($request['status']) }}
                        </span>
                        <span>{{ $request['items_count'] }} items</span>
                    </div>
                </a>

                @if (!$loop->last)
                    <hr class="divider">
                @endif
            @empty
                <p class="empty-state">No recent requests yet.</p>
            @endforelse
        </section>
    </main>

    @include('mobile.partials.bottom-nav')
</div>
@endsection
