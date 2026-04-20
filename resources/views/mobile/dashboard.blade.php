@extends('layout.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div>
        <h3>{{ $greeting }}, {{ auth()->user()->name }}</h3>
        <span>Kitchen team · Tuesday overview</span>
    </div>
    <div class="profile" onclick="toggleProfileMenu()">{{ substr(auth()->user()->name, 0, 1) }}</div>
</div>

<div class="profile-menu" id="profileMenu" style="display:none;">
    <div class="menu-item">Profile</div>
    <form method="POST" action="/logout" style="display:inline;">@csrf<button class="menu-item" type="submit">Logout</button></form>
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

<script>
function toggleProfileMenu() {
    var menu = document.getElementById('profileMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
</script>
