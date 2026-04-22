@extends('layout.mobile')

@section('title', 'Quick Add')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@section('mobile-content')
<div class="app-container">
    <header class="app-header">
        <button class="back-btn icon-btn" type="button" aria-label="Go back" onclick="window.location.href='{{ url('/mobile/dashboard') }}'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <div class="qa-header-text">
            <h1>New Kitchen Request</h1>
            <p>Quick Add workflow</p>
        </div>
        <button class="dots-btn icon-btn" type="button" aria-label="More options">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H5.01M12 12H12.01M19 12H19.01" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </button>
    </header>

    <main class="content qa-content">
        <section class="card flex-row-card">
            <div class="input-col">
                <label class="qa-label" for="needed_by">NEEDED BY</label>
                <input type="datetime-local" id="needed_by" class="qa-input" value="{{ now()->addDay()->format('Y-m-d\\TH:i') }}">
            </div>
            <div class="input-col">
                <label class="qa-label" for="priority">PRIORITY</label>
                <div class="select-wrapper">
                    <select id="priority" class="qa-select">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="action-tabs">
            <button class="tab-btn active" type="button">Search Product</button>
            <button class="tab-btn" type="button" onclick="lookupBarcode()">Scan</button>
            <button class="tab-btn" type="button">Templates</button>
        </section>

        <section class="card">
            <label class="qa-label" for="barcodeInput">BARCODE INPUT DEMO</label>
            <div class="lookup-group">
                <input type="text" id="barcodeInput" class="qa-input" value="{{ $scannedProduct['barcode'] }}">
                <button class="lookup-btn" type="button" onclick="lookupBarcode()">Lookup</button>
            </div>
            <p class="qa-help-text">Demo barcodes: 1234567890123, 1234567890456, 9988123412000, 7722009911001</p>
        </section>

        <div id="lookupContent" hidden>
            <div id="barcodeResult"></div>

            <section class="card">
                <div class="flex-row-card no-padding">
                    <div class="input-col" style="flex: 1.5;">
                        <label class="qa-label" for="quantityInput">QUANTITY</label>
                        <input type="number" id="quantityInput" class="qa-input" value="2.5" step="0.1">
                    </div>
                    <div class="input-col" style="flex: 1;">
                        <label class="qa-label" for="unitInput">UNIT</label>
                        <input type="text" id="unitInput" class="qa-input" value="{{ $scannedProduct['unit'] }}">
                    </div>
                </div>
                <div class="input-col" style="margin-top: 16px;">
                    <label class="qa-label" for="notesInput">NOTES</label>
                    <textarea id="notesInput" class="qa-textarea" rows="2">Ripe if possible</textarea>
                </div>
                <button class="qa-primary-btn" type="button" onclick="addCurrentProductToBasket()">Add Item</button>
            </section>

            <section class="card request-basket">
                <div class="qa-card-header">
                    <h3>Request Basket</h3>
                    <span class="text-blue-bold"><span id="basketCount">0</span> items</span>
                </div>
                <div id="basketItems">
                    <p class="qa-help-text">No products added yet.</p>
                </div>
                <button class="qa-primary-btn" id="submitRequestBtn" type="button" onclick="submitRequest()" disabled>Submit Request</button>
            </section>
        </div>

        <div id="lookupMessage"></div>
    </main>

    <nav class="bottom-nav">
        <a href="{{ url('/mobile/dashboard') }}" class="nav-item">Home</a>
        <a href="{{ url('/mobile/request-detail') }}" class="nav-item">Requests</a>
        <a href="{{ url('/mobile/quick-add') }}" class="nav-item active">Templates</a>
        <a href="{{ url('/mobile/purchasing') }}" class="nav-item">Purchasing</a>
    </nav>
</div>
@endsection

@push('styles')
<style>
    #lookupContent[hidden] {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
let currentProduct = null;
let basketItems = [];

async function lookupBarcode() {
    const code = document.getElementById('barcodeInput').value.trim();
    const result = document.getElementById('barcodeResult');
    const content = document.getElementById('lookupContent');
    const message = document.getElementById('lookupMessage');
    if (!code) return;

    try {
        const response = await fetch('/api/products/barcode/' + encodeURIComponent(code));
        const payload = await response.json();

        if (!response.ok || !payload.success) {
            content.hidden = true;
            message.innerHTML = '<section class="card"><p class="qa-help-text">Barcode not found. Try another product barcode.</p></section>';
            return;
        }

        const product = payload.data;
        currentProduct = product;
        message.innerHTML = '';
        content.hidden = false;
        result.innerHTML = `
            <section class="card tomato-card">
                <div class="qa-card-header align-start">
                    <div>
                        <h3>${escapeHtml(product.name)}</h3>
                        <p class="subtitle">Auto-filled from barcode lookup</p>
                    </div>
                    <span class="badge badge-light-blue">Scanned</span>
                </div>
                <div class="info-grid">
                    <div class="info-box"><label>CATEGORY</label><p>${escapeHtml(product.category)}</p></div>
                    <div class="info-box"><label>UNIT</label><p>${escapeHtml(product.unit || '-')}</p></div>
                    <div class="info-box"><label>SUPPLIER</label><p>${escapeHtml(product.preferred_supplier)}</p></div>
                    <div class="info-box"><label>PACK SIZE</label><p>${escapeHtml(product.pack_size || 'Standard')}</p></div>
                </div>
                <div class="dashed-box">
                    <p>Scanned code: <strong>${escapeHtml(product.barcode || '-')}</strong></p>
                </div>
            </section>
        `;
        document.getElementById('quantityInput').value = '';
        document.getElementById('unitInput').value = product.unit || '';
        document.getElementById('notesInput').value = '';
    } catch (e) {
        content.hidden = true;
        message.innerHTML = '<section class="card"><p class="qa-help-text">Barcode lookup failed.</p></section>';
    }
}

function addCurrentProductToBasket() {
    if (!currentProduct) return;

    const quantity = parseFloat(document.getElementById('quantityInput').value || 0);

    if (quantity <= 0) {
        document.getElementById('lookupMessage').innerHTML = '<section class="card"><p class="qa-help-text">Enter a quantity greater than 0.</p></section>';
        return;
    }

    const item = {
        product_id: currentProduct.id,
        name: currentProduct.name,
        category: currentProduct.category,
        unit: currentProduct.unit || '',
        supplier: currentProduct.preferred_supplier || '-',
        supplier_id: currentProduct.supplier_id || null,
        quantity,
        notes: document.getElementById('notesInput').value.trim(),
    };

    const existingIndex = basketItems.findIndex((basketItem) => basketItem.product_id === item.product_id);

    if (existingIndex >= 0) {
        basketItems[existingIndex] = item;
    } else {
        basketItems.push(item);
    }

    document.getElementById('lookupMessage').innerHTML = '';
    renderBasket();
}

function removeBasketItem(index) {
    basketItems.splice(index, 1);
    renderBasket();
}

function renderBasket() {
    const basket = document.getElementById('basketItems');
    const submitButton = document.getElementById('submitRequestBtn');

    document.getElementById('basketCount').textContent = basketItems.length;
    submitButton.disabled = basketItems.length === 0;

    if (!basketItems.length) {
        basket.innerHTML = '<p class="qa-help-text">No products added yet.</p>';
        return;
    }

    basket.innerHTML = basketItems.map((item, index) => `
        <div class="basket-item">
            <div class="basket-item-info">
                <h4>${escapeHtml(item.name)}</h4>
                <p>${escapeHtml(item.supplier)} &middot; ${escapeHtml(item.category)}</p>
            </div>
            <div class="basket-item-actions">
                <span class="qty-badge">${escapeHtml(formatQuantity(item.quantity, item.unit))}</span>
                <div class="action-links">
                    <a href="#" onclick="removeBasketItem(${index}); return false;">Remove</a>
                </div>
            </div>
        </div>
        ${index < basketItems.length - 1 ? '<hr class="qa-divider">' : ''}
    `).join('');
}

async function submitRequest() {
    if (!basketItems.length) return;

    const button = document.getElementById('submitRequestBtn');
    const message = document.getElementById('lookupMessage');
    button.disabled = true;
    button.textContent = 'Submitting...';

    try {
        const response = await fetch('{{ url('/mobile/quick-add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                needed_by: document.getElementById('needed_by').value,
                priority: document.getElementById('priority').value,
                items: basketItems.map((item) => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    supplier_id: item.supplier_id,
                    notes: item.notes,
                })),
            }),
        });

        const payload = await response.json();

        if (!response.ok || !payload.success) {
            const errors = payload.errors ? Object.values(payload.errors).flat().join(' ') : 'Request submit failed.';
            message.innerHTML = `<section class="card"><p class="qa-help-text">${escapeHtml(errors)}</p></section>`;
            button.disabled = false;
            button.textContent = 'Submit Request';
            return;
        }

        basketItems = [];
        currentProduct = null;
        renderBasket();
        document.getElementById('lookupContent').hidden = true;
        document.getElementById('barcodeInput').value = '';
        message.innerHTML = `<section class="card"><p class="qa-help-text">Created ${escapeHtml(payload.request.request_no)} successfully.</p></section>`;
        button.textContent = 'Submit Request';
    } catch (e) {
        message.innerHTML = '<section class="card"><p class="qa-help-text">Request submit failed.</p></section>';
        button.disabled = false;
        button.textContent = 'Submit Request';
    }
}

function formatQuantity(quantity, unit) {
    const cleanQuantity = Number(quantity).toLocaleString(undefined, {
        maximumFractionDigits: 2,
    });

    return `${cleanQuantity}${unit ? ' ' + unit : ''}`;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));
}

document.getElementById('barcodeInput').addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        lookupBarcode();
    }
});
</script>
@endpush
