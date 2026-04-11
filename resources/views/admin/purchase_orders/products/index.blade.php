<x-default-layout>
    @php
        $selectedSuppliers = $selectedProduct?->suppliers ?? collect();
        $selectedSupplierNames = $selectedSuppliers->pluck('name')->filter();
        $selectedStatus = strtolower((string) ($selectedProduct->status ?? 'inactive'));
        $selectedBadgeClass = match ($selectedStatus) {
            'active' => 'success',
            'inactive' => 'secondary',
            default => 'warning',
        };
    @endphp

    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold">{{ $stats['total'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Catalog Products</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-success">{{ $stats['active'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Active Products</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-primary">{{ $stats['barcode_enabled'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Barcode Enabled</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-warning">{{ $stats['catalog_linked_suppliers'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Linked Suppliers</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div>
                    <h3 class="fw-bold mb-1">Products</h3>
                    <div class="text-muted fw-semibold fs-6">Manage requestable items, scan codes, and preferred suppliers.</div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('admin.purchase-orders.product-categories.index') }}" class="btn btn-light-primary">
                        Product Categories
                    </a>
                    <button type="button" class="btn btn-light" disabled>Import</button>
                    <a href="{{ route('admin.purchase-orders.products.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!} Add Product
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <form method="GET" class="d-flex flex-wrap gap-3 mb-8">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="form-control form-control-solid w-250px"
                    placeholder="Search name, SKU, barcode..."
                >

                <select name="category" class="form-select form-select-solid w-200px">
                    <option value="">Category: All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="supplier" class="form-select form-select-solid w-200px">
                    <option value="">Supplier: All</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected((string) request('supplier') === (string) $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-select form-select-solid w-180px">
                    <option value="">Status: All</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>

                <button class="btn btn-light-primary">Filter</button>

                @if(request()->hasAny(['q', 'category', 'supplier', 'status']))
                    <a href="{{ route('admin.purchase-orders.products') }}" class="btn btn-light">Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Preferred Supplier</th>
                            <th>Barcode</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $badgeClass = match (strtolower((string) $product->status)) {
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    default => 'warning',
                                };
                            @endphp
                            <tr class="{{ (int) request('product', $selectedProduct?->id) === (int) $product->id ? 'bg-light-primary' : '' }}">
                                <td>
                                    <div class="fw-bold">{{ $product->name }}</div>
                                    <div class="text-muted fs-7">
                                        {{ $product->request_lines_count }} request lines, {{ number_format((float) $product->ordered_quantity, 2) }} ordered qty
                                    </div>
                                    <a href="{{ route('admin.purchase-orders.products', array_filter(array_merge(request()->query(), ['product' => $product->id]))) }}" class="text-primary fs-7 fw-semibold">
                                        View details
                                    </a>
                                </td>
                                <td>{{ $product->sku ?: '-' }}</td>
                                <td>{{ $product->category?->name ?: '-' }}</td>
                                <td>{{ $product->unit ?: '-' }}</td>
                                <td>{{ $product->preferred_supplier_name ?: '-' }}</td>
                                <td>{{ $product->barcode ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $badgeClass }}">{{ ucfirst($product->status ?: 'unknown') }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.purchase-orders.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form action="{{ route('admin.purchase-orders.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10">
                                    <div class="fw-bold mb-2">No products found</div>
                                    <div class="text-muted">Start by adding products to the catalog, then this PO product management page will show live catalog data here.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card card-xl-stretch">
                <div class="card-header">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold mb-1">Selected Product</h3>
                            <div class="text-muted fw-semibold fs-6">{{ $selectedProduct?->name ?? 'No product selected' }}</div>
                        </div>
                    </div>
                    @if($selectedProduct)
                        <div class="card-toolbar">
                            <span class="badge badge-light-{{ $selectedBadgeClass }}">
                                {{ $selectedProduct->barcode ? 'Barcode enabled' : 'Barcode pending' }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($selectedProduct)
                        <div class="row g-5">
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Name</div>
                                    <div class="fw-bold fs-5">{{ $selectedProduct->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Default Unit</div>
                                    <div class="fw-bold fs-5">{{ $selectedProduct->unit ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Category</div>
                                    <div class="fw-bold fs-5">{{ $selectedProduct->category?->name ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Supplier</div>
                                    <div class="fw-bold fs-5">{{ $selectedSupplierNames->isNotEmpty() ? $selectedSupplierNames->join(', ') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Barcode</div>
                                    <div class="fw-bold fs-5">{{ $selectedProduct->barcode ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Internal Code</div>
                                    <div class="fw-bold fs-5">{{ $selectedProduct->sku ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Request Lines</div>
                                    <div class="fw-bold fs-5">{{ number_format((float) ($selectedProduct->request_lines_count ?? 0)) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">PO Lines</div>
                                    <div class="fw-bold fs-5">{{ number_format((float) ($selectedProduct->po_items_count ?? $selectedProduct->po_lines_count ?? 0)) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Ordered Quantity</div>
                                    <div class="fw-bold fs-5">{{ number_format((float) ($selectedProduct->ordered_quantity_total ?? $selectedProduct->ordered_quantity ?? 0), 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="fw-bold fs-4 mb-2">No product selected</div>
                            <div class="text-muted">Choose a product from the table to review its catalog details and purchasing activity.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-xl-stretch">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Catalog Actions</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-8">
                        <div class="fw-bold mb-1">Edit product</div>
                        <div class="text-muted">Update item details, units, category, barcode, and product status.</div>
                    </div>
                    <div class="mb-8">
                        <div class="fw-bold mb-1">Add alternate code</div>
                        <div class="text-muted">Support supplier-specific identifiers or future multi-barcode workflows.</div>
                    </div>
                    <div class="mb-0">
                        <div class="fw-bold mb-1">Deactivate item</div>
                        <div class="text-muted">Hide old or replaced products from request and PO selection screens.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
