@extends('layout.mobile')

@section('title', $requestReview['request_no'])
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@php
    $purchaseRoles = app(\App\Services\PurchaseRoleService::class);
    $canApproveRequest = $purchaseRoles->canApproveRequests(auth()->user());
    $money = static fn (float $amount): string => $requestReview['currency'] . ' ' . number_format($amount, 2);
    $managerComment = old('manager_comment', $requestReview['manager_comment'] ?? '');
    $showSendbackPanel = old('status') === 'returned' || $errors->has('manager_comment');
@endphp

@section('mobile-content')
<div class="order-review-page">
    <header class="or-topbar">
        <a class="or-icon-btn" href="{{ url('/mobile/request-detail') }}" aria-label="Back to requests">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div class="or-title-block">
            <h2>Order Review</h2>
            <p>Manager approval and budget control</p>
        </div>
        <div class="or-avatar">{{ strtoupper(substr((string) auth()->user()?->name, 0, 1)) ?: 'M' }}</div>
    </header>

    <main>
        @if(session('success'))
            <div class="or-result-message show success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="or-result-message show danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="or-result-message show danger">{{ $errors->first() }}</div>
        @endif

        <section class="or-card or-hero">
            <h2>{{ $requestReview['request_no'] }}</h2>
            <p>
                Submitted by {{ $requestReview['requester'] }} for {{ $requestReview['department'] }}.
                Review request value, category budgets, and purchasing readiness.
            </p>
            <div class="or-hero-grid">
                <div class="or-hero-box">
                    <strong>{{ $requestReview['needed_by_short'] }}</strong>
                    <span>Requested delivery</span>
                </div>
                <div class="or-hero-box">
                    <strong id="heroItemCount">{{ $requestReview['items_count'] }} items</strong>
                    <span>Selected products</span>
                </div>
                <div class="or-hero-box">
                    <strong id="heroTotal">{{ $money($requestReview['total_cost']) }}</strong>
                    <span>Total request value</span>
                </div>
                <div class="or-hero-box">
                    <strong id="heroAlerts">{{ $requestReview['alert_count'] }} alerts</strong>
                    <span>Budget attention needed</span>
                </div>
            </div>
        </section>

        <div
            id="warningBanner"
            class="or-warning-banner {{ $requestReview['alert_count'] > 0 ? 'show ' . ($requestReview['over_budget_categories']->isNotEmpty() ? 'danger' : 'warning') : '' }}"
        >
            @if($requestReview['over_budget_categories']->isNotEmpty())
                Over-budget warning: approved current month spend plus this request for {{ $requestReview['over_budget_categories']->implode(', ') }} exceeded the category budget.
            @elseif($requestReview['warning_categories']->isNotEmpty())
                Budget caution: approved current month spend plus this request for {{ $requestReview['warning_categories']->implode(', ') }} is close to the category limit.
            @endif
        </div>

        <section class="or-card">
            <div class="or-section-head">
                <h3>Order Summary</h3>
                <span>Live Review</span>
            </div>
            <div class="or-summary-grid">
                <div class="or-summary-box">
                    <strong id="summaryTotal">{{ $money($requestReview['total_cost']) }}</strong>
                    <span>Current request total</span>
                </div>
                <div class="or-summary-box">
                    <strong id="budgetRemaining">{{ $money($requestReview['budget_remaining']) }}</strong>
                    <span>Budget left after request</span>
                </div>
            </div>
        </section>

        <section class="or-card">
            <div class="or-section-head">
                <h3>Request Details</h3>
                <span class="or-pill {{ $requestReview['status_tone'] }}" id="statusPill">{{ $requestReview['status_label'] }}</span>
            </div>
            <div class="or-meta-list">
                <div class="or-meta-row">
                    <div>
                        <strong>Requester</strong>
                        <small>{{ $requestReview['requester'] }} &middot; {{ $requestReview['department'] }}</small>
                    </div>
                    <div>
                        <strong>Requested On</strong>
                        <small>{{ $requestReview['created_at'] }}</small>
                    </div>
                </div>
                <div class="or-meta-row">
                    <div>
                        <strong>Request No.</strong>
                        <small>{{ $requestReview['request_no'] }}</small>
                    </div>
                    <div>
                        <strong>Delivery Date</strong>
                        <small>{{ $requestReview['needed_by'] }}</small>
                    </div>
                </div>
                <div class="or-meta-row">
                    <div>
                        <strong>Purchase Order No.</strong>
                        <small>{{ $requestReview['po_number'] }}</small>
                    </div>
                    <div>
                        <strong>Priority</strong>
                        <small>{{ $requestReview['priority'] }}</small>
                    </div>
                </div>
            </div>
        </section>

        <div class="or-section-head or-list-head">
            <h3>Monthly Budget</h3>
            <span>Approved + Current Request</span>
        </div>

        @forelse($requestReview['categories'] as $category)
            <article
                class="or-category-card"
                data-category="{{ $category['name'] }}"
                data-monthly-cost="{{ $category['monthly_cost'] }}"
                data-budget="{{ $category['budget'] }}"
            >
                <div class="or-category-top">
                    <div>
                        <div class="or-category-title">{{ $category['name'] }}</div>
                        <div class="or-category-sub">{{ $category['items_count'] }} items selected</div>
                    </div>
                    <div class="or-budget-box">
                        <strong class="or-budget-amount">{{ $money($category['monthly_cost']) }} / {{ $money($category['budget']) }}</strong>
                        <small class="or-budget-status">{{ $category['status_text'] }}</small>
                    </div>
                </div>
                <div class="or-progress {{ $category['tone'] }}">
                    <div style="width: {{ $category['progress_pct'] }}%"></div>
                </div>

                @foreach($category['items'] as $item)
                    <div class="or-item-row">
                        <div>
                            <div class="or-item-name">{{ $item['name'] }}</div>
                            <div class="or-item-meta">
                                {{ $item['quantity_label'] }}
                                &middot;
                                {{ $item['notes'] ? 'Note: ' . $item['notes'] : 'No note' }}
                                &middot;
                                {{ $item['supplier'] !== '-' ? 'Supplier: ' . $item['supplier'] : 'No supplier selected' }}
                            </div>
                        </div>
                        <div class="or-item-price">
                            {{ $item['has_price'] ? $money($item['line_total']) : 'Price pending' }}
                        </div>
                    </div>
                @endforeach
            </article>
        @empty
            <section class="or-card">
                <p class="or-empty-state">No request items added yet.</p>
            </section>
        @endforelse

        @if(filled($requestReview['admin_comment'] ?? null))
            <section class="or-card">
                <div class="or-section-head">
                    <h3>Admin Comment</h3>
                    <span>Latest response</span>
                </div>
                <p class="or-empty-state">{!! nl2br(e($requestReview['admin_comment'])) !!}</p>
            </section>
        @endif

        @if($canApproveRequest)
        <section class="or-card">
            <div class="or-section-head">
                <h3>Manager Comment</h3>
                <span>Required for send back</span>
            </div>

            <form id="sendbackStatusForm" method="POST" action="{{ $requestReview['status_action_url'] }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="returned">

                <div class="or-field">
                    <label for="managerComment">Comment for purchasing or requester</label>
                    <textarea
                        id="managerComment"
                        name="manager_comment"
                        maxlength="2000"
                        placeholder="Write comment, change request, or approval note..."
                        required
                    >{{ $managerComment }}</textarea>
                </div>

                <div id="sendbackPanel" class="or-sendback-panel {{ $showSendbackPanel ? 'show' : '' }}">
                    <div class="or-sendback-title">Send back with comment</div>
                    <p>Add a comment explaining what should be changed, then send this request back to the requester.</p>
                    <div class="or-sendback-actions">
                        <button class="or-mini-btn secondary" id="cancelSendbackBtn" type="button">Cancel</button>
                        <button class="or-mini-btn primary" id="confirmSendbackBtn" type="submit">Send Back</button>
                    </div>
                </div>
            </form>

            <div id="resultMessage" class="or-result-message"></div>
        </section>
        @endif
    </main>

    @if($canApproveRequest)
    <div class="or-sticky-bar">
        <div class="or-sticky-meta">
            <span>Budget alerts: <strong id="bottomAlerts">{{ $requestReview['alert_count'] }}</strong></span>
            <span>Total: <strong id="bottomTotal">{{ $money($requestReview['total_cost']) }}</strong></span>
        </div>
        <div class="or-action-grid">
            <form method="POST" action="{{ $requestReview['status_action_url'] }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <input type="hidden" name="manager_comment" class="status-comment-input">
                <button class="or-btn decline" id="declineBtn" type="submit">Decline</button>
            </form>
            <button class="or-btn modify" id="modifyBtn" type="button">Send Back</button>
            <form method="POST" action="{{ $requestReview['status_action_url'] }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="approved">
                <input type="hidden" name="manager_comment" class="status-comment-input">
                <input type="hidden" name="needed_by" id="confirmNeededByInput">
                <button class="or-btn confirm" id="confirmBtn" type="submit">Confirm</button>
            </form>
        </div>
    </div>
    @endif

    <div class="or-modal-backdrop" id="expiredDeliveryModal" hidden>
        <div class="or-modal" role="dialog" aria-modal="true" aria-labelledby="expiredDeliveryTitle">
            <h3 id="expiredDeliveryTitle">Delivery date is expired</h3>
            <p>Please select a future delivery date before confirming this request.</p>
            <form id="expiredDeliveryForm">
                <div class="or-field">
                    <label for="expiredDeliveryInput">New Delivery Date</label>
                    <input
                        id="expiredDeliveryInput"
                        type="datetime-local"
                        min="{{ $requestReview['min_needed_by_input'] }}"
                        required
                    >
                </div>
                <div id="expiredDeliveryError" class="or-inline-error"></div>
                <div class="or-modal-actions">
                    <button class="or-mini-btn secondary" id="cancelExpiredDeliveryBtn" type="button">Cancel</button>
                    <button class="or-mini-btn primary" type="submit">Update &amp; Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const categoryCards = Array.from(document.querySelectorAll('.or-category-card'));
const warningBanner = document.getElementById('warningBanner');
const summaryTotal = document.getElementById('summaryTotal');
const budgetRemaining = document.getElementById('budgetRemaining');
const heroTotal = document.getElementById('heroTotal');
const heroAlerts = document.getElementById('heroAlerts');
const heroItemCount = document.getElementById('heroItemCount');
const bottomAlerts = document.getElementById('bottomAlerts');
const bottomTotal = document.getElementById('bottomTotal');
const statusPill = document.getElementById('statusPill');
const managerComment = document.getElementById('managerComment');
const modifyBtn = document.getElementById('modifyBtn');
const confirmBtn = document.getElementById('confirmBtn');
const confirmStatusForm = confirmBtn ? confirmBtn.closest('form') : null;
const confirmNeededByInput = document.getElementById('confirmNeededByInput');
const declineBtn = document.getElementById('declineBtn');
const sendbackStatusForm = document.getElementById('sendbackStatusForm');
const sendbackPanel = document.getElementById('sendbackPanel');
const cancelSendbackBtn = document.getElementById('cancelSendbackBtn');
const resultMessage = document.getElementById('resultMessage');
const expiredDeliveryModal = document.getElementById('expiredDeliveryModal');
const expiredDeliveryForm = document.getElementById('expiredDeliveryForm');
const expiredDeliveryInput = document.getElementById('expiredDeliveryInput');
const expiredDeliveryError = document.getElementById('expiredDeliveryError');
const cancelExpiredDeliveryBtn = document.getElementById('cancelExpiredDeliveryBtn');
const currency = @json($requestReview['currency']);
const requestTotal = @json($requestReview['total_cost']);
const deliveryDateExpired = @json($requestReview['needed_by_is_past']);
const minNeededByInput = @json($requestReview['min_needed_by_input']);
let confirmWithUpdatedDelivery = false;

function formatMoney(value) {
    return `${currency} ${Number(value || 0).toFixed(2)}`;
}

function setResult(type, text) {
    if (!resultMessage) {
        return;
    }

    resultMessage.className = `or-result-message show ${type}`;
    resultMessage.textContent = text;
}

function clearResult() {
    if (!resultMessage) {
        return;
    }

    resultMessage.className = 'or-result-message';
    resultMessage.textContent = '';
}

function showSendbackPanel(message = '') {
    if (!sendbackPanel || !managerComment) {
        return;
    }

    sendbackPanel.classList.add('show');
    setStatus('Modification Requested', 'orange');

    if (message) {
        setResult('warning', message);
    } else {
        clearResult();
    }

    managerComment.focus();
}

function submitSendbackForm() {
    if (!sendbackStatusForm) {
        return;
    }

    if (sendbackStatusForm.requestSubmit) {
        sendbackStatusForm.requestSubmit();
        return;
    }

    sendbackStatusForm.submit();
}

function setStatus(text, variant) {
    statusPill.textContent = text;
    statusPill.className = `or-pill ${variant}`;
}

function showExpiredDeliveryModal() {
    expiredDeliveryError.className = 'or-inline-error';
    expiredDeliveryError.textContent = '';
    expiredDeliveryInput.value = minNeededByInput;
    expiredDeliveryModal.hidden = false;
    expiredDeliveryInput.focus();
}

function hideExpiredDeliveryModal() {
    expiredDeliveryModal.hidden = true;
}

function isFutureDateTime(value) {
    const selectedTime = new Date(value).getTime();

    return Number.isFinite(selectedTime) && selectedTime > Date.now();
}

function updateBudgetWarnings() {
    let monthlyTotal = 0;
    let totalBudget = 0;
    let alerts = 0;
    let totalItems = 0;
    const overBudgetCategories = [];
    const warningCategories = [];

    categoryCards.forEach((card) => {
        const monthlyCost = Number(card.dataset.monthlyCost || 0);
        const budget = Number(card.dataset.budget || 0);
        const usedPct = budget > 0 ? (monthlyCost / budget) * 100 : 0;
        const amountEl = card.querySelector('.or-budget-amount');
        const statusEl = card.querySelector('.or-budget-status');
        const progress = card.querySelector('.or-progress');
        const progressBar = progress.querySelector('div');

        monthlyTotal += monthlyCost;
        totalBudget += budget;
        totalItems += card.querySelectorAll('.or-item-row').length;
        amountEl.textContent = `${formatMoney(monthlyCost)} / ${formatMoney(budget)}`;
        progress.classList.remove('safe', 'warn', 'over');

        if (budget <= 0 && monthlyCost > 0) {
            alerts += 1;
            warningCategories.push(card.dataset.category);
            progress.classList.add('warn');
            progressBar.style.width = '100%';
            statusEl.textContent = 'Monthly budget not set';
        } else if (usedPct > 100) {
            alerts += 1;
            overBudgetCategories.push(card.dataset.category);
            progress.classList.add('over');
            progressBar.style.width = '100%';
            statusEl.textContent = `${usedPct.toFixed(0)}% of approved + this request - over budget`;
        } else if (usedPct >= 75) {
            alerts += 1;
            warningCategories.push(card.dataset.category);
            progress.classList.add('warn');
            progressBar.style.width = `${usedPct}%`;
            statusEl.textContent = `${usedPct.toFixed(1)}% of approved + this request - near limit`;
        } else {
            progress.classList.add('safe');
            progressBar.style.width = `${usedPct}%`;
            statusEl.textContent = `${usedPct.toFixed(0)}% of approved + this request`;
        }
    });

    summaryTotal.textContent = formatMoney(requestTotal);
    heroTotal.textContent = formatMoney(requestTotal);
    budgetRemaining.textContent = formatMoney(Math.max(0, totalBudget - monthlyTotal));
    heroAlerts.textContent = `${alerts} alerts`;
    heroItemCount.textContent = `${totalItems} items`;

    if (bottomTotal) {
        bottomTotal.textContent = formatMoney(requestTotal);
    }

    if (bottomAlerts) {
        bottomAlerts.textContent = String(alerts);
    }

    if (overBudgetCategories.length) {
        warningBanner.className = 'or-warning-banner show danger';
        warningBanner.textContent = `Over-budget warning: approved current month spend plus this request for ${overBudgetCategories.join(', ')} exceeded the category budget.`;
    } else if (warningCategories.length) {
        warningBanner.className = 'or-warning-banner show warning';
        warningBanner.textContent = `Budget caution: approved current month spend plus this request for ${warningCategories.join(', ')} is close to the category limit or has no budget set.`;
    } else {
        warningBanner.className = 'or-warning-banner';
        warningBanner.textContent = '';
    }

    return { overBudgetCategories, warningCategories };
}

if (modifyBtn) {
modifyBtn.addEventListener('click', () => {
    if (managerComment.value.trim()) {
        submitSendbackForm();
        return;
    }

    showSendbackPanel('Please add a manager comment before sending the request back.');
});
}

if (cancelSendbackBtn) {
cancelSendbackBtn.addEventListener('click', () => {
    sendbackPanel.classList.remove('show');
    clearResult();
    setStatus(@json($requestReview['status_label']), @json($requestReview['status_tone']));
});
}

if (sendbackStatusForm) {
sendbackStatusForm.addEventListener('submit', (event) => {
    const comment = managerComment.value.trim();

    if (!comment) {
        event.preventDefault();
        showSendbackPanel('Please add a manager comment before sending the request back.');
        return;
    }
});
}

if (confirmBtn) {
confirmBtn.addEventListener('click', () => {
    sendbackPanel.classList.remove('show');
    updateBudgetWarnings();
});
}

if (confirmStatusForm) {
confirmStatusForm.addEventListener('submit', (event) => {
    if (!deliveryDateExpired || confirmWithUpdatedDelivery) {
        return;
    }

    event.preventDefault();
    showExpiredDeliveryModal();
});
}

if (declineBtn) {
declineBtn.addEventListener('click', () => {
    sendbackPanel.classList.remove('show');
});
}

if (cancelExpiredDeliveryBtn) {
    cancelExpiredDeliveryBtn.addEventListener('click', hideExpiredDeliveryModal);
}

if (expiredDeliveryForm) {
expiredDeliveryForm.addEventListener('submit', (event) => {
    event.preventDefault();

    if (!isFutureDateTime(expiredDeliveryInput.value)) {
        expiredDeliveryError.className = 'or-inline-error show';
        expiredDeliveryError.textContent = 'Please select a delivery date greater than today.';
        return;
    }

    confirmNeededByInput.value = expiredDeliveryInput.value;
    confirmWithUpdatedDelivery = true;
    hideExpiredDeliveryModal();
    confirmStatusForm.requestSubmit();
});
}

document.querySelectorAll('.status-comment-input').forEach((input) => {
    input.form.addEventListener('submit', () => {
        input.value = managerComment.value.trim();
    });
});

updateBudgetWarnings();
</script>
@endpush
