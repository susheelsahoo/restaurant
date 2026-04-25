@extends('layout.mobile')

@section('title', $purchaseOrderReview['po_number'])
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@section('mobile-content')
<div class="order-review-page">
    <header class="or-topbar">
        <a class="or-icon-btn" href="{{ url('/mobile/orders') }}" aria-label="Back to purchase orders">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div class="or-title-block">
            <h1>Purchase Order</h1>
            <p>Supplier order and delivery tracking</p>
        </div>
        <div class="or-avatar">{{ strtoupper(substr((string) auth()->user()?->name, 0, 1)) ?: 'P' }}</div>
    </header>

    <main>
        @if(session('success'))
            <div class="or-result-message show success">{{ session('success') }}</div>
        @endif

        <section class="or-card or-hero">
            <h2>{{ $purchaseOrderReview['po_number'] }}</h2>
            <p>
                Ordered from {{ $purchaseOrderReview['supplier'] }} by {{ $purchaseOrderReview['buyer'] }}.
                Linked request {{ $purchaseOrderReview['request_no'] }} for {{ $purchaseOrderReview['department'] }}.
            </p>
            <div class="or-hero-grid">
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['order_date_short'] }}</strong>
                    <span>Order date</span>
                </div>
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['expected_delivery_short'] }}</strong>
                    <span>Expected delivery</span>
                </div>
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['items_count'] }} items</strong>
                    <span>Line items</span>
                </div>
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['total_label'] }}</strong>
                    <span>PO total</span>
                </div>
            </div>
        </section>

        <section class="or-card">
            <div class="or-section-head">
                <h3>PO Details</h3>
                <span class="or-pill {{ $purchaseOrderReview['status_pill_tone'] }}">
                    {{ $purchaseOrderReview['status_label'] }}
                </span>
            </div>
            <div class="or-meta-list">
                <div class="or-meta-row">
                    <div>
                        <strong>Supplier</strong>
                        <small>{{ $purchaseOrderReview['supplier'] }}</small>
                    </div>
                    <div>
                        <strong>Buyer</strong>
                        <small>{{ $purchaseOrderReview['buyer'] }}</small>
                    </div>
                </div>
                <div class="or-meta-row">
                    <div>
                        <strong>Request No.</strong>
                        <small>{{ $purchaseOrderReview['request_no'] }}</small>
                    </div>
                    <div>
                        <strong>Requester</strong>
                        <small>{{ $purchaseOrderReview['requester'] }}</small>
                    </div>
                </div>
                <div class="or-meta-row">
                    <div>
                        <strong>Order Date</strong>
                        <small>{{ $purchaseOrderReview['order_date'] }}</small>
                    </div>
                    <div>
                        <strong>Expected</strong>
                        <small>{{ $purchaseOrderReview['expected_delivery'] }}</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="or-card">
            <div class="or-section-head">
                <h3>Delivery Progress</h3>
                <span>{{ $purchaseOrderReview['received_percent'] }}% received</span>
            </div>
            <div class="or-summary-grid">
                <div class="or-summary-box">
                    <strong>{{ $purchaseOrderReview['received_label'] }}</strong>
                    <span>Received / ordered quantity</span>
                </div>
                <div class="or-summary-box">
                    <strong>{{ $purchaseOrderReview['total_label'] }}</strong>
                    <span>Total order value</span>
                </div>
            </div>
            <div class="or-progress {{ $purchaseOrderReview['received_percent'] >= 100 ? 'safe' : 'warn' }}">
                <div style="width: {{ $purchaseOrderReview['received_percent'] }}%"></div>
            </div>
        </section>

        <section class="or-card">
            <div class="or-section-head">
                <h3>Supplier Contact</h3>
                <span>Order desk</span>
            </div>
            <div class="or-meta-list">
                <div class="or-meta-row">
                    <div>
                        <strong>Email</strong>
                        <small>{{ $purchaseOrderReview['supplier_email'] }}</small>
                    </div>
                    <div>
                        <strong>Phone</strong>
                        <small>{{ $purchaseOrderReview['supplier_phone'] }}</small>
                    </div>
                </div>
            </div>
        </section>

        <div class="or-section-head or-list-head">
            <h3>Line Items</h3>
            <span>{{ $purchaseOrderReview['items_count'] }} lines</span>
        </div>

        <section class="or-card">
            @forelse($purchaseOrderReview['items'] as $item)
                <div class="or-item-row">
                    <div>
                        <div class="or-item-name">{{ $item['name'] }}</div>
                        <div class="or-item-meta">
                            Ordered {{ $item['ordered_label'] }}
                            &middot;
                            Received {{ $item['received_label'] }}
                        </div>
                    </div>
                    <div class="or-item-price">
                        {{ $item['line_total_label'] }}
                    </div>
                </div>
            @empty
                <p class="or-empty-state">No purchase order items added yet.</p>
            @endforelse
        </section>

        <section class="or-card">
            <div class="or-section-head">
                <h3>Status Actions</h3>
                <span>Quick update</span>
            </div>
            <div class="po-action-grid">
                @foreach($purchaseOrderReview['statuses'] as $status)
                    @continue($status === $purchaseOrderReview['status'])

                    <form method="POST" action="{{ url('/mobile/purchase-order/' . $purchaseOrderReview['id'] . '/status') }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $status }}">
                        <button class="or-mini-btn {{ $status === 'delayed' ? 'primary' : 'secondary' }}" type="submit">
                            Mark {{ ucfirst($status) }}
                        </button>
                    </form>
                @endforeach
            </div>
        </section>
    </main>
</div>
@endsection
