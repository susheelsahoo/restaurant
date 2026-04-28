<x-default-layout>
    @php
        $money = static fn (float $amount): string => env('PRICE_SIGN', '$') . ' ' . number_format($amount, 2);

        $statusTone = match ($purchaseRequest->status) {
            'approved', 'ordered' => 'success',
            'submitted' => 'primary',
            'rejected', 'returned' => 'danger',
            default => 'secondary',
        };
    @endphp

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0">
                    <div class="card-title">
                        <div>
                            <h3 class="fw-bold m-0">Purchase Request Detail</h3>
                            <div class="text-muted fs-6 mt-1">{{ $purchaseRequest->request_no }}</div>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-{{ $statusTone }}">
                            {{ ucfirst($purchaseRequest->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-5 mb-8">
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Requester</div>
                                <div class="fw-bold fs-5">{{ $purchaseRequest->requester->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Department</div>
                                <div class="fw-bold fs-5">{{ $purchaseRequest->department->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Priority</div>
                                <div class="fw-bold fs-5">{{ ucfirst($purchaseRequest->priority) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-4 h-100">
                                <div class="text-muted fs-7">Needed By</div>
                                <div class="fw-bold fs-5">{{ optional($purchaseRequest->needed_by)->format('d M Y H:i') ?: '-' }}</div>
                            </div>
                        </div>
                    </div>

                    @if(filled($purchaseRequest->manager_comment))
                        <div class="alert alert-warning d-flex flex-column gap-2 mb-8">
                            <div class="fw-bold">Manager Comment</div>
                            <div>{!! nl2br(e($purchaseRequest->manager_comment)) !!}</div>
                        </div>
                    @endif

                    @if(filled($purchaseRequest->admin_comment))
                        <div class="alert alert-primary d-flex flex-column gap-2 mb-8">
                            <div class="fw-bold">Admin Comment</div>
                            <div>{!! nl2br(e($purchaseRequest->admin_comment)) !!}</div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-row-bordered align-middle">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                    <th>Suggested Supplier</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseRequest->items as $item)
                                    @php
                                        $unitPrice = (float) ($item->product->estimated_price ?? 0);
                                        $lineTotal = ((float) $item->quantity) * $unitPrice;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->product->name ?? '-' }}</td>
                                        <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                        <td>{{ $money($unitPrice) }}</td>
                                        <td class="fw-bold">{{ $money($lineTotal) }}</td>
                                        <td>{{ $item->supplier->name ?? '-' }}</td>
                                        <td>{{ $item->notes ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8">No request items added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
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
                    <div class="row g-4 mb-5">
                        <div class="col-6">
                            <div class="border rounded p-4 h-100">
                                <div class="fw-bold mb-1">Linked PO</div>
                                <div class="text-gray-600">{{ $purchaseRequest->purchaseOrders->whereNull('parent_po_id')->count() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-4 h-100">
                                <div class="fw-bold mb-1">Item Count</div>
                                <div class="text-gray-600">{{ $purchaseRequest->items->count() }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-4 h-100">
                                <div class="fw-bold mb-1">Total Quantity</div>
                                <div class="text-gray-600">{{ number_format($purchaseRequest->total_quantity, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-4 h-100">
                                <div class="fw-bold mb-1">Total Price</div>
                                <div class="text-gray-600">{{ $money($purchaseRequest->total_price) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-7"></div>

                    <div class="mb-6">
                        <label for="adminCommentDetail" class="form-label fw-semibold">Admin Comment</label>
                        <textarea
                            id="adminCommentDetail"
                            class="form-control form-control-solid"
                            rows="4"
                            placeholder="Write admin response or action note..."
                        >{{ old('admin_comment', $purchaseRequest->admin_comment) }}</textarea>
                        <div class="text-muted fs-7 mt-2">This note is saved with the next status action.</div>
                    </div>

                    <div class="d-grid gap-3 mb-6">
                        @foreach(['submitted', 'approved', 'rejected', 'returned', 'ordered'] as $status)
                            @continue($status === $purchaseRequest->status)

                            <form method="POST" action="{{ route('admin.purchase-orders.requests.status.update', $purchaseRequest->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status }}">
                                <input type="hidden" name="admin_comment" class="admin-comment-input">
                                <button class="btn btn-light-primary w-100">Mark as {{ ucfirst($status) }}</button>
                            </form>
                        @endforeach
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admin.purchase-orders.requests.edit', $purchaseRequest->id) }}" class="btn btn-primary">Edit Request</a>
                        <a href="{{ route('admin.purchase-orders.requests', ['selected_request' => $purchaseRequest->id]) }}" class="btn btn-light">Back To List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.admin-comment-input').forEach((input) => {
                input.form.addEventListener('submit', () => {
                    input.value = document.getElementById('adminCommentDetail')?.value.trim() || '';
                });
            });
        </script>
    @endpush
</x-default-layout>
