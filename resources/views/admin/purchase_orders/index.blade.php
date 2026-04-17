<x-default-layout>
    @php
        $statusTone = static fn (string $status): string => match ($status) {
            'confirmed', 'completed' => 'success',
            'partial' => 'warning',
            'delayed' => 'danger',
            'sent' => 'primary',
            default => 'secondary',
        };
    @endphp

    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold">{{ $stats['total'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Total POs</div>
                    <div class="text-muted fs-7 mt-2">All purchase orders in the system.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-dark">{{ $stats['open'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Open POs</div>
                    <div class="text-muted fs-7 mt-2">Draft, sent, confirmed, partial, and delayed.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-primary">{{ $stats['sent'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Sent To Supplier</div>
                    <div class="text-muted fs-7 mt-2">Waiting for supplier confirmation.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-warning">{{ $stats['partial'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Partially Received</div>
                    <div class="text-muted fs-7 mt-2">Open lines still need follow-up.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div>
                    <h3 class="fw-bold mb-1">Purchase Orders</h3>
                    <div class="text-muted fs-6">Create, send, track, and receive supplier orders.</div>
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} New PO
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <form method="GET" class="row g-3 align-items-end mb-6">
                <input type="hidden" name="po" value="{{ request('po') }}">

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold fs-7">Status</label>
                    <select name="status" class="form-select form-select-solid">
                        <option value="">All statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold fs-7">Supplier</label>
                    <select name="supplier_id" class="form-select form-select-solid">
                        <option value="">All suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) request('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold fs-7">Department</label>
                    <select name="department_id" class="form-select form-select-solid">
                        <option value="">All departments</option>
                        @foreach($departments ?? [] as $department)
                            <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold fs-7">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-solid">
                </div>

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold fs-7">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-solid">
                </div>

                <div class="col-md-6 col-xl-3">
                    <label class="form-label fw-semibold fs-7">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid" placeholder="PO number, supplier...">
                </div>

                <div class="col-md-6 col-xl-3 d-flex gap-2">
                    <button class="btn btn-light-primary flex-fill">Filter</button>
                    @if(request()->hasAny(['q', 'status', 'supplier_id', 'department_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.purchase-orders.index', ['po' => request('po')]) }}" class="btn btn-light">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Linked Request</th>
                            <th>Order Date</th>
                            <th>Expected Delivery</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $purchaseOrder)
                            <tr @class(['bg-light-primary' => optional($selectedPurchaseOrder)->id === $purchaseOrder->id])>
                                <td>
                                    <a href="{{ route('admin.purchase-orders.index', array_merge(request()->query(), ['po' => $purchaseOrder->id])) }}" class="fw-bold text-gray-900 text-hover-primary">
                                        {{ $purchaseOrder->po_number }}
                                    </a>
                                    <div class="text-muted fs-7">{{ $purchaseOrder->buyer->name ?? 'No buyer' }}</div>
                                </td>
                                <td>{{ $purchaseOrder->supplier->name ?? '-' }}</td>
                                <td>{{ $purchaseOrder->request->request_no ?? '-' }}</td>
                                <td>{{ optional($purchaseOrder->order_date)->format('d M Y') ?: '-' }}</td>
                                <td>{{ optional($purchaseOrder->expected_delivery)->format('d M Y') ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $statusTone($purchaseOrder->status) }}">
                                        {{ ucfirst($purchaseOrder->status) }}
                                    </span>
                                </td>
                                <td>{{ config('app.price_sign') }} {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-sm btn-light-info">View</a>
                                    <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form action="{{ route('admin.purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this purchase order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10">No purchase orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $purchaseOrders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header border-0">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold mb-1">Purchase Order Detail Preview</h3>
                            <div class="text-muted fs-6">{{ $selectedPurchaseOrder->po_number ?? 'Select a purchase order' }}</div>
                        </div>
                    </div>
                    @if($selectedPurchaseOrder)
                        <div class="card-toolbar">
                            <span class="badge badge-light-{{ $statusTone($selectedPurchaseOrder->status) }}">
                                {{ ucfirst($selectedPurchaseOrder->status) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($selectedPurchaseOrder)
                        <div class="row g-5 mb-8">
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Supplier</div>
                                    <div class="fw-bold fs-5">{{ $selectedPurchaseOrder->supplier->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Buyer</div>
                                    <div class="fw-bold fs-5">{{ $selectedPurchaseOrder->buyer->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Expected Delivery</div>
                                    <div class="fw-bold fs-5">{{ optional($selectedPurchaseOrder->expected_delivery)->format('d M Y') ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Total</div>
                                    <div class="fw-bold fs-5">{{ config('app.price_sign') }} {{ number_format($selectedPurchaseOrder->total_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-row-bordered align-middle">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th>Item</th>
                                        <th>Ordered</th>
                                        <th>Received</th>
                                        <th>Unit Price</th>
                                        <th>Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedPurchaseOrder->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? '-' }}</td>
                                            <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                            <td>{{ number_format((float) $item->received_qty, 2) }}</td>
                                            <td>{{ config('app.price_sign') }} {{ number_format((float) $item->unit_price, 2) }}</td>
                                            <td>{{ config('app.price_sign') }} {{ number_format((float) $item->quantity * (float) $item->unit_price, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-8">No PO items added yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-15">Create a purchase order or pick one from the list to preview it here.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header border-0">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold mb-1">Quick Actions</h3>
                            <div class="text-muted fs-6">Keep supplier workflow moving.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($selectedPurchaseOrder)
                        <div class="mb-6">
                            <div class="fw-bold mb-1">Linked Request</div>
                            <div class="text-gray-600">{{ $selectedPurchaseOrder->request->request_no ?? '-' }}</div>
                        </div>
                        <div class="mb-6">
                            <div class="fw-bold mb-1">Requester</div>
                            <div class="text-gray-600">{{ $selectedPurchaseOrder->request->requester->name ?? '-' }}</div>
                        </div>
                        <div class="mb-6">
                            <div class="fw-bold mb-1">Supplier Contact</div>
                            <div class="text-gray-600">{{ $selectedPurchaseOrder->supplier->email ?? '-' }}</div>
                            <div class="text-gray-600">{{ $selectedPurchaseOrder->supplier->phone ?? '' }}</div>
                        </div>

                        <div class="d-grid gap-3">
                            @foreach($statuses as $status)
                                @continue($status === $selectedPurchaseOrder->status)

                                <form method="POST" action="{{ route('admin.purchase-orders.status.update', $selectedPurchaseOrder->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $status }}">
                                    <button class="btn btn-light-primary w-100">Mark as {{ ucfirst($status) }}</button>
                                </form>
                            @endforeach

                            <a href="{{ route('admin.purchase-orders.show', $selectedPurchaseOrder->id) }}" class="btn btn-primary">Open Full Detail</a>
                        </div>
                    @else
                        <div class="text-muted">Quick actions will appear here after you select a purchase order from the table.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
