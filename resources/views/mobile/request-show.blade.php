@extends('layout.mobile')

@section('title', $requestReview['request_no'])
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@php
    $money = static fn (float $amount): string => $requestReview['currency'] . ' ' . number_format($amount, 2);
@endphp

@section('mobile-content')
<div class="order-review-page">
    <header class="or-topbar">
        <a class="or-icon-btn" href="{{ url('/mobile/request-detail') }}" aria-label="Back to requests">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div class="or-title-block">
            <h1>Order Review</h1>
            <p>Manager approval and budget control</p>
        </div>
        <div class="or-avatar">{{ strtoupper(substr((string) auth()->user()?->name, 0, 1)) ?: 'M' }}</div>
    </header>

    <main>
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
                Over-budget warning: approved current month spend for {{ $requestReview['over_budget_categories']->implode(', ') }} exceeded the category budget.
            @elseif($requestReview['warning_categories']->isNotEmpty())
                Budget caution: approved current month spend for {{ $requestReview['warning_categories']->implode(', ') }} is close to the category limit.
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
                    <span>Approved month budget left</span>
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
            <h3>Category Pricing &amp; Budget</h3>
            <span>Approved Month Expense</span>
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

        <section class="or-card">
            <div class="or-section-head">
                <h3>Manager Comment</h3>
                <span>Required for send back</span>
            </div>
            <div class="or-field">
                <label for="managerComment">Comment for purchasing or requester</label>
                <textarea id="managerComment" placeholder="Write comment, change request, or approval note..."></textarea>
            </div>

            <div id="sendbackPanel" class="or-sendback-panel">
                <div class="or-sendback-title">Send back with comment</div>
                <p>Add a comment explaining what should be changed, then send this request back to the requester.</p>
                <div class="or-sendback-actions">
                    <button class="or-mini-btn secondary" id="cancelSendbackBtn" type="button">Cancel</button>
                    <button class="or-mini-btn primary" id="confirmSendbackBtn" type="button">Send Back</button>
                </div>
            </div>

            <div id="resultMessage" class="or-result-message"></div>
        </section>
    </main>

    <div class="or-sticky-bar">
        <div class="or-sticky-meta">
            <span>Budget alerts: <strong id="bottomAlerts">{{ $requestReview['alert_count'] }}</strong></span>
            <span>Total: <strong id="bottomTotal">{{ $money($requestReview['total_cost']) }}</strong></span>
        </div>
        <div class="or-action-grid">
            <button class="or-btn decline" id="declineBtn" type="button">Decline</button>
            <button class="or-btn modify" id="modifyBtn" type="button">Modify</button>
            <button class="or-btn confirm" id="confirmBtn" type="button">Confirm</button>
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
const declineBtn = document.getElementById('declineBtn');
const sendbackPanel = document.getElementById('sendbackPanel');
const cancelSendbackBtn = document.getElementById('cancelSendbackBtn');
const confirmSendbackBtn = document.getElementById('confirmSendbackBtn');
const resultMessage = document.getElementById('resultMessage');
const currency = @json($requestReview['currency']);
const requestTotal = @json($requestReview['total_cost']);

function formatMoney(value) {
    return `${currency} ${Number(value || 0).toFixed(2)}`;
}

function setResult(type, text) {
    resultMessage.className = `or-result-message show ${type}`;
    resultMessage.textContent = text;
}

function clearResult() {
    resultMessage.className = 'or-result-message';
    resultMessage.textContent = '';
}

function setStatus(text, variant) {
    statusPill.textContent = text;
    statusPill.className = `or-pill ${variant}`;
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
            statusEl.textContent = `${usedPct.toFixed(0)}% of approved current month spend - over budget`;
        } else if (usedPct >= 75) {
            alerts += 1;
            warningCategories.push(card.dataset.category);
            progress.classList.add('warn');
            progressBar.style.width = `${usedPct}%`;
            statusEl.textContent = `${usedPct.toFixed(1)}% of approved current month spend - near limit`;
        } else {
            progress.classList.add('safe');
            progressBar.style.width = `${usedPct}%`;
            statusEl.textContent = `${usedPct.toFixed(0)}% of approved current month spend`;
        }
    });

    summaryTotal.textContent = formatMoney(requestTotal);
    heroTotal.textContent = formatMoney(requestTotal);
    bottomTotal.textContent = formatMoney(requestTotal);
    budgetRemaining.textContent = formatMoney(Math.max(0, totalBudget - monthlyTotal));
    heroAlerts.textContent = `${alerts} alerts`;
    bottomAlerts.textContent = String(alerts);
    heroItemCount.textContent = `${totalItems} items`;

    if (overBudgetCategories.length) {
        warningBanner.className = 'or-warning-banner show danger';
        warningBanner.textContent = `Over-budget warning: approved current month spend for ${overBudgetCategories.join(', ')} exceeded the category budget.`;
    } else if (warningCategories.length) {
        warningBanner.className = 'or-warning-banner show warning';
        warningBanner.textContent = `Budget caution: approved current month spend for ${warningCategories.join(', ')} is close to the category limit or has no budget set.`;
    } else {
        warningBanner.className = 'or-warning-banner';
        warningBanner.textContent = '';
    }

    return { overBudgetCategories, warningCategories };
}

modifyBtn.addEventListener('click', () => {
    clearResult();
    sendbackPanel.classList.add('show');
    managerComment.focus();
    setStatus('Modification Requested', 'orange');
});

cancelSendbackBtn.addEventListener('click', () => {
    sendbackPanel.classList.remove('show');
    clearResult();
    setStatus(@json($requestReview['status_label']), @json($requestReview['status_tone']));
});

confirmSendbackBtn.addEventListener('click', () => {
    const comment = managerComment.value.trim();

    if (!comment) {
        setResult('warning', 'Please add a manager comment before sending the request back.');
        return;
    }

    sendbackPanel.classList.remove('show');
    setStatus('Sent Back to Requester', 'orange');
    setResult('warning', `Request sent back with comment: "${comment}"`);
});

confirmBtn.addEventListener('click', () => {
    sendbackPanel.classList.remove('show');
    const state = updateBudgetWarnings();

    if (state.overBudgetCategories.length) {
        setStatus('Confirmed with Budget Exception', 'red');
        setResult('danger', `Request confirmed, but ${state.overBudgetCategories.join(', ')} is over budget.`);
    } else if (state.warningCategories.length) {
        setStatus('Confirmed with Warning', 'orange');
        setResult('warning', `Request confirmed. ${state.warningCategories.join(', ')} needs budget attention.`);
    } else {
        setStatus('Confirmed', 'green');
        setResult('success', 'Request confirmed successfully and sent to purchasing.');
    }
});

declineBtn.addEventListener('click', () => {
    sendbackPanel.classList.remove('show');
    setStatus('Declined', 'red');
    setResult('danger', 'Request declined. Add a comment if you want to explain the reason to the requester.');
});

updateBudgetWarnings();
</script>
@endpush
