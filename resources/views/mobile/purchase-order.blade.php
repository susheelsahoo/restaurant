@extends('layouts.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div class="icon-btn">←</div>
    <div class="topbar-title"><h3>PO-2026-0087</h3><span>FreshFarm</span></div>
    <div class="pill green">Confirmed</div>
</div>

<div class="card">
    <div class="grid-2">
        <div class="meta-box"><small>Order date</small><strong>Mar 31</strong></div>
        <div class="meta-box"><small>Expected</small><strong>Apr 1</strong></div>
        <div class="meta-box"><small>Buyer</small><strong>Mariam</strong></div>
        <div class="meta-box"><small>Linked req</small><strong>REQ-0215</strong></div>
    </div>
    <div class="progress-row">
        <div class="pill blue">Sent</div>
        <div class="pill green">Confirmed</div>
        <div class="pill gray">In delivery</div>
        <div class="pill gray">Received</div>
    </div>
</div>

<div class="card">
    <div class="section-head"><h4>Supplier</h4><span>Call</span></div>
    <div class="note-box">FreshFarm LLC<br>Contact: Giorgi · +36 20 555 1111<br>Email: orders@freshfarm.hu</div>
</div>

<div class="card">
    <div class="section-head"><h4>Line Items</h4><span>3 items</span></div>
    <div class="po-row"><div><h5>Tomato</h5><p>Ordered 2.5 kg</p></div><div class="pill gray">€ 10.6</div></div>
    <div class="po-row"><div><h5>Onion</h5><p>Ordered 3 kg</p></div><div class="pill gray">€ 7.2</div></div>
    <div class="po-row"><div><h5>Cucumber</h5><p>Ordered 2 kg</p></div><div class="pill gray">€ 5.0</div></div>
</div>

<div class="card">
    <div class="grid-2">
        <div class="button">Edit PO</div>
        <div class="button primary">Record Delivery</div>
    </div>
</div>
@endsection
