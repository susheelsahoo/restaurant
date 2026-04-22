<nav class="bottom-nav">
    <a href="{{ url('/mobile/dashboard') }}" class="nav-item {{ request()->path() === 'mobile/dashboard' ? 'active' : '' }}">Home</a>
    <a href="{{ url('/mobile/request-detail') }}" class="nav-item {{ request()->path() === 'mobile/request-detail' ? 'active' : '' }}">Requests</a>
    <a href="{{ url('/mobile/templates') }}" class="nav-item {{ in_array(request()->path(), ['mobile/templates', 'mobile/quick-add'], true) ? 'active' : '' }}">Templates</a>
    <a href="{{ url('/mobile/purchasing') }}" class="nav-item {{ request()->path() === 'mobile/purchasing' ? 'active' : '' }}">Purchasing</a>
</nav>
