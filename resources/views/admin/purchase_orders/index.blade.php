<x-default-layout>
    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold">{{ $stats['open'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Open POs</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-primary">{{ $stats['sent'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Sent To Supplier</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-success">{{ $stats['confirmed'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Confirmed</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-warning">{{ $stats['partial'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Partially Received</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <form method="GET" class="d-flex flex-wrap gap-3 align-items-center">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid w-250px" placeholder="Search PO, supplier, buyer...">

                    <select name="status" class="form-select form-select-solid w-180px">
                        <option value="">Status: All</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>

                    <select name="supplier_id" class="form-select form-select-solid w-200px">
                        <option value="">Supplier: All</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) request('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>

                    <select name="buyer_id" class="form-select form-select-solid w-200px">
                        <option value="">Buyer: All</option>
                        @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}" @selected((string) request('buyer_id') === (string) $buyer->id)>{{ $buyer->name }}</option>
                        @endforeach
                    </select>

                    <button class="btn btn-light">Filter</button>
                </form>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} New PO
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Linked Request</th>
                            <th>Buyer</th>
                            <th>Order Date</th>
                            <th>Expected Delivery</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $purchaseOrder)
                            <tr>
                                <td class="fw-bold">{{ $purchaseOrder->po_number }}</td>
                                <td>{{ $purchaseOrder->supplier->name ?? '-' }}</td>
                                <td>{{ $purchaseOrder->request->request_no ?? '-' }}</td>
                                <td>{{ $purchaseOrder->buyer->name ?? '-' }}</td>
                                <td>{{ optional($purchaseOrder->order_date)->format('d M Y') }}</td>
                                <td>{{ optional($purchaseOrder->expected_delivery)->format('d M Y') ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-light-{{ match($purchaseOrder->status) {
                                        'confirmed', 'completed' => 'success',
                                        'partial' => 'warning',
                                        'delayed' => 'danger',
                                        'sent' => 'primary',
                                        default => 'secondary',
                                    } }}">
                                        {{ ucfirst($purchaseOrder->status) }}
                                    </span>
                                </td>
                                <td>{{ number_format($purchaseOrder->total_amount, 2) }} {{ config('app.price_sign') }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-sm btn-info">View</a>
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
                                <td colspan="9" class="text-center py-10">No purchase orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-5">
                    {{ $purchaseOrders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
