@php($purchaseRoles = app(\App\Services\PurchaseRoleService::class))

<nav class="bottom-nav">
    <a href="{{ url('/mobile/dashboard') }}" class="nav-item {{ request()->path() === 'mobile/dashboard' ? 'active' : '' }}">Home</a>
    <a href="{{ url('/mobile/request-detail') }}" class="nav-item {{ request()->is('mobile/request-detail*') || request()->is('mobile/quick-add') ? 'active' : '' }}">Requests</a>
    @if($purchaseRoles->canManagePurchaseOrders(auth()->user()))
        <a href="{{ url('/mobile/purchasing') }}" class="nav-item {{ request()->path() === 'mobile/purchasing' ? 'active' : '' }}">Purchasing</a>
    @endif
</nav>
