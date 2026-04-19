@extends('layouts.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div class="topbar-title"><h3>Purchasing</h3><span>Manager dashboard</span></div>
    <div class="profile">P</div>
</div>

<div class="stats-grid">
    <div class="stat card"><strong>7</strong><span>Approved requests</span></div>
    <div class="stat card"><strong>4</strong><span>Orders in progress</span></div>
    <div class="stat card"><strong>2</strong><span>Due today</span></div>
    <div class="stat card"><strong>1</strong><span>Delayed PO</span></div>
</div>

<div class="card">
    <div class="section-head"><h4>Approved Requests</h4><span>Create PO</span></div>
    <div class="po-row"><div><h5>REQ-2026-0215</h5><p>Kitchen · 3 items · Suggested supplier: FreshFarm</p></div><div class="pill blue">Ready</div></div>
    <div class="po-row"><div><h5>REQ-2026-0214</h5><p>Housekeeping · 6 items · Mixed suppliers</p></div><div class="pill orange">Priority</div></div>
</div>

<div class="card">
    <div class="section-head"><h4>Deliveries Today</h4><span>View all</span></div>
    <div class="po-row"><div><h5>PO-2026-0087</h5><p>FreshFarm · ETA 11:30 AM</p></div><div class="pill green">On time</div></div>
    <div class="po-row"><div><h5>PO-2026-0084</h5><p>CleanPro · ETA 16:00 PM</p></div><div class="pill gray">Scheduled</div></div>
</div>

<div class="card">
    <div class="section-head"><h4>Supplier Alerts</h4></div>
    <div class="note-box">FreshFarm changed delivery window for today. DairyPlus has not confirmed PO-2026-0089 yet.</div>
</div>
@endsection

@section('mobile-footer')
<div class="mobile-nav">
    <div class="active">Home</div><div>Queue</div><div>Orders</div><div>More</div>
</div>
@endsection
