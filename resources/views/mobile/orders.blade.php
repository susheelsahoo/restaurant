@extends('layout.mobile')

@section('title', 'Purchase Orders')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@push('styles')
<link rel="stylesheet" href="{{ asset('mobile-login/style.css') }}">
@endpush

@section('mobile-content')
<div class="app-container">
    <header class="header">
        <div class="header-text">
            <h1>Purchase Orders</h1>
            <p>Track supplier orders from the PO module</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main class="content">
        <section class="card templates-card">
            <div class="templates-header">
                <h3>All Purchase Orders</h3>
            </div>

            @forelse($purchaseOrders as $purchaseOrder)
                <a href="{{ $purchaseOrder['detail_url'] }}" class="template-item recent-request-item request-list-link">
                    <div class="template-info">
                        <div class="recent-request-title">
                            <h4>{{ $purchaseOrder['po_number'] }}</h4>
                            <div class="badge {{ $purchaseOrder['summary_badge'] }}">
                                {{ $purchaseOrder['order_date'] }}
                            </div>
                        </div>
                        <p>
                            {{ $purchaseOrder['supplier'] }}
                            &middot;
                            {{ $purchaseOrder['request_no'] }}
                            &middot;
                            {{ $purchaseOrder['department'] }}
                            &middot;
                            Expected {{ $purchaseOrder['expected_delivery'] }}
                        </p>
                    </div>
                    <div class="recent-request-meta">
                        <span class="badge badge-light-{{ $purchaseOrder['status_tone'] }}">
                            {{ $purchaseOrder['status_label'] }}
                        </span>
                        <span>{{ $purchaseOrder['items_count'] }} items</span>
                        <span>{{ $purchaseOrder['total_label'] }}</span>
                    </div>
                </a>

                @if (!$loop->last)
                    <hr class="divider">
                @endif
            @empty
                <p class="empty-state">No purchase orders found.</p>
            @endforelse
        </section>
    </main>

    @include('mobile.partials.bottom-nav')
</div>
@endsection
