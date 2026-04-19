@extends('layout.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div>
        <h3>Good morning, Nino</h3>
        <span>Kitchen team · Tuesday overview</span>
    </div>
    <div class="profile">N</div>
</div>

<div class="card hero">
    <h4>Create requests in seconds</h4>
    <p>Use templates, favorites, recent items, or scan products to submit daily ingredient orders quickly.</p>
    <div class="cta-row">
        <div class="mini-card"><strong>5</strong><span>Open requests</span></div>
        <div class="mini-card"><strong>2</strong><span>Awaiting approval</span></div>
    </div>
</div>

<a class="button primary full" href="/mobile/quick-add">+ New Request</a>

<div class="stats-grid">
    @foreach($stats as $stat)
        <div class="stat card">
            <strong>{{ $stat['value'] }}</strong>
            <span>{{ $stat['label'] }}</span>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="section-head"><h4>Favorite Templates</h4><span>See all</span></div>
    <div class="list-row">
        <div><h5>Daily Kitchen Essentials</h5><p>Vegetables, dairy, herbs, bread basics</p></div>
        <div class="pill blue">12 items</div>
    </div>
    <div class="list-row">
        <div><h5>Weekend Prep Order</h5><p>Higher quantities for Friday–Sunday service</p></div>
        <div class="pill orange">Urgent</div>
    </div>
</div>
@endsection

@section('mobile-footer')
<div class="mobile-nav">
    <div class="active">Home</div>
    <div>Requests</div>
    <div>Templates</div>
    <div>Profile</div>
</div>
@endsection
