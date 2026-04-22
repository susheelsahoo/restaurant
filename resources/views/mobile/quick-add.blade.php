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
                <input type="text" id="needed_by" class="qa-input" value="Today, 18:00">
            </div>
            <div class="input-col">
                <label class="qa-label" for="priority">PRIORITY</label>
                <div class="select-wrapper">
                    <select id="priority" class="qa-select">
                        <option>Normal</option>
                        <option>High</option>
                        <option>Urgent</option>
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

        <section class="card">
            <div class="qa-card-header">
                <h3>Favorites</h3>
                <a href="#" class="qa-link">Manage</a>
            </div>
            <div class="pills-container">
                @foreach($favoriteItems as $item)
                    <span class="pill">{{ $item }}</span>
                @endforeach
            </div>
        </section>

        <section class="card">
            <div class="qa-card-header">
                <h3>Recent Items</h3>
                <a href="#" class="qa-link">See all</a>
            </div>
            <div class="pills-container">
                @foreach($recentItems as $item)
                    <span class="pill">{{ $item }}</span>
                @endforeach
            </div>
        </section>

        <div id="barcodeResult">
            <section class="card tomato-card">
                <div class="qa-card-header align-start">
                    <div>
                        <h3>{{ $scannedProduct['name'] }}</h3>
                        <p class="subtitle">Auto-filled from barcode lookup</p>
                    </div>
                    <span class="badge badge-light-blue">Scanned</span>
                </div>
                <div class="info-grid">
                    <div class="info-box">
                        <label>CATEGORY</label>
                        <p>{{ $scannedProduct['category'] }}</p>
                    </div>
                    <div class="info-box">
                        <label>UNIT</label>
                        <p>{{ $scannedProduct['unit'] }}</p>
                    </div>
                    <div class="info-box">
                        <label>SUPPLIER</label>
                        <p>{{ $scannedProduct['preferred_supplier'] }}</p>
                    </div>
                    <div class="info-box">
                        <label>PACK SIZE</label>
                        <p>{{ $scannedProduct['pack_size'] }}</p>
                    </div>
                </div>
                <div class="dashed-box">
                    <p>Scanned code: <strong>{{ $scannedProduct['barcode'] }}</strong></p>
                </div>
            </section>
        </div>

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
            <button class="qa-primary-btn" type="button">Add Item</button>
        </section>

        <section class="card request-basket">
            <div class="qa-card-header">
                <h3>Request Basket</h3>
                <span class="text-blue-bold">{{ count($basketItems) }} items</span>
            </div>

            @foreach($basketItems as $item)
                <div class="basket-item">
                    <div class="basket-item-info">
                        <h4>{{ $item['name'] }}</h4>
                        <p>{{ $item['supplier'] }} &middot; {{ $item['category'] }}</p>
                    </div>
                    <div class="basket-item-actions">
                        <span class="qty-badge">{{ $item['quantity'] }}</span>
                        <div class="action-links">
                            <a href="#">Edit</a> &middot; <a href="#">Remove</a>
                        </div>
                    </div>
                </div>
                @if (!$loop->last)
                    <hr class="qa-divider">
                @endif
            @endforeach
        </section>
    </main>

    <nav class="bottom-nav">
        <a href="{{ url('/mobile/dashboard') }}" class="nav-item">Home</a>
        <a href="{{ url('/mobile/request-detail') }}" class="nav-item">Requests</a>
        <a href="{{ url('/mobile/quick-add') }}" class="nav-item active">Templates</a>
        <a href="{{ url('/mobile/purchasing') }}" class="nav-item">Purchasing</a>
    </nav>
</div>
@endsection

@push('scripts')
<script>
async function lookupBarcode() {
    const code = document.getElementById('barcodeInput').value.trim();
    const result = document.getElementById('barcodeResult');
    if (!code) return;

    try {
        const response = await fetch('/api/products/barcode/' + encodeURIComponent(code));
        const payload = await response.json();

        if (!response.ok || !payload.success) {
            result.innerHTML = '<section class="card"><p class="qa-help-text">Barcode not found. Use search, favorites, or templates.</p></section>';
            return;
        }

        const product = payload.data;
        result.innerHTML = `
            <section class="card tomato-card">
                <div class="qa-card-header align-start">
                    <div>
                        <h3>${product.name}</h3>
                        <p class="subtitle">Auto-filled from barcode lookup</p>
                    </div>
                    <span class="badge badge-light-blue">Scanned</span>
                </div>
                <div class="info-grid">
                    <div class="info-box"><label>CATEGORY</label><p>${product.category}</p></div>
                    <div class="info-box"><label>UNIT</label><p>${product.unit}</p></div>
                    <div class="info-box"><label>SUPPLIER</label><p>${product.preferred_supplier}</p></div>
                    <div class="info-box"><label>PACK SIZE</label><p>${product.pack_size || 'Standard'}</p></div>
                </div>
                <div class="dashed-box">
                    <p>Scanned code: <strong>${product.barcode}</strong></p>
                </div>
            </section>
        `;
        document.getElementById('quantityInput').value = '';
        document.getElementById('unitInput').value = product.unit;
        document.getElementById('notesInput').value = '';
    } catch (e) {
        result.innerHTML = '<section class="card"><p class="qa-help-text">Barcode lookup failed.</p></section>';
    }
}
</script>
@endpush
