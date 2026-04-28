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
    $hasReceiveErrors = $errors->has('receipts')
        || collect($errors->getMessages())->keys()->contains(fn ($key) => str_starts_with($key, 'receipts.'));
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
        @if($errors->any())
            <div class="or-result-message show danger">{{ $errors->first() }}</div>
        @endif

        <section class="or-card or-hero">
            <h2>Approved Order Ready for Dispatch</h2>
            <p>
                Review {{ $purchaseOrderReview['category_summary'] }} order {{ $purchaseOrderReview['po_number'] }} for {{ $purchaseOrderReview['supplier'] }} and send it by WhatsApp or email.
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
                    <strong>{{ $purchaseOrderReview['category_summary'] }}</strong>
                    <span>Order part</span>
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
                    <small>Category Part</small>
                    <strong>{{ $purchaseOrderReview['category_summary'] }}</strong>
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
                <div class="po-preview-error or-inline-error"></div>
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
                @if($purchaseOrderReview['status'] === 'partial')
                    <button class="or-mini-btn primary po-receive-open-btn" type="button">
                        Update Receiving
                    </button>
                @endif

                @foreach($purchaseOrderReview['status_actions'] as $status)
                    @continue($status === $purchaseOrderReview['status'])

                    @if($status === 'sent')
                        <button
                            class="or-mini-btn secondary po-status-send-btn"
                            type="button"
                            @disabled(!$canSendOrder)
                        >
                            Mark Sent
                        </button>

                        @continue
                    @endif

                    @if($status === 'partial')
                        <button class="or-mini-btn secondary po-receive-open-btn" type="button">
                            Mark Partial
                        </button>

                        @continue
                    @endif

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

    <div class="or-modal-backdrop" id="poReceiveModal" hidden>
        <div class="or-modal po-receive-mobile-modal" role="dialog" aria-modal="true" aria-labelledby="poReceiveTitle">
            <form method="POST" action="{{ url('/mobile/purchase-order/' . $purchaseOrderReview['id'] . '/receiving') }}" id="poReceiveForm">
                @csrf
                @method('PATCH')

                <div class="po-message-preview-header">
                    <strong id="poReceiveTitle">Receive PO Items</strong>
                    <span>{{ $purchaseOrderReview['po_number'] }}</span>
                </div>

                <p>Update total received quantities for this PO. It will complete automatically when every line is fully received.</p>

                <div class="po-receive-list">
                    @forelse($purchaseOrderReview['items'] as $item)
                        <label class="po-receive-row">
                            <span>
                                <strong>{{ $item['name'] }}</strong>
                                <small>
                                    Requested {{ $item['ordered_label'] }}
                                    &middot;
                                    Current {{ $item['received_label'] }}
                                </small>
                            </span>
                            <input
                                class="po-receive-input"
                                type="number"
                                name="receipts[{{ $item['id'] }}]"
                                value="{{ old('receipts.' . $item['id'], number_format((float) $item['received_quantity'], 2, '.', '')) }}"
                                min="0"
                                max="{{ number_format((float) $item['ordered_quantity'], 2, '.', '') }}"
                                step="0.01"
                                data-po-receive-input
                                data-ordered="{{ number_format((float) $item['ordered_quantity'], 2, '.', '') }}"
                                aria-label="Received quantity for {{ $item['name'] }}"
                            >
                        </label>
                    @empty
                        <div class="or-empty-state">No purchase order items added yet.</div>
                    @endforelse
                </div>

                <div id="poReceiveValidationError" class="or-inline-error"></div>

                <div class="po-receive-actions">
                    <button class="po-preview-btn secondary po-receive-close-btn" type="button">Cancel</button>
                    <button class="po-preview-btn secondary" id="poReceiveAllBtn" type="button" @disabled($purchaseOrderReview['items_count'] === 0)>Receive All</button>
                    <button class="po-preview-btn primary" type="submit" @disabled($purchaseOrderReview['items_count'] === 0)>Submit</button>
                </div>
            </form>
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
const statusSendButtons = Array.from(document.querySelectorAll('.po-status-send-btn'));
const receiveModal = document.getElementById('poReceiveModal');
const receiveForm = document.getElementById('poReceiveForm');
const receiveOpenButtons = Array.from(document.querySelectorAll('.po-receive-open-btn'));
const receiveCloseBtn = receiveModal.querySelector('.po-receive-close-btn');
const receiveAllBtn = document.getElementById('poReceiveAllBtn');
const receiveValidationError = document.getElementById('poReceiveValidationError');
let activePreviewCard = null;
let pendingChannel = 'email';
const emailPreviewSubject = @json($purchaseOrderReview['email_preview_subject']);
const emailPreviewHtml = @json($purchaseOrderReview['email_preview_html']);
const supplierMessageText = @json($purchaseOrderReview['supplier_message_text']);
const supplierPhone = @json($purchaseOrderReview['supplier_phone']);
const shouldOpenReceiveModal = @json($hasReceiveErrors);

function channelLabel(channel) {
    const labels = {
        whatsapp: 'WhatsApp',
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

function openPreview(card, channel) {
    if (card.dataset.status === 'unassigned') {
        setLog(card, 'warning', 'Please assign a supplier before sending this order.');
        return;
    }

    activePreviewCard = card;
    pendingChannel = channel;

    supplierCards.forEach((supplierCard) => {
        supplierCard.querySelector('.po-message-preview')?.classList.remove('show');
    });

    const preview = card.querySelector('.po-message-preview');
    const textarea = card.querySelector('.po-message-text');
    const label = card.querySelector('.po-preview-channel-label');
    const error = card.querySelector('.po-preview-error');

    if (label) {
        label.textContent = channelLabel(channel);
    }

    if (textarea) {
        textarea.value = `${emailPreviewSubject}\n\n${supplierMessageText}`;
    }

    if (error) {
        error.className = 'po-preview-error or-inline-error';
        error.textContent = '';
    }

    preview?.classList.add('show');
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
    const previewError = card.querySelector('.po-preview-error');
    const previewText = card.querySelector('.po-message-text');

    closePreviewBtn?.addEventListener('click', () => {
        preview?.classList.remove('show');
    });

    confirmSendBtn?.addEventListener('click', () => {
        if (!activePreviewCard) {
            return;
        }

        if (pendingChannel === 'whatsapp') {
            const phone = normalizedWhatsAppPhone(supplierPhone);

            if (!phone) {
                previewError.className = 'po-preview-error or-inline-error show';
                previewError.textContent = 'Supplier phone number is not available for WhatsApp.';
                return;
            }

            window.open(`https://wa.me/${phone}?text=${encodeURIComponent(previewText?.value || supplierMessageText)}`, '_blank', 'noopener');
        }

        sendChannelInput.value = pendingChannel;
        confirmSendBtn.disabled = true;
        confirmSendBtn.textContent = 'Sending...';
        sendStatusForm.submit();
    });
});

function normalizedWhatsAppPhone(phone) {
    return String(phone || '').replace(/\D/g, '');
}

statusSendButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const readyCard = supplierCards.find((card) => card.dataset.status === 'ready');
        const fallbackCard = supplierCards[0];

        if (readyCard) {
            openPreview(readyCard, 'email');
            return;
        }

        if (fallbackCard?.dataset.status === 'unassigned') {
            setLog(fallbackCard, 'warning', 'Supplier must be assigned before marking this order sent.');
            return;
        }

        if (fallbackCard) {
            openPreview(fallbackCard, 'email');
        }
    });
});

function openReceiveModal() {
    receiveValidationError.className = 'or-inline-error';
    receiveValidationError.textContent = '';
    receiveModal.hidden = false;
}

function closeReceiveModal() {
    receiveModal.hidden = true;
}

receiveOpenButtons.forEach((button) => {
    button.addEventListener('click', openReceiveModal);
});

receiveCloseBtn.addEventListener('click', closeReceiveModal);

receiveModal.addEventListener('click', (event) => {
    if (event.target === receiveModal) {
        closeReceiveModal();
    }
});

if (receiveAllBtn) {
    receiveAllBtn.addEventListener('click', () => {
        receiveForm.querySelectorAll('[data-po-receive-input]').forEach((input) => {
            input.value = input.dataset.ordered || '0.00';
        });
    });
}

receiveForm.addEventListener('submit', (event) => {
    const invalidInput = Array.from(receiveForm.querySelectorAll('[data-po-receive-input]')).find((input) => {
        const receivedQuantity = Number(input.value || 0);
        const orderedQuantity = Number(input.dataset.ordered || 0);

        return receivedQuantity < 0 || receivedQuantity > orderedQuantity;
    });

    if (!invalidInput) {
        return;
    }

    event.preventDefault();
    receiveValidationError.className = 'or-inline-error show';
    receiveValidationError.textContent = 'Received quantity cannot be negative or greater than requested quantity.';
    invalidInput.focus();
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

if (shouldOpenReceiveModal) {
    openReceiveModal();
}
</script>
@endpush
