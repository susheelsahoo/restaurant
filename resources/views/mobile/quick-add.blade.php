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
            <button class="tab-btn" type="button" onclick="lookupProduct()">Scan</button>
            <button class="tab-btn" type="button">Templates</button>
        </section>

        <section class="card">
            <label class="qa-label" for="barcodeInput">PRODUCT NAME OR BARCODE</label>
            <div class="lookup-group">
                <input type="text" id="barcodeInput" class="qa-input" list="productSuggestions" value="{{ $scannedProduct['barcode'] }}" placeholder="Type product name or barcode">
                <datalist id="productSuggestions"></datalist>
                <button class="lookup-btn" type="button" onclick="lookupProduct()">Lookup</button>
            </div>
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

    @include('mobile.partials.bottom-nav')
</div>
@endsection

@push('scripts')
<script>
let currentProduct = null;
let basketItems = [];
let productSuggestions = [];
let lookupTimer = null;

async function lookupProduct() {
    const code = document.getElementById('barcodeInput').value.trim();
    const result = document.getElementById('barcodeResult');
    const content = document.getElementById('lookupContent');
    const message = document.getElementById('lookupMessage');
    if (!code) return;

    try {
        let product = productSuggestions.find((item) => {
            return item.name.toLowerCase() === code.toLowerCase() || (item.barcode && item.barcode === code);
        }) || null;

        if (!product) {
            const barcodeResponse = await fetch('/api/products/barcode/' + encodeURIComponent(code));
            const barcodePayload = await barcodeResponse.json();

            if (barcodeResponse.ok && barcodePayload.success) {
                product = barcodePayload.data;
            }
        }

        if (!product) {
            const response = await fetch('/api/products/search?q=' + encodeURIComponent(code));
            const payload = await response.json();

            if (response.ok && payload.success && payload.data.length) {
                product = payload.data[0];
                productSuggestions = payload.data;
                renderSuggestions(payload.data);
            }
        }

        if (!product) {
            content.hidden = true;
            message.innerHTML = '<section class="card"><p class="qa-help-text">Product not found. Try another product name or barcode.</p></section>';
            return;
        }

        setLookupProduct(product);
        message.innerHTML = '';
    } catch (e) {
        content.hidden = true;
        message.innerHTML = '<section class="card"><p class="qa-help-text">Product lookup failed.</p></section>';
    }
}

async function fetchProductSuggestions() {
    const query = document.getElementById('barcodeInput').value.trim();

    if (query.length < 2) {
        productSuggestions = [];
        renderSuggestions([]);
        return;
    }

    try {
        const response = await fetch('/api/products/search?q=' + encodeURIComponent(query));
        const payload = await response.json();

        if (!response.ok || !payload.success) {
            return;
        }

        productSuggestions = payload.data;
        renderSuggestions(payload.data);
    } catch (e) {
        productSuggestions = [];
        renderSuggestions([]);
    }
}

function renderSuggestions(products) {
    const suggestions = document.getElementById('productSuggestions');

    suggestions.innerHTML = products.map((product) => {
        const labelParts = [product.name];

        if (product.barcode) {
            labelParts.push(product.barcode);
        }

        return `<option value="${escapeHtml(product.name)}" label="${escapeHtml(labelParts.join(' - '))}"></option>`;
    }).join('');
}

function setLookupProduct(product) {
    const result = document.getElementById('barcodeResult');
    const content = document.getElementById('lookupContent');

    currentProduct = product;
    document.getElementById('barcodeInput').value = product.name;
    content.hidden = false;
    result.innerHTML = `
        <section class="card tomato-card">
            <div class="qa-card-header align-start">
                <div>
                    <h3>${escapeHtml(product.name)}</h3>
                    <p class="subtitle">Auto-filled from product lookup</p>
                </div>
                <span class="badge badge-light-blue">Matched</span>
            </div>
            <div class="info-grid">
                <div class="info-box"><label>CATEGORY</label><p>${escapeHtml(product.category)}</p></div>
                <div class="info-box"><label>UNIT</label><p>${escapeHtml(product.unit || '-')}</p></div>
                <div class="info-box"><label>SUPPLIER</label><p>${escapeHtml(product.preferred_supplier)}</p></div>
                <div class="info-box"><label>PACK SIZE</label><p>${escapeHtml(product.pack_size || 'Standard')}</p></div>
            </div>
            <div class="dashed-box">
                <p>Barcode: <strong>${escapeHtml(product.barcode || '-')}</strong></p>
            </div>
        </section>
    `;
    document.getElementById('quantityInput').value = '';
    document.getElementById('unitInput').value = product.unit || '';
    document.getElementById('notesInput').value = '';
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

document.getElementById('barcodeInput').addEventListener('input', function () {
    clearTimeout(lookupTimer);
    lookupTimer = setTimeout(fetchProductSuggestions, 250);
});

document.getElementById('barcodeInput').addEventListener('change', function () {
    const value = this.value.trim();
    const matchedProduct = productSuggestions.find((item) => item.name.toLowerCase() === value.toLowerCase());

    if (matchedProduct) {
        setLookupProduct(matchedProduct);
        document.getElementById('lookupMessage').innerHTML = '';
    }
});

document.getElementById('barcodeInput').addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        lookupProduct();
    }
});
</script>
@endpush
