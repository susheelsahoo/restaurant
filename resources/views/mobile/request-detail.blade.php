@extends('layout.mobile')

@section('title', 'Requests')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@push('styles')
<link rel="stylesheet" href="{{ asset('mobile-login/style.css') }}">
<style>
    .badge-light-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-light-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-light-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .recent-request-item {
        align-items: flex-start;
        gap: 12px;
    }

    .recent-request-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        flex-shrink: 0;
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
    }

    .recent-request-title {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .empty-state {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
    }
</style>
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
            <h1>Requests</h1>
            <p>View all recent purchase requests</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main class="content">
        <button class="primary-btn" type="button" onclick="window.location.href='{{ url('/mobile/quick-add') }}'">
            + Add Request
        </button>

        <section class="card templates-card">
            <div class="templates-header">
                <h3>All Requests</h3>
            </div>

            @forelse($requests as $request)
                <div class="template-item recent-request-item">
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
                </div>

                @if (!$loop->last)
                    <hr class="divider">
                @endif
            @empty
                <p class="empty-state">No requests found.</p>
            @endforelse
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="{{ url('/mobile/dashboard') }}" class="nav-item">Home</a>
        <a href="{{ url('/mobile/request-detail') }}" class="nav-item active">Requests</a>
        <a href="{{ url('/mobile/quick-add') }}" class="nav-item">Templates</a>
        <a href="{{ url('/mobile/purchasing') }}" class="nav-item">Purchasing</a>
    </nav>
</div>
@endsection
