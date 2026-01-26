<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">

	<div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">

		<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item here show menu-here-bg menu-lg-down-accordion me-0 me-lg-2">

			<span class="menu-link">
				<span class="menu-title">Dashboards</span>
				<span class="menu-arrow d-lg-none"></span>
			</span>


			<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0 w-100 w-lg-850px">
				@include(config('settings.KT_THEME_LAYOUT_DIR').'/partials/header-layout/header/_menu/__dashboards')
			</div>

		</div>


		<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">

			<span class="menu-link">
				<span class="menu-title">Pages</span>
				<span class="menu-arrow d-lg-none"></span>
			</span>


			<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0">
				@include(config('settings.KT_THEME_LAYOUT_DIR').'/partials/header-layout/header/_menu/__pages')
			</div>

		</div>


		<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">

			<span class="menu-link">
				<span class="menu-title">Apps</span>
				<span class="menu-arrow d-lg-none"></span>
			</span>


			<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px">

				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('rocket', 'fs-2') !!}</span>
						<span class="menu-title">Projects</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">My Projects</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">View Project</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Targets</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Budget</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Users</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Files</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Activity</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Settings</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('handcart', 'fs-2') !!}</span>
						<span class="menu-title">eCommerce</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Catalog</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Products</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Categories</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Add Product</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Edit Product</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Add Category</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Edit Category</span>
									</a>

								</div>

							</div>

						</div>


						<div data-kt-menu-trigger="click" class="menu-item menu-accordion menu-sub-indention">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Sales</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-accordion">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Orders Listing</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Order Details</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Add Order</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Edit Order</span>
									</a>

								</div>

							</div>

						</div>


						<div data-kt-menu-trigger="click" class="menu-item menu-accordion menu-sub-indention">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Customers</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-accordion">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Customers Listing</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Customers Details</span>
									</a>

								</div>

							</div>

						</div>


						<div data-kt-menu-trigger="click" class="menu-item menu-accordion menu-sub-indention">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Reports</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-accordion">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Products Viewed</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Sales</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Returns</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Customer Orders</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Shipping</span>
									</a>

								</div>

							</div>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Settings</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('chart', 'fs-2') !!}</span>
						<span class="menu-title">Support Center</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Overview</span>
							</a>

						</div>


						<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Tickets</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Ticket List</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Ticket View</span>
									</a>

								</div>

							</div>

						</div>


						<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Tutorials</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Tutorials List</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Tutorials Post</span>
									</a>

								</div>

							</div>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">FAQ</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Licenses</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Contact Us</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('shield-tick', 'fs-2') !!}</span>
						<span class="menu-title">User Management</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Users</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Users List</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">View User</span>
									</a>

								</div>

							</div>

						</div>


						<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Roles</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Roles List</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">View Roles</span>
									</a>

								</div>

							</div>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Permissions</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('phone', 'fs-2') !!}</span>
						<span class="menu-title">Contacts</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Getting Started</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Add Contact</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Edit Contact</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">View Contact</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('basket', 'fs-2') !!}</span>
						<span class="menu-title">Subscriptions</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Getting Started</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Subscription List</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Add Subscription</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">View Subscription</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('briefcase', 'fs-2') !!}</span>
						<span class="menu-title">Customers</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Getting Started</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Customer Listing</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Customer Details</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('credit-cart', 'fs-2') !!}</span>
						<span class="menu-title">Invoice Management</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

							<span class="menu-link">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Profile</span>
								<span class="menu-arrow"></span>
							</span>


							<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Invoice 1</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Invoice 2</span>
									</a>

								</div>


								<div class="menu-item">

									<a class="menu-link" href="{{ route('dashboard') }}">
										<span class="menu-bullet">
											<span class="bullet bullet-dot"></span>
										</span>
										<span class="menu-title">Invoice 3</span>
									</a>

								</div>

							</div>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Create Invoice</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('file-added', 'fs-2') !!}</span>
						<span class="menu-title">File Manager</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Folders</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Files</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Blank Directory</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Settings</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('sms', 'fs-2') !!}</span>
						<span class="menu-title">Inbox</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Messages</span>
								<span class="menu-badge">
									<span class="badge badge-light-success">3</span>
								</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Compose</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">View & Reply</span>
							</a>

						</div>

					</div>

				</div>


				<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">

					<span class="menu-link">
						<span class="menu-icon">{!! getIcon('message-text-2', 'fs-2') !!}</span>
						<span class="menu-title">Chat</span>
						<span class="menu-arrow"></span>
					</span>


					<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">

						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Private Chat</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Group Chat</span>
							</a>

						</div>


						<div class="menu-item">

							<a class="menu-link" href="{{ route('dashboard') }}">
								<span class="menu-bullet">
									<span class="bullet bullet-dot"></span>
								</span>
								<span class="menu-title">Drawer Chat</span>
							</a>

						</div>

					</div>

				</div>


				<div class="menu-item">

					<a class="menu-link" href="{{ route('dashboard') }}">
						<span class="menu-icon">{!! getIcon('calendar-8', 'fs-2') !!}</span>
						<span class="menu-title">Calendar</span>
					</a>

				</div>

			</div>

		</div>


		<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-0 me-lg-2">

			<span class="menu-link">
				<span class="menu-title">Layouts</span>
				<span class="menu-arrow d-lg-none"></span>
			</span>


			<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0 w-100 w-lg-850px">
				@include(config('settings.KT_THEME_LAYOUT_DIR').'/partials/header-layout/header/_menu/__layouts')
			</div>

		</div>


		<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">

			<span class="menu-link">
				<span class="menu-title">Help</span>
				<span class="menu-arrow d-lg-none"></span>
			</span>


			<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">

				<div class="menu-item">

					<a class="menu-link" href="https://preview.keenthemes.com/html/metronic/docs/base/utilities" target="_blank" title="Check out over 200 in-house components, plugins and ready for use solutions" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
						<span class="menu-icon">{!! getIcon('rocket', 'fs-2') !!}</span>
						<span class="menu-title">Components</span>
					</a>

				</div>


				<div class="menu-item">

					<a class="menu-link" href="https://preview.keenthemes.com/html/metronic/docs" target="_blank" title="Check out the complete documentation" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
						<span class="menu-icon">{!! getIcon('abstract-26', 'fs-2') !!}</span>
						<span class="menu-title">Documentation</span>
					</a>

				</div>


				<div class="menu-item">

					<a class="menu-link" href="https://preview.keenthemes.com/laravel/metronic/docs/changelog" target="_blank">
						<span class="menu-icon">{!! getIcon('code', 'fs-2') !!}</span>
						<span class="menu-title">Changelog v8.2.8</span>
					</a>

				</div>

			</div>

		</div>

	</div>

</div>