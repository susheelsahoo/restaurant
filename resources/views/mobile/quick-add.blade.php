@extends('layout.mobile')

@push('head')
<script>
async function lookupBarcode() {
    const code = document.getElementById('barcodeInput').value.trim();
    const result = document.getElementById('barcodeResult');
    if (!code) return;
    result.innerHTML = '<div class="card info-box">Looking up scanned product…</div>';

    try {
        const response = await fetch('/api/products/barcode/' + encodeURIComponent(code));
        const payload = await response.json();

        if (!response.ok || !payload.success) {
            result.innerHTML = '<div class="card warning-box">Barcode not found. Use search, favorites, or templates.</div>';
            return;
        }

        const product = payload.data;
        result.innerHTML = `
            <div class="card product-card">
                <div class="product-top">
                    <div>
                        <h4>${product.name}</h4>
                        <p>Auto-filled from barcode lookup</p>
                    </div>
                    <div class="pill blue">Scanned</div>
                </div>
                <div class="meta">
                    <div class="meta-box"><small>Category</small><strong>${product.category}</strong></div>
                    <div class="meta-box"><small>Unit</small><strong>${product.unit}</strong></div>
                    <div class="meta-box"><small>Supplier</small><strong>${product.preferred_supplier}</strong></div>
                    <div class="meta-box"><small>Pack size</small><strong>${product.pack_size || 'Standard'}</strong></div>
                </div>
                <div class="note-box">Scanned code: <strong>${product.barcode}</strong></div>
            </div>
        `;
        document.getElementById('quantityInput').value = '';
        document.getElementById('unitInput').value = product.unit;
        document.getElementById('notesInput').value = '';
    } catch (e) {
        result.innerHTML = '<div class="card error-box">Barcode lookup failed.</div>';
    }
}
</script>
@endpush

@section('mobile-content')
<div class="topbar mobile-top">
    <div class="icon-btn" onclick="window.history.back()">←</div>
    <div class="topbar-title"><h3>New Kitchen Request</h3><span>Quick Add workflow</span></div>
    <div class="icon-btn">⋯</div>
</div>

<div class="card">
    <div class="grid-2">
        <div class="field"><label>Needed by</label><input class="input" value="Today, 18:00"></div>
        <div class="field"><label>Priority</label><select class="select"><option>Normal</option><option>Urgent</option><option>Low</option></select></div>
    </div>
</div>

<div class="actions">
    <div class="action primary">Search Product</div>
    <div class="action" onclick="lookupBarcode()">Scan</div>
    <div class="action">Templates</div>
</div>

<div class="card">
    <div class="field">
        <label>Barcode input demo</label>
        <div class="barcode-row">
            <input class="input" id="barcodeInput" value="1234567890123">
            <button class="btn primary" type="button" onclick="lookupBarcode()">Lookup</button>
        </div>
        <small class="helper">Demo barcodes: 1234567890123, 1234567890456, 9988123412000, 7722009911001</small>
    </div>
</div>

<div class="card">
    <div class="section-head"><h4>Favorites</h4><span>Manage</span></div>
    <div class="chips">@foreach($favoriteItems as $item)<div class="chip">{{ $item }}</div>@endforeach</div>
</div>

<div class="card">
    <div class="section-head"><h4>Recent Items</h4><span>See all</span></div>
    <div class="chips">@foreach($recentItems as $item)<div class="chip">{{ $item }}</div>@endforeach</div>
</div>

<div id="barcodeResult">
    <div class="card product-card">
        <div class="product-top">
            <div><h4>{{ $scannedProduct['name'] }}</h4><p>Auto-filled from barcode lookup</p></div>
            <div class="pill blue">Scanned</div>
        </div>
        <div class="meta">
            <div class="meta-box"><small>Category</small><strong>{{ $scannedProduct['category'] }}</strong></div>
            <div class="meta-box"><small>Unit</small><strong>{{ $scannedProduct['unit'] }}</strong></div>
            <div class="meta-box"><small>Supplier</small><strong>{{ $scannedProduct['preferred_supplier'] }}</strong></div>
            <div class="meta-box"><small>Pack size</small><strong>{{ $scannedProduct['pack_size'] }}</strong></div>
        </div>
        <div class="note-box">Scanned code: <strong>{{ $scannedProduct['barcode'] }}</strong></div>
    </div>
</div>

<div class="card">
    <div class="qty-row">
        <div class="field"><label>Quantity</label><input class="input" id="quantityInput" value="2.5"></div>
        <div class="field"><label>Unit</label><input class="input" id="unitInput" value="kg"></div>
    </div>
    <div class="field"><label>Notes</label><textarea class="input" id="notesInput">Ripe if possible</textarea></div>
    <button class="button primary full">Add Item</button>
</div>

<div class="card">
    <div class="section-head"><h4>Request Basket</h4><span>{{ count($basketItems) }} items</span></div>
    @foreach($basketItems as $item)
        <div class="request-item">
            <div><h5>{{ $item['name'] }}</h5><p>{{ $item['supplier'] }} · {{ $item['category'] }}</p></div>
            <div class="text-right"><div class="pill gray">{{ $item['quantity'] }}</div><p class="link-note">Edit · Remove</p></div>
        </div>
    @endforeach
</div>
@endsection

@section('mobile-footer')
<div class="sticky-submit">
    <div class="submit-meta"><span>Total items: <strong>{{ count($basketItems) }}</strong></span><span>Status: Draft</span></div>
    <button class="button primary full">Submit Request</button>
</div>
@endsection
