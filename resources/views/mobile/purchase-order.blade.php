@extends('layout.mobile')

@section('title', $purchaseOrderReview['po_number'])
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@php
    $canSendOrder = $purchaseOrderReview['dispatch_status'] !== 'unassigned';
    $supplierContactParts = collect([
        $purchaseOrderReview['supplier_phone'],
        $purchaseOrderReview['supplier_email'],
    ])->filter(fn ($value) => $value !== '-')->values();
@endphp

@section('mobile-content')
<div class="order-review-page po-dispatch-page">
    <header class="or-topbar">
        <a class="or-icon-btn" href="{{ url('/mobile/orders') }}" aria-label="Back to purchase orders">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div class="or-title-block">
            <h1>Supplier Order List</h1>
            <p>Assign suppliers and dispatch approved orders</p>
        </div>
        @include('mobile.partials.profile-menu')
    </header>

    <main>
        @if(session('success'))
            <div class="or-result-message show success">{{ session('success') }}</div>
        @endif

        <section class="or-card or-hero">
            <h2>Approved Order Ready for Dispatch</h2>
            <p>
                Review {{ $purchaseOrderReview['po_number'] }} for {{ $purchaseOrderReview['supplier'] }} and send it by WhatsApp, Viber, or email.
            </p>
            <div class="or-hero-grid">
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['supplier_needed_count'] }}</strong>
                    <span>Suppliers needed</span>
                </div>
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['total_label'] }}</strong>
                    <span>Total dispatch value</span>
                </div>
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['po_number'] }}</strong>
                    <span>Main order number</span>
                </div>
                <div class="or-hero-box">
                    <strong>{{ $purchaseOrderReview['expected_delivery_short'] }}</strong>
                    <span>Requested delivery</span>
                </div>
            </div>
        </section>

        <section class="or-card">
            <div class="or-section-head">
                <h3>Dispatch Summary</h3>
                <span>Purchasing View</span>
            </div>
            <div class="or-summary-grid">
                <div class="or-summary-box">
                    <strong id="supplierOrderCount">1</strong>
                    <span>Supplier orders</span>
                </div>
                <div class="or-summary-box">
                    <strong id="alreadySentCount">{{ $purchaseOrderReview['sent_count'] }}</strong>
                    <span>Already sent</span>
                </div>
            </div>
        </section>

        <section class="or-card po-filter-card">
            <div class="or-section-head">
                <h3>Filter by Status</h3>
                <span>Quick Sort</span>
            </div>
            <div class="po-filters">
                <button class="po-filter-chip active" data-filter="all" type="button">All</button>
                <button class="po-filter-chip" data-filter="unassigned" type="button">Unassigned</button>
                <button class="po-filter-chip" data-filter="ready" type="button">Ready to Send</button>
                <button class="po-filter-chip" data-filter="sent" type="button">Sent</button>
                <button class="po-filter-chip" data-filter="delayed" type="button">Delayed</button>
            </div>
        </section>

        <article class="po-supplier-card" data-status="{{ $purchaseOrderReview['dispatch_status'] }}">
            <div class="po-supplier-head">
                <div>
                    <div class="po-supplier-name">
                        {{ $purchaseOrderReview['dispatch_status'] === 'unassigned' ? 'Unassigned Order Group' : $purchaseOrderReview['supplier'] }}
                    </div>
                    <div class="po-supplier-meta">
                        @if($supplierContactParts->isNotEmpty())
                            Supplier contact: {{ $supplierContactParts->implode(' / ') }}
                        @else
                            Supplier contact not available
                        @endif
                    </div>
                </div>
                <div class="po-supplier-actions">
                    <span class="or-pill {{ $purchaseOrderReview['dispatch_pill_tone'] }} po-status-pill">
                        {{ $purchaseOrderReview['dispatch_label'] }}
                    </span>
                </div>
            </div>

            <div class="po-order-meta">
                <div class="po-order-meta-box">
                    <small>Supplier PO</small>
                    <strong>{{ $purchaseOrderReview['po_number'] }}</strong>
                </div>
                <div class="po-order-meta-box">
                    <small>Delivery Date</small>
                    <strong>{{ $purchaseOrderReview['expected_delivery'] }}</strong>
                </div>
            </div>

            @forelse($purchaseOrderReview['categories'] as $category)
                <div class="po-category-block">
                    <div class="po-category-title">{{ $category['name'] }}</div>

                    @foreach($category['items'] as $item)
                        <div class="po-item-row">
                            <div>
                                <div class="po-item-name">{{ $item['name'] }}</div>
                                <div class="po-item-meta">
                                    Ordered {{ $item['ordered_label'] }}
                                    &middot;
                                    Received {{ $item['received_label'] }}
                                    &middot;
                                    Unit {{ $item['unit_price_label'] }}
                                </div>
                            </div>
                            <div class="po-item-right">{{ $item['line_total_label'] }}</div>
                        </div>
                    @endforeach
                </div>
            @empty
                <p class="or-empty-state">No purchase order items added yet.</p>
            @endforelse

            <div class="po-supplier-actions po-channel-actions">
                <button class="po-send-btn whatsapp" data-channel="whatsapp" type="button">WhatsApp</button>
                <button class="po-send-btn viber" data-channel="viber" type="button">Viber</button>
                <button class="po-send-btn email" data-channel="email" type="button">Email</button>
            </div>

            <div
                class="po-dispatch-log {{ $purchaseOrderReview['dispatch_status'] === 'sent' ? 'show success' : ($purchaseOrderReview['dispatch_status'] === 'unassigned' ? 'show warning' : '') }}"
            >
                @if($purchaseOrderReview['dispatch_status'] === 'sent')
                    Order already sent or confirmed with supplier.
                @elseif($purchaseOrderReview['dispatch_status'] === 'unassigned')
                    Supplier not assigned yet.
                @endif
            </div>

            <div class="po-message-preview">
                <div class="po-message-preview-header">
                    <strong>Supplier Message Preview</strong>
                    <span class="po-preview-channel-label">Channel</span>
                </div>
                <textarea class="po-message-text" aria-label="Supplier message preview"></textarea>
                <div class="po-message-preview-actions">
                    <button class="po-preview-btn secondary po-close-preview-btn" type="button">Close</button>
                    <button class="po-preview-btn primary po-confirm-send-btn" type="button">Send Now</button>
                </div>
            </div>
        </article>

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
                        <strong>Buyer</strong>
                        <small>{{ $purchaseOrderReview['buyer'] }}</small>
                    </div>
                    <div>
                        <strong>Request No.</strong>
                        <small>{{ $purchaseOrderReview['request_no'] }}</small>
                    </div>
                </div>
                <div class="or-meta-row">
                    <div>
                        <strong>Requester</strong>
                        <small>{{ $purchaseOrderReview['requester'] }}</small>
                    </div>
                    <div>
                        <strong>Department</strong>
                        <small>{{ $purchaseOrderReview['department'] }}</small>
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
                    <strong>{{ $purchaseOrderReview['items_count'] }} lines</strong>
                    <span>Purchase order items</span>
                </div>
            </div>
            <div class="or-progress {{ $purchaseOrderReview['received_percent'] >= 100 ? 'safe' : 'warn' }}">
                <div style="width: {{ $purchaseOrderReview['received_percent'] }}%"></div>
            </div>
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

        <form id="poSendStatusForm" method="POST" action="{{ url('/mobile/purchase-order/' . $purchaseOrderReview['id'] . '/status') }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="sent">
            <input id="poSendChannel" type="hidden" name="channel" value="email">
        </form>
    </main>

    <div class="or-sticky-bar">
        <div class="or-sticky-meta">
            <span>Ready to send: <strong id="readyCount">{{ $purchaseOrderReview['ready_count'] }}</strong></span>
            <span>Sent: <strong id="sentCount">{{ $purchaseOrderReview['sent_count'] }}</strong></span>
        </div>
        <div class="or-action-grid po-dispatch-footer-actions">
            <button class="or-btn modify" id="exportBtn" type="button">Export PO</button>
            <button class="or-btn confirm" id="sendAllBtn" type="button" @disabled(!$canSendOrder)>Send Ready PO</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const filterChips = Array.from(document.querySelectorAll('.po-filter-chip'));
const supplierCards = Array.from(document.querySelectorAll('.po-supplier-card'));
const readyCount = document.getElementById('readyCount');
const sentCount = document.getElementById('sentCount');
const alreadySentCount = document.getElementById('alreadySentCount');
const sendAllBtn = document.getElementById('sendAllBtn');
const exportBtn = document.getElementById('exportBtn');
const sendStatusForm = document.getElementById('poSendStatusForm');
const sendChannelInput = document.getElementById('poSendChannel');
let activePreviewCard = null;
let pendingChannel = 'email';

function channelLabel(channel) {
    const labels = {
        whatsapp: 'WhatsApp',
        viber: 'Viber',
        email: 'Email',
    };

    return labels[channel] || 'Email';
}

function setLog(card, type, text) {
    const log = card.querySelector('.po-dispatch-log');

    if (!log) {
        return;
    }

    log.className = `po-dispatch-log show ${type}`;
    log.textContent = text;
}

function updateCounts() {
    const ready = supplierCards.filter((card) => card.dataset.status === 'ready').length;
    const sent = supplierCards.filter((card) => card.dataset.status === 'sent').length;

    readyCount.textContent = String(ready);
    sentCount.textContent = String(sent);
    alreadySentCount.textContent = String(sent);
}

function buildSupplierMessage(card, channel) {
    const supplier = card.querySelector('.po-supplier-name')?.textContent?.trim() || 'Supplier';
    const orderBoxes = card.querySelectorAll('.po-order-meta-box strong');
    const poNumber = orderBoxes[0]?.textContent?.trim() || 'PO Number';
    const deliveryDate = orderBoxes[1]?.textContent?.trim() || 'Delivery Date';
    const parts = [
        `Hello ${supplier},`,
        '',
        'Please find our order details below:',
        '',
        `PO Number: ${poNumber}`,
        `Delivery Date: ${deliveryDate}`,
        '',
    ];

    card.querySelectorAll('.po-category-block').forEach((block) => {
        const category = block.querySelector('.po-category-title')?.textContent?.trim() || 'Category';
        parts.push(`[${category}]`);

        block.querySelectorAll('.po-item-row').forEach((row) => {
            const name = row.querySelector('.po-item-name')?.textContent?.trim() || '';
            const meta = row.querySelector('.po-item-meta')?.textContent?.trim() || '';
            const price = row.querySelector('.po-item-right')?.textContent?.trim() || '';

            parts.push(`- ${name} - ${meta} - ${price}`);
        });

        parts.push('');
    });

    parts.push('Please confirm availability and delivery.');
    parts.push('');
    parts.push('Thank you.');
    parts.push('PurchaseFlow Restaurant');

    if (channel === 'email') {
        return `Subject: Purchase Order ${poNumber} - Delivery ${deliveryDate}\n\n${parts.join('\n')}`;
    }

    return parts.join('\n');
}

function openPreview(card, channel) {
    if (card.dataset.status === 'unassigned') {
        setLog(card, 'warning', 'Please assign a supplier before sending this order.');
        return;
    }

    activePreviewCard = card;
    pendingChannel = channel;

    const preview = card.querySelector('.po-message-preview');
    const textarea = card.querySelector('.po-message-text');
    const label = card.querySelector('.po-preview-channel-label');

    if (label) {
        label.textContent = channelLabel(channel);
    }

    if (textarea) {
        textarea.value = buildSupplierMessage(card, channel);
    }

    if (preview) {
        preview.classList.add('show');
    }
}

filterChips.forEach((chip) => {
    chip.addEventListener('click', () => {
        filterChips.forEach((item) => item.classList.remove('active'));
        chip.classList.add('active');

        const filter = chip.dataset.filter;

        supplierCards.forEach((card) => {
            card.classList.toggle('hidden', filter !== 'all' && card.dataset.status !== filter);
        });
    });
});

supplierCards.forEach((card) => {
    card.querySelectorAll('.po-send-btn').forEach((button) => {
        button.addEventListener('click', () => {
            openPreview(card, button.dataset.channel || 'email');
        });
    });

    const preview = card.querySelector('.po-message-preview');
    const closePreviewBtn = card.querySelector('.po-close-preview-btn');
    const confirmSendBtn = card.querySelector('.po-confirm-send-btn');

    if (closePreviewBtn) {
        closePreviewBtn.addEventListener('click', () => {
            preview?.classList.remove('show');
        });
    }

    if (confirmSendBtn) {
        confirmSendBtn.addEventListener('click', () => {
            if (!activePreviewCard) {
                return;
            }

            sendChannelInput.value = pendingChannel;
            confirmSendBtn.disabled = true;
            confirmSendBtn.textContent = 'Sending...';
            sendStatusForm.submit();
        });
    }
});

if (exportBtn) {
    exportBtn.addEventListener('click', () => {
        window.print();
    });
}

if (sendAllBtn) {
    sendAllBtn.addEventListener('click', () => {
        const readyCard = supplierCards.find((card) => card.dataset.status === 'ready');
        const fallbackCard = supplierCards[0];

        if (readyCard) {
            openPreview(readyCard, 'email');
            return;
        }

        if (fallbackCard?.dataset.status === 'unassigned') {
            setLog(fallbackCard, 'warning', 'Supplier must be assigned before dispatch.');
            return;
        }

        if (fallbackCard) {
            openPreview(fallbackCard, 'email');
        }
    });
}

updateCounts();
</script>
@endpush
