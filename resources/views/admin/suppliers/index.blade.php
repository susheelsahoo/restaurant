<x-default-layout>
    @php
        $selectedStatus = strtolower((string) ($selectedSupplier->status ?? 'review'));
        $selectedBadgeClass = match ($selectedStatus) {
            'active' => 'success',
            'review' => 'warning',
            default => 'secondary',
        };
        $linkedGroups = $selectedSupplier?->products
            ?->pluck('category.name')
            ->filter()
            ->unique()
            ->values() ?? collect();
    @endphp

    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold">{{ $stats['total'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Suppliers</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-success">{{ $stats['active'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Active Suppliers</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-warning">{{ $stats['review'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Under Review</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-primary">{{ $stats['linked_products'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Product Links</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div>
                    <h3 class="fw-bold mb-1">Suppliers</h3>
                    <div class="text-muted fw-semibold fs-6">Review contact details, supplier-linked products, and current PO activity.</div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex flex-wrap gap-3">
                    <button type="button" class="btn btn-light" disabled>Export</button>
                    <a href="{{ route('admin.purchase-orders.suppliers.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!} Add Supplier
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
                    placeholder="Search supplier name, email, phone..."
                >

                <select name="status" class="form-select form-select-solid w-180px">
                    <option value="">Status: All</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>

                <button class="btn btn-light-primary">Filter</button>

                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.purchase-orders.suppliers') }}" class="btn btn-light">Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>Supplier</th>
                            <th>Contact</th>
                            <th>Products</th>
                            <th>Open POs</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            @php
                                $badgeClass = match (strtolower((string) $supplier->status)) {
                                    'active' => 'success',
                                    'review' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr class="{{ (int) request('supplier', $selectedSupplier?->id) === (int) $supplier->id ? 'bg-light-primary' : '' }}">
                                <td>
                                    <div class="fw-bold">{{ $supplier->name }}</div>
                                    <a href="{{ route('admin.purchase-orders.suppliers', array_filter(array_merge(request()->query(), ['supplier' => $supplier->id]))) }}" class="text-primary fs-7 fw-semibold">
                                        View profile
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $supplier->email ?: '-' }}</div>
                                    <div class="text-muted fs-7">{{ $supplier->phone ?: '-' }}</div>
                                </td>
                                <td>{{ $supplier->products_count }}</td>
                                <td>{{ $supplier->open_purchase_orders_count }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $badgeClass }}">{{ ucfirst($supplier->status ?: 'unknown') }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.purchase-orders.suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form action="{{ route('admin.purchase-orders.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this supplier?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <div class="fw-bold mb-2">No suppliers found</div>
                                    <div class="text-muted">Add supplier records to manage contacts, linked products, and open purchase orders here.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $suppliers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card card-xl-stretch">
                <div class="card-header">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold mb-1">Supplier Profile</h3>
                            <div class="text-muted fw-semibold fs-6">{{ $selectedSupplier?->name ?? 'No supplier selected' }}</div>
                        </div>
                    </div>
                    @if($selectedSupplier)
                        <div class="card-toolbar">
                            <span class="badge badge-light-{{ $selectedBadgeClass }}">{{ ucfirst($selectedSupplier->status) }}</span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($selectedSupplier)
                        <div class="row g-5">
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Supplier</div>
                                    <div class="fw-bold fs-5">{{ $selectedSupplier->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Email</div>
                                    <div class="fw-bold fs-5">{{ $selectedSupplier->email ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Phone</div>
                                    <div class="fw-bold fs-5">{{ $selectedSupplier->phone ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Open POs</div>
                                    <div class="fw-bold fs-5">{{ $selectedSupplier->open_purchase_orders_count }} active</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Linked Products</div>
                                    <div class="fw-bold fs-5">{{ $selectedSupplier->products_count }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Status</div>
                                    <div class="fw-bold fs-5">{{ ucfirst($selectedSupplier->status) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <div class="fw-bold mb-2">Linked Product Groups</div>
                            <div class="text-muted">
                                {{ $linkedGroups->isNotEmpty() ? $linkedGroups->join(', ') : 'No linked product groups yet.' }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="fw-bold fs-4 mb-2">No supplier selected</div>
                            <div class="text-muted">Choose a supplier from the table to review the profile and linked purchasing details.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-xl-stretch">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Supplier Notes</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-8">
                        <div class="fw-bold mb-1">Linked Products</div>
                        <div class="text-muted">Track which products each supplier supports and keep sourcing relationships clear.</div>
                    </div>
                    <div class="mb-8">
                        <div class="fw-bold mb-1">Current Activity</div>
                        <div class="text-muted">Review open purchase orders and use supplier status for follow-up or approval needs.</div>
                    </div>
                    <div class="mb-0">
                        <div class="fw-bold mb-1">Quality Review</div>
                        <div class="text-muted">Use the `review` status when a supplier needs attention before new orders are placed.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
