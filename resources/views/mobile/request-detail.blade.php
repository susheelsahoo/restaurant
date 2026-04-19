@extends('layout.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div class="icon-btn">←</div>
    <div class="topbar-title"><h3>REQ-2026-0215</h3><span>Kitchen request details</span></div>
    <div class="pill blue">Pending</div>
</div>

<div class="card">
    <div class="section-head"><h4>Request Summary</h4><span>Today</span></div>
    <div class="grid-2">
        <div class="meta-box"><small>Requester</small><strong>Nino G.</strong></div>
        <div class="meta-box"><small>Needed by</small><strong>18:00</strong></div>
        <div class="meta-box"><small>Priority</small><strong>Normal</strong></div>
        <div class="meta-box"><small>Department</small><strong>Kitchen</strong></div>
    </div>
</div>

<div class="card">
    <div class="section-head"><h4>Items</h4><span>{{ count($basketItems) }} total</span></div>
    @foreach($basketItems as $item)
        <div class="request-item">
            <div><h5>{{ $item['name'] }}</h5><p>{{ $item['supplier'] }} · {{ $item['category'] }}</p></div>
            <div class="pill gray">{{ $item['quantity'] }}</div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="section-head"><h4>Approval Timeline</h4><span>Live</span></div>
    <div class="timeline">
        <div class="timeline-item"><h5>Submitted by Nino</h5><p>Today · 10:14 AM</p></div>
        <div class="timeline-item"><h5>Awaiting kitchen manager approval</h5><p>Notification sent to approver</p></div>
        <div class="timeline-item"><h5>Purchasing queue</h5><p>Will appear here after approval</p></div>
    </div>
</div>

<div class="card">
    <div class="section-head"><h4>Notes</h4></div>
    <div class="note-box">Tomatoes should be ripe if possible. Ingredients needed for evening prep and lunch service.</div>
</div>
@endsection
