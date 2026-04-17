@if (
	request()->routeIs('admin.purchase-orders.*') ||
	request()->routeIs('admin.purchase-orders.dashboard') ||
	request()->routeIs('admin.purchase-orders.requests') ||
	request()->routeIs('admin.purchase-orders.requests.*') ||
	request()->routeIs('admin.purchase-orders.approvals') ||
	request()->routeIs('admin.purchase-orders.products') ||
	request()->routeIs('admin.purchase-orders.products.*') ||
	request()->routeIs('admin.purchase-orders.product-categories.*') ||
	request()->routeIs('admin.purchase-orders.suppliers') ||
	request()->routeIs('admin.purchase-orders.suppliers.*') ||
	request()->routeIs('admin.purchase-orders.departments') ||
	request()->routeIs('admin.purchase-orders.departments.*') ||
	request()->routeIs('admin.purchase-orders.deliveries') ||
	request()->routeIs('admin.purchase-orders.reports')
)
	<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
		<div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
			<div class="menu-item me-lg-1">
				<a class="menu-link py-3 {{ request()->routeIs('admin.purchase-orders.dashboard') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.dashboard') }}">
					<span class="menu-title">PO Dashboard</span>
				</a>
			</div>
			<div class="menu-item me-lg-1">
				<a class="menu-link py-3 {{ request()->routeIs('admin.purchase-orders.requests') || request()->routeIs('admin.purchase-orders.requests.*') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.requests') }}">
					<span class="menu-title">Requests</span>
				</a>
			</div>
			<!--<div class="menu-item me-lg-1">
				<a class="menu-link py-3 {{ request()->routeIs('admin.purchase-orders.approvals') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.approvals') }}">
					<span class="menu-title">Approval</span>
				</a>
			</div>-->
			<div class="menu-item me-lg-1">
				<a class="menu-link py-3 {{ request()->routeIs('admin.purchase-orders.index') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.index') }}">
					<span class="menu-title">Purchase Orders</span>
				</a>
			</div>
			<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-lg-1 {{ request()->routeIs('admin.purchase-orders.products') || request()->routeIs('admin.purchase-orders.products.*') || request()->routeIs('admin.purchase-orders.product-categories.*') || request()->routeIs('admin.purchase-orders.suppliers') || request()->routeIs('admin.purchase-orders.suppliers.*') || request()->routeIs('admin.purchase-orders.departments') || request()->routeIs('admin.purchase-orders.departments.*') ? 'here show' : '' }}">
				<span class="menu-link py-3">
					<span class="menu-title">Settings</span>
					<span class="menu-arrow d-lg-none"></span>
				</span>

				<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-225px">
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.purchase-orders.product-categories.*') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.product-categories.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Category</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.purchase-orders.products') || request()->routeIs('admin.purchase-orders.products.*') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.products') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Product</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.purchase-orders.suppliers') || request()->routeIs('admin.purchase-orders.suppliers.*') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.suppliers') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Suppliers</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.purchase-orders.departments') || request()->routeIs('admin.purchase-orders.departments.*') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.departments.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Departments</span>
						</a>
					</div>
				</div>
			</div>
			<div class="menu-item me-lg-1">
				<a class="menu-link py-3 {{ request()->routeIs('admin.purchase-orders.deliveries') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.deliveries') }}">
					<span class="menu-title">Deliveries</span>
				</a>
			</div>
			<div class="menu-item me-lg-1">
				<a class="menu-link py-3 {{ request()->routeIs('admin.purchase-orders.reports') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.reports') }}">
					<span class="menu-title">Reports</span>
				</a>
			</div>
		</div>
	</div>
@endif
