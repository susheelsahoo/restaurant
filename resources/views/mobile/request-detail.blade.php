@extends('layout.mobile')

@section('title', 'Requests')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@php
    $purchaseRoles = app(\App\Services\PurchaseRoleService::class);
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
            <h3>Requests</h3>
            <p>View all recent purchase requests</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main class="content">
        @if(session('success'))
        <div class="or-result-message show success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="or-result-message show danger">{{ session('error') }}</div>
        @endif

       

        <section class="card templates-card">
            <div class="templates-header">
                <h3>All Requests</h3>
            </div>

            @forelse($requests as $request)
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
            <p class="empty-state">No requests found.</p>
            @endforelse
        </section>

        @if($purchaseRoles->canCreateRequests(auth()->user()))
            <button class="primary-btn" type="button" onclick="window.location.href='{{ url('/mobile/quick-add') }}'">
                + Add Request
            </button>
        @endif
    </main>

    @include('mobile.partials.bottom-nav')
</div>
@endsection
