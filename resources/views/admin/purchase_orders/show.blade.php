<x-default-layout>
    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Purchase Order Detail Preview</h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-{{ match($purchaseOrder->status) {
                            'confirmed', 'completed' => 'success',
                            'partial' => 'warning',
                            'delayed' => 'danger',
                            'sent' => 'primary',
                            default => 'secondary',
                        } }}">
                            {{ ucfirst($purchaseOrder->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-5 mb-8">
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">PO Number</div>
                                <div class="fw-bold fs-5">{{ $purchaseOrder->po_number }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Supplier</div>
                                <div class="fw-bold fs-5">{{ $purchaseOrder->supplier->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Buyer</div>
                                <div class="fw-bold fs-5">{{ $purchaseOrder->buyer->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Expected Delivery</div>
                                <div class="fw-bold fs-5">{{ optional($purchaseOrder->expected_delivery)->format('d M Y') ?: '-' }}</div>
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
                                @forelse($purchaseOrder->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? '-' }}</td>
                                        <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                        <td>{{ number_format((float) $item->received_qty, 2) }}</td>
                                        <td>{{ number_format((float) $item->unit_price, 2) }} {{ config('app.price_sign') }}</td>
                                        <td>{{ number_format((float) $item->quantity * (float) $item->unit_price, 2) }} {{ config('app.price_sign') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8">No PO items added yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th>{{ number_format($purchaseOrder->total_amount, 2) }} {{ config('app.price_sign') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Quick Actions</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <div class="fw-bold mb-1">Linked Request</div>
                        <div class="text-gray-600">{{ $purchaseOrder->request->request_no ?? '-' }}</div>
                    </div>
                    <div class="mb-5">
                        <div class="fw-bold mb-1">Requester</div>
                        <div class="text-gray-600">{{ $purchaseOrder->request->requester->name ?? '-' }}</div>
                    </div>
                    <div class="mb-5">
                        <div class="fw-bold mb-1">Order Date</div>
                        <div class="text-gray-600">{{ optional($purchaseOrder->order_date)->format('d M Y') }}</div>
                    </div>
                    <div class="mb-5">
                        <div class="fw-bold mb-1">Supplier Contact</div>
                        <div class="text-gray-600">{{ $purchaseOrder->supplier->email ?? '-' }}</div>
                        <div class="text-gray-600">{{ $purchaseOrder->supplier->phone ?? '' }}</div>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder->id) }}" class="btn btn-primary">Edit PO</a>
                        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-light">Back To List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
