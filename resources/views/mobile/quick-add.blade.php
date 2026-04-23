@extends('layout.mobile')

@section('title', 'Quick Add')
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@section('mobile-content')
<div class="quick-add-page">
    <header class="qa-topbar">
        <button class="qa-icon-btn" type="button" aria-label="Go back" onclick="window.location.href='{{ url('/mobile/dashboard') }}'">
            <span aria-hidden="true">←</span>
        </button>
        <div class="qa-title-block">
            <h1>Choose Products</h1>
            <p>Select items, adjust quantity, and submit one request.</p>
        </div>
        <div class="qa-avatar">{{ strtoupper(substr((string) auth()->user()?->name, 0, 1)) ?: 'U' }}</div>
    </header>

    <main class="qa-main">
        <section class="qa-card">
            <div class="qa-action-row">
                <button id="scanBtn" class="qa-action-btn qa-action-primary" type="button">Scan Product</button>
                <button id="templateBtn" class="qa-action-btn qa-action-secondary" type="button">Templates</button>
            </div>

            <div id="scanInfo" class="qa-helper qa-helper-scan" hidden>
                Scan mode uses barcode lookup. Enter a barcode or product name, then we will auto-select the matched item.
            </div>

            <div id="templatePanel" class="qa-helper qa-helper-template" hidden>
                <div class="qa-helper-title">Quick templates</div>
                <div class="qa-template-list">
                    @forelse ($quickAddTemplates as $template)
                        <button class="qa-template-chip" type="button" data-template-id="{{ $template['id'] }}">
                            {{ $template['name'] }}
                        </button>
                    @empty
                        <div class="qa-template-empty">No active templates available.</div>
                    @endforelse
                </div>
            </div>

            <div class="qa-search-box">
                <span class="qa-search-icon" aria-hidden="true">⌕</span>
                <input id="searchInput" type="text" placeholder="Search product name, SKU, barcode, category...">
            </div>

            <div class="qa-chip-row">
                <button class="qa-chip active" type="button" data-category="all">All</button>
                @foreach ($quickAddCategories as $category)
                    <button class="qa-chip" type="button" data-category="{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</button>
                @endforeach
            </div>
        </section>

        <section class="qa-card">
            <div class="qa-section-head">
                <h2>Selection Summary</h2>
                <span>Live</span>
            </div>
            <div class="qa-summary-grid">
                <div class="qa-summary-box">
                    <strong id="selectedCount">0</strong>
                    <span>Selected products</span>
                </div>
                <div class="qa-summary-box">
                    <strong id="totalQuantity">0</strong>
                    <span>Total quantity</span>
                </div>
            </div>
        </section>

        <div class="qa-section-head qa-list-head">
            <h2>Product List</h2>
            <span id="visibleCount">0 products</span>
        </div>

        <div id="lookupMessage"></div>

        <section class="qa-product-list" id="productList">
            @foreach ($quickAddProducts as $product)
                @php
                    $categorySlug = \Illuminate\Support\Str::slug($product['category']);
                    $defaultQty = $product['unit'] === 'kg' ? '0.5' : '1';
                    $units = $product['unit'] === 'kg' ? 'kg,pcs' : $product['unit'];
                @endphp
                <article
                    class="qa-product-item"
                    data-id="{{ $product['id'] }}"
                    data-name="{{ $product['name'] }}"
                    data-category="{{ strtolower($product['category']) }}"
                    data-category-slug="{{ $categorySlug }}"
                    data-sku="{{ $product['sku'] }}"
                    data-barcode="{{ $product['barcode'] }}"
                    data-supplier="{{ $product['preferred_supplier'] }}"
                    data-supplier-id="{{ $product['supplier_id'] }}"
                    data-unit="{{ $product['unit'] }}"
                    data-default-unit="{{ $product['unit'] }}"
                    data-units="{{ $units }}"
                    data-qty="{{ $defaultQty }}"
                    data-default-qty="{{ $defaultQty }}"
                    data-step-kg="0.5"
                    data-step-pcs="1"
                >
                    <div class="qa-product-row">
                        <div class="qa-product-left" role="button" tabindex="0">
                            <div class="qa-mark"></div>
                            <div class="qa-product-info">
                                <h3>{{ $product['name'] }}</h3>
                                <p>{{ $product['category'] }} · {{ $product['preferred_supplier'] }} @if($product['sku']) · SKU: {{ $product['sku'] }} @endif</p>
                            </div>
                        </div>
                        <div class="qa-qty-control">
                            <button class="qa-qty-btn qa-minus-btn" type="button">-</button>
                            <input class="qa-qty-value" value="{{ $defaultQty }}" inputmode="{{ $product['unit'] === 'kg' ? 'decimal' : 'numeric' }}">
                            <div class="qa-unit-value">{{ $product['unit'] }}</div>
                            <button class="qa-qty-btn qa-plus-btn" type="button">+</button>
                        </div>
                    </div>
                    <div class="qa-note-row">
                        <input class="qa-note-input" placeholder="Add note for this item">
                    </div>
                </article>
            @endforeach
        </section>
    </main>

    <div class="qa-sticky-bar">
        <div class="qa-sticky-meta">
            <span>Marked items: <strong id="bottomSelectedCount">0</strong></span>
            <span>Draft selection</span>
        </div>
        <button class="qa-submit-btn" id="openOrderModalBtn" type="button">Add Selected Products</button>
    </div>

    <div class="qa-modal-overlay" id="orderModal">
        <div class="qa-modal">
            <div class="qa-modal-head">
                <div>
                    <h3>Submit Request</h3>
                    <p>Review selected items and send the purchase request.</p>
                </div>
                <button class="qa-modal-close" id="closeOrderModalBtn" type="button">×</button>
            </div>

            <div class="qa-form-grid">
                <div class="qa-field">
                    <label for="needed_by">Needed By</label>
                    <input type="datetime-local" id="needed_by" value="{{ now()->addDay()->format('Y-m-d\\TH:i') }}">
                </div>
                <div class="qa-field">
                    <label for="priority">Priority</label>
                    <select id="priority">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="qa-section-head">
                <h2>Selected Items by Category</h2>
                <span id="modalItemCount">0 items</span>
            </div>

            <div id="orderCategoryList"></div>

            <div class="qa-modal-actions">
                <button class="qa-cancel-btn" id="cancelOrderBtn" type="button">Cancel</button>
                <button class="qa-submit-btn" id="submitOrderBtn" type="button">Submit Request</button>
            </div>

            <div class="qa-submit-state" id="submitState" hidden></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const initialProducts = @json($quickAddProducts);
const quickAddTemplates = @json($quickAddTemplates);

const searchInput = document.getElementById('searchInput');
const selectedCountEl = document.getElementById('selectedCount');
const totalQuantityEl = document.getElementById('totalQuantity');
const bottomSelectedCountEl = document.getElementById('bottomSelectedCount');
const visibleCountEl = document.getElementById('visibleCount');
const lookupMessage = document.getElementById('lookupMessage');
const scanBtn = document.getElementById('scanBtn');
const templateBtn = document.getElementById('templateBtn');
const scanInfo = document.getElementById('scanInfo');
const templatePanel = document.getElementById('templatePanel');
const chips = Array.from(document.querySelectorAll('.qa-chip'));
const openOrderModalBtn = document.getElementById('openOrderModalBtn');
const orderModal = document.getElementById('orderModal');
const closeOrderModalBtn = document.getElementById('closeOrderModalBtn');
const cancelOrderBtn = document.getElementById('cancelOrderBtn');
const submitOrderBtn = document.getElementById('submitOrderBtn');
const orderCategoryList = document.getElementById('orderCategoryList');
const modalItemCount = document.getElementById('modalItemCount');
const submitState = document.getElementById('submitState');
const neededByInput = document.getElementById('needed_by');
const priorityInput = document.getElementById('priority');

let productItems = [];
let productSearchCache = initialProducts.slice();
let activeCategory = 'all';
let lookupTimer = null;

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));
}

function showMessage(text, type = 'error') {
    lookupMessage.innerHTML = `<div class="qa-message-card ${type}">${escapeHtml(text)}</div>`;
}

function clearMessage() {
    lookupMessage.innerHTML = '';
}

function formatQty(value) {
    const numeric = Number(value || 0);

    if (Number.isInteger(numeric)) {
        return String(numeric);
    }

    return numeric.toFixed(2).replace(/\.?0+$/, '');
}

function getSelectedItems() {
    return productItems.filter((item) => item.classList.contains('selected'));
}

function getStep(item) {
    const unit = item.dataset.unit || 'pcs';
    const key = `step${unit.charAt(0).toUpperCase()}${unit.slice(1)}`;

    return Number(item.dataset[key]) || 1;
}

function setItemQuantity(item, nextQuantity) {
    const qtyInput = item.querySelector('.qa-qty-value');
    const unit = item.dataset.unit || 'pcs';
    let quantity = Number(nextQuantity);

    if (Number.isNaN(quantity) || quantity <= 0) {
        quantity = getStep(item);
    }

    if (unit === 'pcs') {
        quantity = Math.max(1, Math.round(quantity));
    } else {
        const step = getStep(item);
        quantity = Math.max(step, Math.round(quantity / step) * step);
    }

    item.dataset.qty = String(quantity);
    qtyInput.value = formatQty(quantity);
}

function setItemSelected(item, selected) {
    item.classList.toggle('selected', selected);
}

function resetItemState(item) {
    const defaultUnit = item.dataset.defaultUnit || 'pcs';
    const defaultQty = item.dataset.defaultQty || (defaultUnit === 'kg' ? '0.5' : '1');
    const unitValue = item.querySelector('.qa-unit-value');

    item.dataset.unit = defaultUnit;

    if (unitValue) {
        unitValue.textContent = defaultUnit;
    }

    item.querySelector('.qa-note-input').value = '';
    setItemQuantity(item, defaultQty);
    setItemSelected(item, false);
}

function updateSummary() {
    const selectedItems = getSelectedItems();
    const totalQty = selectedItems.reduce((sum, item) => sum + Number(item.dataset.qty || 0), 0);

    selectedCountEl.textContent = selectedItems.length;
    bottomSelectedCountEl.textContent = selectedItems.length;
    totalQuantityEl.textContent = formatQty(totalQty);
    openOrderModalBtn.disabled = selectedItems.length === 0;
}

function updateVisibleCount() {
    const visibleItems = productItems.filter((item) => !item.classList.contains('hidden'));
    visibleCountEl.textContent = `${visibleItems.length} products`;
}

function applyFilters() {
    const query = searchInput.value.trim().toLowerCase();

    productItems.forEach((item) => {
        const haystack = [
            item.dataset.name,
            item.dataset.category,
            item.dataset.sku,
            item.dataset.barcode,
            item.dataset.supplier,
        ].join(' ').toLowerCase();

        const matchesSearch = query === '' || haystack.includes(query);
        const matchesCategory = activeCategory === 'all' || item.dataset.categorySlug === activeCategory;

        item.classList.toggle('hidden', !(matchesSearch && matchesCategory));
    });

    updateVisibleCount();
}

function attachProductItemEvents(item) {
    const left = item.querySelector('.qa-product-left');
    const minusBtn = item.querySelector('.qa-minus-btn');
    const plusBtn = item.querySelector('.qa-plus-btn');
    const qtyInput = item.querySelector('.qa-qty-value');
    const unitValue = item.querySelector('.qa-unit-value');
    const allowedUnits = (item.dataset.units || item.dataset.unit || 'pcs').split(',');

    left.addEventListener('click', () => {
        setItemSelected(item, !item.classList.contains('selected'));
        updateSummary();
    });

    left.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            setItemSelected(item, !item.classList.contains('selected'));
            updateSummary();
        }
    });

    plusBtn.addEventListener('click', () => {
        setItemSelected(item, true);
        setItemQuantity(item, Number(item.dataset.qty || 0) + getStep(item));
        updateSummary();
    });

    minusBtn.addEventListener('click', () => {
        setItemSelected(item, true);
        setItemQuantity(item, Number(item.dataset.qty || 0) - getStep(item));
        updateSummary();
    });

    qtyInput.addEventListener('click', (event) => event.stopPropagation());
    qtyInput.addEventListener('input', (event) => event.stopPropagation());
    qtyInput.addEventListener('focus', () => setItemSelected(item, true));
    qtyInput.addEventListener('blur', () => {
        setItemQuantity(item, qtyInput.value);
        updateSummary();
    });

    unitValue.addEventListener('click', (event) => {
        event.stopPropagation();

        if (allowedUnits.length < 2) {
            return;
        }

        const currentIndex = allowedUnits.indexOf(item.dataset.unit);
        const nextUnit = allowedUnits[(currentIndex + 1) % allowedUnits.length];
        item.dataset.unit = nextUnit;
        unitValue.textContent = nextUnit;
        setItemSelected(item, true);
        setItemQuantity(item, item.dataset.qty);
        updateSummary();
    });
}

function initializeProductItems() {
    productItems = Array.from(document.querySelectorAll('.qa-product-item'));
    productItems.forEach(attachProductItemEvents);
    applyFilters();
    updateSummary();
}

function buildModalPreview() {
    const selectedItems = getSelectedItems();
    const grouped = {};

    selectedItems.forEach((item) => {
        const category = item.dataset.category || 'uncategorized';

        if (!grouped[category]) {
            grouped[category] = [];
        }

        grouped[category].push({
            name: item.dataset.name,
            qty: formatQty(item.dataset.qty),
            unit: item.dataset.unit,
            note: item.querySelector('.qa-note-input').value.trim(),
        });
    });

    modalItemCount.textContent = `${selectedItems.length} items`;

    if (!selectedItems.length) {
        orderCategoryList.innerHTML = '<div class="qa-category-block">No selected items yet.</div>';
        return;
    }

    orderCategoryList.innerHTML = Object.keys(grouped).map((category) => `
        <div class="qa-category-block">
            <div class="qa-category-title">${escapeHtml(category)}</div>
            ${grouped[category].map((item) => `
                <div class="qa-order-item">
                    <div>
                        <div class="qa-order-item-name">${escapeHtml(item.name)}</div>
                        <div class="qa-order-item-meta">${escapeHtml(item.note || 'No note added')}</div>
                    </div>
                    <div>${escapeHtml(item.qty)} ${escapeHtml(item.unit)}</div>
                </div>
            `).join('')}
        </div>
    `).join('');
}

function openOrderModal() {
    if (!getSelectedItems().length) {
        showMessage('Select at least one product before continuing.');
        return;
    }

    clearMessage();
    submitState.hidden = true;
    buildModalPreview();
    orderModal.classList.add('open');
}

function closeOrderModal() {
    orderModal.classList.remove('open');
}

function applyTemplate(templateId) {
    const template = quickAddTemplates.find((item) => Number(item.id) === Number(templateId));

    if (!template || !Array.isArray(template.items) || !template.items.length) {
        return;
    }

    let matched = 0;

    template.items.forEach((templateItem) => {
        const matchedItem = productItems.find((item) => Number(item.dataset.id) === Number(templateItem.product_id));

        if (!matchedItem) {
            return;
        }

        matched += 1;
        setItemSelected(matchedItem, true);
        setItemQuantity(matchedItem, templateItem.quantity);

        if (templateItem.unit) {
            matchedItem.dataset.unit = templateItem.unit;
            const unitValue = matchedItem.querySelector('.qa-unit-value');
            if (unitValue) {
                unitValue.textContent = templateItem.unit;
            }
        }

        if (templateItem.note) {
            matchedItem.querySelector('.qa-note-input').value = templateItem.note;
        }
    });

    updateSummary();

    if (!matched) {
        showMessage('Template products are not available in the current product list.');
        return;
    }

    clearMessage();
}

async function lookupProduct() {
    const query = searchInput.value.trim();

    if (!query) {
        showMessage('Enter a product name or barcode to scan.');
        return;
    }

    try {
        let product = productSearchCache.find((item) => {
            return item.name.toLowerCase() === query.toLowerCase()
                || String(item.barcode || '') === query
                || String(item.sku || '').toLowerCase() === query.toLowerCase();
        }) || null;

        if (!product) {
            const barcodeResponse = await fetch('/api/products/barcode/' + encodeURIComponent(query));

            if (barcodeResponse.ok) {
                const barcodePayload = await barcodeResponse.json();
                if (barcodePayload.success) {
                    product = barcodePayload.data;
                }
            }
        }

        if (!product) {
            const response = await fetch('/api/products/search?q=' + encodeURIComponent(query));
            const payload = await response.json();

            if (response.ok && payload.success && payload.data.length) {
                product = payload.data[0];
                productSearchCache = payload.data;
            }
        }

        if (!product) {
            showMessage('Product not found. Try another product name or barcode.');
            return;
        }

        const matchedItem = productItems.find((item) => Number(item.dataset.id) === Number(product.id));

        if (matchedItem) {
            setItemSelected(matchedItem, true);
            matchedItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            clearMessage();
            updateSummary();
            return;
        }

        showMessage('Product was found but is not in the quick list yet.');
    } catch (error) {
        showMessage('Product lookup failed. Please try again.');
    }
}

async function fetchSuggestions() {
    const query = searchInput.value.trim();

    if (query.length < 2) {
        productSearchCache = initialProducts.slice();
        applyFilters();
        return;
    }

    try {
        const response = await fetch('/api/products/search?q=' + encodeURIComponent(query));
        const payload = await response.json();

        if (response.ok && payload.success) {
            productSearchCache = payload.data;
        }
    } catch (error) {
        productSearchCache = initialProducts.slice();
    }

    applyFilters();
}

async function submitRequest() {
    const selectedItems = getSelectedItems();

    if (!selectedItems.length) {
        showMessage('Select at least one product before submitting.');
        return;
    }

    submitOrderBtn.disabled = true;
    submitOrderBtn.textContent = 'Submitting...';

    const payload = {
        needed_by: neededByInput.value,
        priority: priorityInput.value,
        items: selectedItems.map((item) => ({
            product_id: Number(item.dataset.id),
            quantity: Number(item.dataset.qty),
            supplier_id: item.dataset.supplierId ? Number(item.dataset.supplierId) : null,
            notes: item.querySelector('.qa-note-input').value.trim() || null,
        })),
    };

    try {
        const response = await fetch('{{ url('/mobile/quick-add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            const errors = result.errors ? Object.values(result.errors).flat().join(' ') : 'Request submit failed.';
            submitState.hidden = false;
            submitState.textContent = errors;
            submitState.className = 'qa-submit-state';
            submitOrderBtn.disabled = false;
            submitOrderBtn.textContent = 'Submit Request';
            return;
        }

        submitState.hidden = false;
        submitState.textContent = `Created ${result.request.request_no} successfully.`;
        submitState.className = 'qa-submit-state';
        clearMessage();

        productItems.forEach((item) => {
            resetItemState(item);
        });

        updateSummary();
        searchInput.value = '';
        applyFilters();
        setTimeout(closeOrderModal, 1000);
    } catch (error) {
        submitState.hidden = false;
        submitState.textContent = 'Request submit failed. Please try again.';
        submitState.className = 'qa-submit-state';
    } finally {
        submitOrderBtn.disabled = false;
        submitOrderBtn.textContent = 'Submit Request';
    }
}

chips.forEach((chip) => {
    chip.addEventListener('click', () => {
        chips.forEach((item) => item.classList.remove('active'));
        chip.classList.add('active');
        activeCategory = chip.dataset.category;
        applyFilters();
    });
});

searchInput.addEventListener('input', () => {
    clearTimeout(lookupTimer);
    lookupTimer = setTimeout(fetchSuggestions, 250);
    applyFilters();
});

searchInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();
        lookupProduct();
    }
});

scanBtn.addEventListener('click', () => {
    scanInfo.hidden = !scanInfo.hidden;

    if (searchInput.value.trim() !== '') {
        lookupProduct();
    }
});

templateBtn.addEventListener('click', () => {
    templatePanel.hidden = !templatePanel.hidden;
});

document.querySelectorAll('.qa-template-chip').forEach((button) => {
    button.addEventListener('click', () => applyTemplate(button.dataset.templateId));
});

openOrderModalBtn.addEventListener('click', openOrderModal);
closeOrderModalBtn.addEventListener('click', closeOrderModal);
cancelOrderBtn.addEventListener('click', closeOrderModal);
submitOrderBtn.addEventListener('click', submitRequest);

orderModal.addEventListener('click', (event) => {
    if (event.target === orderModal) {
        closeOrderModal();
    }
});

initializeProductItems();
</script>
@endpush
