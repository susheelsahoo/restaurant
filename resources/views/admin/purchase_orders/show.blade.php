<x-default-layout>
    @php
        $statusTone = match ($purchaseOrder->status) {
            'confirmed', 'completed' => 'success',
            'partial' => 'warning',
            'delayed' => 'danger',
            'sent' => 'primary',
            default => 'secondary',
        };
    @endphp

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold m-0">Purchase Order Detail</h3>
                            <div class="text-muted fs-6 mt-1">{{ $purchaseOrder->po_number }}</div>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-{{ $statusTone }}">
                            {{ ucfirst($purchaseOrder->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-5 mb-8">
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
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Grand Total</div>
                                <div class="fw-bold fs-5">{{ config('app.price_sign') }} {{ number_format($purchaseOrder->total_amount, 2) }}</div>
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
                                    <th>Outstanding</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrder->items as $item)
                                    @php
                                        $ordered = (float) $item->quantity;
                                        $received = (float) $item->received_qty;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->product->name ?? '-' }}</td>
                                        <td>{{ number_format($ordered, 2) }}</td>
                                        <td>{{ number_format($received, 2) }}</td>
                                        <td>{{ number_format(max($ordered - $received, 0), 2) }}</td>
                                        <td>{{ config('app.price_sign') }} {{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td>{{ config('app.price_sign') }} {{ number_format($ordered * (float) $item->unit_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8">No PO items added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Total</th>
                                    <th>{{ config('app.price_sign') }} {{ number_format($purchaseOrder->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0">
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
                        <div class="text-gray-600">{{ optional($purchaseOrder->order_date)->format('d M Y') ?: '-' }}</div>
                    </div>
                    <div class="mb-5">
                        <div class="fw-bold mb-1">Supplier Contact</div>
                        <div class="text-gray-600">{{ $purchaseOrder->supplier->email ?? '-' }}</div>
                        <div class="text-gray-600">{{ $purchaseOrder->supplier->phone ?? '' }}</div>
                    </div>

                    <div class="separator separator-dashed my-7"></div>

                    <div class="d-grid gap-3 mb-6">
                        @foreach(['sent', 'confirmed', 'partial', 'completed', 'delayed'] as $status)
                            @continue($status === $purchaseOrder->status)

                            <form method="POST" action="{{ route('admin.purchase-orders.status.update', $purchaseOrder->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button class="btn btn-light-primary w-100">Mark as {{ ucfirst($status) }}</button>
                            </form>
                        @endforeach
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder->id) }}" class="btn btn-primary">Edit PO</a>
                        <a href="{{ route('admin.purchase-orders.index', ['po' => $purchaseOrder->id]) }}" class="btn btn-light">Back To List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
