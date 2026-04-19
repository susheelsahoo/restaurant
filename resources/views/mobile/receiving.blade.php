@extends('layout.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div class="icon-btn">←</div>
    <div class="topbar-title"><h3>Record Delivery</h3><span>PO-2026-0087</span></div>
    <div class="pill blue">Partial</div>
</div>

<div class="card">
    <div class="grid-2">
        <div class="field"><label>Delivery date</label><input class="input" value="2026-04-01 11:34"></div>
        <div class="field"><label>Note no.</label><input class="input" value="DN-7782"></div>
    </div>
</div>

<div class="card">
    <div class="section-head"><h4>Receive Items</h4><span>3 lines</span></div>
    <div class="list-row" style="display:block;">
        <h5>Tomato</h5><p>Ordered: 2.5 kg</p>
        <div class="line-receive">
            <div class="field"><label>Received</label><input class="input" value="2.5"></div>
            <div class="field"><label>Damaged</label><input class="input" value="0"></div>
            <div class="field"><label>Unit</label><input class="input" value="kg"></div>
        </div>
    </div>
    <div class="list-row" style="display:block;">
        <h5>Onion</h5><p>Ordered: 3 kg</p>
        <div class="line-receive">
            <div class="field"><label>Received</label><input class="input" value="2"></div>
            <div class="field"><label>Damaged</label><input class="input" value="0"></div>
            <div class="field"><label>Unit</label><input class="input" value="kg"></div>
        </div>
    </div>
    <div class="list-row" style="display:block; border-bottom:none;">
        <h5>Cucumber</h5><p>Ordered: 2 kg</p>
        <div class="line-receive">
            <div class="field"><label>Received</label><input class="input" value="2"></div>
            <div class="field"><label>Damaged</label><input class="input" value="0.5"></div>
            <div class="field"><label>Unit</label><input class="input" value="kg"></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="field"><label>Receiving notes</label><textarea class="input">Onions partially missing. Some cucumber damaged during transport.</textarea></div>
    <div class="grid-2">
        <div class="button soft">Save Partial</div>
        <div class="button primary">Complete Receipt</div>
    </div>
</div>
@endsection
