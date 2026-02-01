<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
	<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
		<div class="menu menu-column menu-rounded menu-sub-indention px-3 fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
			<div class="menu-item">
				<a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
					<span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
					<span class="menu-title">Dashboards</span>
				</a>
			</div>
			<div class="menu-item">
				<a class="menu-link {{ request()->routeIs('admin.banners.index') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
					<span class="menu-icon">{!! getIcon('photoshop', 'fs-2') !!}</span>
					<span class="menu-title">Notification</span>
				</a>
			</div>
			<div class="menu-item">
				<a class="menu-link {{ request()->routeIs('admin.pages.index') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
					<span class="menu-icon">{!! getIcon('abstract-26', 'fs-2') !!}</span>
					<span class="menu-title">Pages</span>
				</a>
			</div>
			<div class="menu-item">
				<a class="menu-link {{ request()->routeIs('admin.gallery.index') ? 'active' : '' }}" href="{{ route('admin.gallery.index') }}">
					<span class="menu-icon">{!! getIcon('picture', 'fs-2') !!}</span>
					<span class="menu-title">Gallery</span>
				</a>
			</div>

			<div class="menu-item">
				<a class="menu-link {{ request()->routeIs('admin.contacts.index') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">
					<span class="menu-icon">{!! getIcon('sms', 'fs-2') !!}</span>
					<span class="menu-title">Contacts</span>
				</a>
			</div>
			<div class="menu-item">
				<a class="menu-link {{ request()->routeIs('admin.leads.index') ? 'active' : '' }}" href="{{ route('admin.leads.index') }}">
					<span class="menu-icon">{!! getIcon('chart', 'fs-2') !!}</span>
					<span class="menu-title">Leads</span>
				</a>
			</div>
			<!-- <div class="menu-item">
				<a class="menu-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
					<span class="menu-icon">{!! getIcon('gear', 'fs-2') !!}</span>
					<span class="menu-title">SeoSettings</span>
				</a>
			</div> -->
			<div class="menu-item pt-5">
				<div class="menu-content">
					<span class="menu-heading fw-bold text-uppercase fs-7">Content</span>
				</div>
			</div>

			<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.blogs.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.tags.*') ? 'here show' : '' }}">
				<span class="menu-link">
					<span class="menu-icon">{!! getIcon('text-align-left', 'fs-2') !!}</span>
					<span class="menu-title">Blog</span>
					<span class="menu-arrow"></span>
				</span>
				<div class="menu-sub menu-sub-accordion">
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}" href="{{ route('admin.blogs.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Posts</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Categories</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" href="{{ route('admin.tags.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Tags</span>
						</a>
					</div>
				</div>
			</div>
			<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.menus.*') || request()->routeIs('admin.menu-categories.*') ? 'here show' : '' }}">
				<span class="menu-link">
					<span class="menu-icon">{!! getIcon('text-align-left', 'fs-2') !!}</span>
					<span class="menu-title">Menu</span>
					<span class="menu-arrow"></span>
				</span>
				<div class="menu-sub menu-sub-accordion">
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}" href="{{ route('admin.menus.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Menu</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.menu-categories.*') ? 'active' : '' }}" href="{{ route('admin.menu-categories.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Menu Categories</span>
						</a>
					</div>
				</div>
			</div>

			<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.wines.*') || request()->routeIs('admin.wine-categories.*') ? 'here show' : '' }}">
				<span class="menu-link">
					<span class="menu-icon">{!! getIcon('text-align-left', 'fs-2') !!}</span>
					<span class="menu-title">Wines</span>
					<span class="menu-arrow"></span>
				</span>
				<div class="menu-sub menu-sub-accordion">
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.wines.*') ? 'active' : '' }}" href="{{ route('admin.wines.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Wines</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.wine-categories.*') ? 'active' : '' }}" href="{{ route('admin.wine-categories.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Wine Categories</span>
						</a>
					</div>
				</div>
			</div>


			<div class="menu-item pt-5">
				<div class="menu-content">
					<span class="menu-heading fw-bold text-uppercase fs-7">Apps</span>
				</div>
			</div>
			<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('admin.user-management.*') ? 'here show' : '' }}">
				<span class="menu-link">
					<span class="menu-icon">{!! getIcon('abstract-28', 'fs-2') !!}</span>
					<span class="menu-title">User Management</span>
					<span class="menu-arrow"></span>
				</span>
				<div class="menu-sub menu-sub-accordion">
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.user-management.users.*') ? 'active' : '' }}" href="{{ route('admin.user-management.users.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Users</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.user-management.roles.*') ? 'active' : '' }}" href="{{ route('admin.user-management.roles.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Roles</span>
						</a>
					</div>
					<div class="menu-item">
						<a class="menu-link {{ request()->routeIs('admin.user-management.permissions.*') ? 'active' : '' }}" href="{{ route('admin.user-management.permissions.index') }}">
							<span class="menu-bullet">
								<span class="bullet bullet-dot"></span>
							</span>
							<span class="menu-title">Permissions</span>
						</a>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>