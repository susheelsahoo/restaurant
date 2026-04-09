<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
	<div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
		<div class="menu-item me-lg-1">
			<a class="menu-link py-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
				<span class="menu-title">Dashboard</span>
			</a>
		</div>
		<div class="menu-item me-lg-1">
			<a class="menu-link py-3 {{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.index') }}">
				<span class="menu-title">Manage Purchase Order</span>
			</a>
		</div>
		<div class="menu-item me-lg-1">
			<a class="menu-link py-3 {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
				<span class="menu-title">Bookings</span>
			</a>
		</div>
		<div class="menu-item me-lg-1">
			<a class="menu-link py-3 {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
				<span class="menu-title">Customers</span>
			</a>
		</div>
	</div>
</div>
