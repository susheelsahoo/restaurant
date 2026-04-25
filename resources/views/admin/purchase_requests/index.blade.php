<x-default-layout>
    @php
        $money = static fn (float $amount): string => env('PRICE_SIGN', '$') . ' ' . number_format($amount, 2);

        $priorityTone = static fn (string $priority): string => match ($priority) {
            'urgent' => 'warning',
            'low' => 'secondary',
            default => 'light-primary',
        };

        $approvalLabel = static fn (string $status): string => match ($status) {
            'approved', 'ordered' => 'Approved',
            'rejected' => 'Rejected',
            'returned' => 'Needs Edit',
            default => 'Pending',
        };

        $approvalTone = static fn (string $status): string => match ($status) {
            'approved', 'ordered' => 'success',
            'rejected', 'returned' => 'danger',
            default => 'secondary',
        };
    @endphp

    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold">{{ $stats['total'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Total Requests</div>
                    <div class="text-muted fs-7 mt-2">All incoming purchase requests.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-primary">{{ $stats['submitted'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Submitted</div>
                    <div class="text-muted fs-7 mt-2">Awaiting review or approval.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-success">{{ $stats['approved'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Approved</div>
                    <div class="text-muted fs-7 mt-2">Ready for purchasing action.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-warning">{{ $stats['urgent'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Urgent</div>
                    <div class="text-muted fs-7 mt-2">High-priority requests needing attention.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div>
                    <h3 class="fw-bold mb-1">Requests</h3>
                    <div class="text-muted fs-6">Review all incoming purchasing requests and their current workflow status.</div>
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.purchase-orders.requests.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} New Manual Request
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <form method="GET" class="row g-3 align-items-end mb-6">
                <input type="hidden" name="selected_request" value="{{ request('selected_request') }}">

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold fs-7">Department</label>
                    <select name="department_id" class="form-select form-select-solid">
                        <option value="">All departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-xl-2">
                    <label class="form-label fw-semibold fs-7">Priority</label>
                    <select name="priority" class="form-select form-select-solid">
                        <option value="">All priorities</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ ucfirst($priority) }}</option>
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
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid" placeholder="Request number, requester, item...">
                </div>

                <div class="col-md-6 col-xl-3 d-flex gap-2">
                    <button class="btn btn-light-primary flex-fill">Filter</button>
                    @if(request()->hasAny(['q', 'department_id', 'priority', 'date_from', 'date_to']))
                        <a href="{{ route('admin.purchase-orders.requests', ['selected_request' => request('selected_request')]) }}" class="btn btn-light">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>Request No</th>
                            <th>Requester</th>
                            <th>Department</th>
                            <th>Items</th>
                            <th>Priority</th>
                            <th>Approval</th>
                            <th>Needed By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseRequests as $purchaseRequest)
                            <tr @class(['bg-light-primary' => optional($selectedPurchaseRequest)->id === $purchaseRequest->id])>
                                <td>
                                    <a href="{{ route('admin.purchase-orders.requests', array_merge(request()->query(), ['selected_request' => $purchaseRequest->id])) }}" class="fw-bold text-gray-900 text-hover-primary">
                                        {{ $purchaseRequest->request_no }}
                                    </a>
                                    <div class="text-muted fs-7">{{ optional($purchaseRequest->created_at)->diffForHumans() ?: 'Recently added' }}</div>
                                </td>
                                <td>{{ $purchaseRequest->requester->name ?? '-' }}</td>
                                <td>{{ $purchaseRequest->department->name ?? '-' }}</td>
                                <td>{{ $purchaseRequest->items_count }} items</td>
                                <td>
                                    <span class="badge badge-{{ $priorityTone($purchaseRequest->priority) }}">
                                        {{ ucfirst($purchaseRequest->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $approvalTone($purchaseRequest->status) }}">
                                        {{ $approvalLabel($purchaseRequest->status) }}
                                    </span>
                                </td>
                                <td>{{ optional($purchaseRequest->needed_by)->format('d M Y H:i') ?: '-' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.purchase-orders.requests.show', $purchaseRequest->id) }}" class="btn btn-sm btn-light-info">View</a>
                                    <a href="{{ route('admin.purchase-orders.requests.edit', $purchaseRequest->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form action="{{ route('admin.purchase-orders.requests.destroy', $purchaseRequest->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this purchase request?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10">No purchase requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $purchaseRequests->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header border-0">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold mb-1">Selected Request Preview</h3>
                            <div class="text-muted fs-6">{{ $selectedPurchaseRequest->request_no ?? 'Select a request' }}</div>
                        </div>
                    </div>
                    @if($selectedPurchaseRequest)
                        <div class="card-toolbar">
                            <span class="badge badge-light-{{ $approvalTone($selectedPurchaseRequest->status) }}">
                                {{ $approvalLabel($selectedPurchaseRequest->status) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($selectedPurchaseRequest)
                        <div class="row g-5 mb-8">
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Requester</div>
                                    <div class="fw-bold fs-5">{{ $selectedPurchaseRequest->requester->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Department</div>
                                    <div class="fw-bold fs-5">{{ $selectedPurchaseRequest->department->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Priority</div>
                                    <div class="fw-bold fs-5">{{ ucfirst($selectedPurchaseRequest->priority) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-4 h-100">
                                    <div class="text-muted fs-7">Needed By</div>
                                    <div class="fw-bold fs-5">{{ optional($selectedPurchaseRequest->needed_by)->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-row-bordered align-middle">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Supplier</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedPurchaseRequest->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? '-' }}</td>
                                            <td>{{ number_format((float) $item->quantity, 2) }} {{ $item->product->unit ?? '-' }}</td>
                                            <td>{{ $item->supplier->name ?? '-' }}</td>
                                            <td>{{ $item->notes ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-8">No request items added yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-15">Create a request or pick one from the list to preview it here.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header border-0">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold mb-1">Actions</h3>
                            <div class="text-muted fs-6">Available based on request status.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($selectedPurchaseRequest)
                        <div class="row g-4 mb-5">
                            <div class="col-6">
                                <div class="border rounded p-4 h-100">
                                    <div class="fw-bold mb-1">Linked PO</div>
                                    <div class="text-gray-600">{{ $selectedPurchaseRequest->purchaseOrders->count() }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-4 h-100">
                                    <div class="fw-bold mb-1">Item Count</div>
                                    <div class="text-gray-600">{{ $selectedPurchaseRequest->items->count() }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-4 h-100">
                                    <div class="fw-bold mb-1">Total Quantity</div>
                                    <div class="text-gray-600">{{ number_format($selectedPurchaseRequest->total_quantity, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-4 h-100">
                                    <div class="fw-bold mb-1">Total Price</div>
                                    <div class="text-gray-600">{{ $money($selectedPurchaseRequest->total_price) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            @foreach($statuses as $status)
                                @continue($status === $selectedPurchaseRequest->status)

                                <form method="POST" action="{{ route('admin.purchase-orders.requests.status.update', $selectedPurchaseRequest->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $status }}">
                                    <button class="btn btn-light-primary w-100">Mark as {{ ucfirst($status) }}</button>
                                </form>
                            @endforeach

                            <a href="{{ route('admin.purchase-orders.requests.show', $selectedPurchaseRequest->id) }}" class="btn btn-primary">Open Full Detail</a>
                        </div>
                    @else
                        <div class="text-muted">Action buttons will appear here after you select a request from the table.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
