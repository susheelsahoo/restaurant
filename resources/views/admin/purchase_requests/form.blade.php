<x-default-layout>
    @php
        $currency = env('PRICE_SIGN', '$');
        $money = static fn (float $amount): string => $currency . ' ' . number_format($amount, 2);

        $existingItems = old('items', isset($purchaseRequest) ? $purchaseRequest->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'supplier_id' => $item->supplier_id,
                'notes' => $item->notes,
            ];
        })->values()->all() : []);

        if (empty($existingItems)) {
            $existingItems = [['product_id' => '', 'quantity' => '', 'supplier_id' => '', 'notes' => '']];
        }

        $initialQuantityTotal = collect($existingItems)->sum(fn (array $item) => (float) ($item['quantity'] ?? 0));
        $productsById = $products->keyBy('id');
        $initialPriceTotal = collect($existingItems)->sum(function (array $item) use ($productsById) {
            $product = $productsById->get((int) ($item['product_id'] ?? 0));

            return ((float) ($item['quantity'] ?? 0)) * ((float) ($product->estimated_price ?? 0));
        });
    @endphp

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title m-0">
                        <div>
                            <h3 class="fw-bold m-0">{{ isset($purchaseRequest) ? 'Edit' : 'Create' }} Purchase Request</h3>
                            <div class="text-muted fs-6 mt-1">Capture the requester, department, timing, and requested line items in one place.</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ isset($purchaseRequest) ? route('admin.purchase-orders.requests.update', $purchaseRequest->id) : route('admin.purchase-orders.requests.store') }}">
                    @csrf
                    @if(isset($purchaseRequest))
                        @method('PUT')
                    @endif

                    <div class="card-body border-top p-9">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Request Number</label>
                            <div class="col-lg-8">
                                <input type="text" name="request_no" class="form-control form-control-lg form-control-solid" value="{{ old('request_no', $purchaseRequest->request_no ?? $defaultRequestNo) }}" placeholder="REQ-2026-0001">
                                <div class="text-muted fs-7 mt-2">Leave as-is or customize it before saving.</div>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Requester</label>
                            <div class="col-lg-8">
                                <select name="user_id" class="form-select form-select-solid" required>
                                    <option value="">Select requester</option>
                                    @foreach($requesters as $requester)
                                        <option value="{{ $requester->id }}" @selected((string) old('user_id', $purchaseRequest->user_id ?? auth()->id()) === (string) $requester->id)>{{ $requester->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Department</label>
                            <div class="col-lg-8">
                                <select name="department_id" class="form-select form-select-solid" required>
                                    <option value="">Select department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @selected((string) old('department_id', $purchaseRequest->department_id ?? '') === (string) $department->id)>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Priority</label>
                            <div class="col-lg-8">
                                <select name="priority" class="form-select form-select-solid" required>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority }}" @selected(old('priority', $purchaseRequest->priority ?? 'normal') === $priority)>{{ ucfirst($priority) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                            <div class="col-lg-8">
                                <select name="status" class="form-select form-select-solid" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" @selected(old('status', $purchaseRequest->status ?? 'submitted') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Needed By</label>
                            <div class="col-lg-8">
                                <input type="datetime-local" name="needed_by" class="form-control form-control-lg form-control-solid" value="{{ old('needed_by', isset($purchaseRequest) && $purchaseRequest->needed_by ? $purchaseRequest->needed_by->format('Y-m-d\\TH:i') : now()->addDay()->format('Y-m-d\\TH:i')) }}" required>
                            </div>
                        </div>

                        @if(isset($purchaseRequest) && filled($purchaseRequest->manager_comment))
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Manager Comment</label>
                                <div class="col-lg-8">
                                    <div class="alert alert-warning mb-0">
                                        {!! nl2br(e($purchaseRequest->manager_comment)) !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Admin Comment</label>
                            <div class="col-lg-8">
                                <textarea
                                    name="admin_comment"
                                    class="form-control form-control-lg form-control-solid"
                                    rows="4"
                                    placeholder="Write admin response or action note..."
                                >{{ old('admin_comment', $purchaseRequest->admin_comment ?? '') }}</textarea>
                                <div class="text-muted fs-7 mt-2">Use this when you adjust quantities, suppliers, or request status.</div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-10"></div>

                        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-4">
                            <div>
                                <h4 class="mb-1">Request Items</h4>
                                <div class="text-muted fs-7">Add the requested products, quantities, suggested suppliers, and any notes.</div>
                            </div>
                            <button type="button" class="btn btn-light-primary" id="add-request-item">Add Item</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-row-bordered align-middle" id="request-items-table">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th style="min-width: 240px;">Product</th>
                                        <th style="min-width: 140px;">Quantity</th>
                                        <th style="min-width: 150px;">Line Total</th>
                                        <th style="min-width: 220px;">Supplier</th>
                                        <th style="min-width: 240px;">Notes</th>
                                        <th class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($existingItems as $index => $item)
                                        <tr class="request-item-row">
                                            <td>
                                                <select name="items[{{ $index }}][product_id]" class="form-select form-select-solid item-product">
                                                    <option value="">Select product</option>
                                                    @foreach($products as $product)
                                                        <option
                                                            value="{{ $product->id }}"
                                                            data-price="{{ (float) ($product->estimated_price ?? 0) }}"
                                                            @selected((string) ($item['product_id'] ?? '') === (string) $product->id)
                                                        >
                                                            {{ $product->name }}{{ $product->unit ? ' (' . $product->unit . ')' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '' }}" class="form-control form-control-solid item-qty"></td>
                                            <td class="fw-bold item-line-total">
                                                @php
                                                    $product = $productsById->get((int) ($item['product_id'] ?? 0));
                                                    $lineTotal = ((float) ($item['quantity'] ?? 0)) * ((float) ($product->estimated_price ?? 0));
                                                @endphp
                                                {{ $money($lineTotal) }}
                                            </td>
                                            <td>
                                                <select name="items[{{ $index }}][supplier_id]" class="form-select form-select-solid item-supplier">
                                                    <option value="">Select supplier</option>
                                                    @foreach($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" @selected((string) ($item['supplier_id'] ?? '') === (string) $supplier->id)>{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="items[{{ $index }}][notes]" value="{{ $item['notes'] ?? '' }}" class="form-control form-control-solid"></td>
                                            <td class="text-end"><button type="button" class="btn btn-sm btn-light-danger remove-item">Remove</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('admin.purchase-orders.requests') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">{{ isset($purchaseRequest) ? 'Update' : 'Create' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Request Summary</h3>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="border rounded p-4 mb-4">
                        <div class="text-muted fs-7">Line Items</div>
                        <div class="fs-2 fw-bold" id="request-item-count">{{ count($existingItems) }}</div>
                    </div>
                    <div class="border rounded p-4 mb-4">
                        <div class="text-muted fs-7">Total Quantity</div>
                        <div class="fs-2 fw-bold"><span id="request-quantity-total">{{ number_format($initialQuantityTotal, 2) }}</span></div>
                    </div>
                    <div class="border rounded p-4 mb-4">
                        <div class="text-muted fs-7">Total Price</div>
                        <div class="fs-2 fw-bold"><span id="request-price-total">{{ $money($initialPriceTotal) }}</span></div>
                    </div>
                    <div class="text-muted fs-7">Tip: keep new requests as `Submitted`, then move them through review and ordering as the workflow progresses.</div>
                </div>
            </div>
        </div>
    </div>

    <template id="request-item-template">
        <tr class="request-item-row">
            <td>
                <select class="form-select form-select-solid item-product">
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ (float) ($product->estimated_price ?? 0) }}">{{ $product->name }}{{ $product->unit ? ' (' . $product->unit . ')' : '' }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-solid item-qty"></td>
            <td class="fw-bold item-line-total">{{ $money(0) }}</td>
            <td>
                <select class="form-select form-select-solid item-supplier">
                    <option value="">Select supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" class="form-control form-control-solid item-notes"></td>
            <td class="text-end"><button type="button" class="btn btn-sm btn-light-danger remove-item">Remove</button></td>
        </tr>
    </template>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.querySelector('#request-items-table tbody');
                const addButton = document.getElementById('add-request-item');
                const template = document.getElementById('request-item-template');
                const itemCountElement = document.getElementById('request-item-count');
                const quantityTotalElement = document.getElementById('request-quantity-total');
                const priceTotalElement = document.getElementById('request-price-total');
                const currency = @json($currency);

                function formatMoney(amount) {
                    return `${currency} ${amount.toFixed(2)}`;
                }

                function selectedProductPrice(row) {
                    const option = row.querySelector('.item-product').selectedOptions[0];

                    return parseFloat(option?.dataset.price || 0);
                }

                function renameRows() {
                    [...tableBody.querySelectorAll('.request-item-row')].forEach((row, index) => {
                        row.querySelector('.item-product').setAttribute('name', `items[${index}][product_id]`);
                        row.querySelector('.item-qty').setAttribute('name', `items[${index}][quantity]`);
                        row.querySelector('.item-supplier').setAttribute('name', `items[${index}][supplier_id]`);
                        row.querySelector('input[type="text"]').setAttribute('name', `items[${index}][notes]`);
                    });

                    itemCountElement.textContent = tableBody.querySelectorAll('.request-item-row').length;
                }

                function updateTotals() {
                    let quantityTotal = 0;
                    let priceTotal = 0;

                    tableBody.querySelectorAll('.request-item-row').forEach((row) => {
                        const quantity = parseFloat(row.querySelector('.item-qty').value || 0);
                        const lineTotal = quantity * selectedProductPrice(row);

                        quantityTotal += quantity;
                        priceTotal += lineTotal;
                        row.querySelector('.item-line-total').textContent = formatMoney(lineTotal);
                    });

                    quantityTotalElement.textContent = quantityTotal.toFixed(2);
                    priceTotalElement.textContent = formatMoney(priceTotal);
                }

                function bindRow(row) {
                    row.querySelector('.item-qty').addEventListener('input', updateTotals);
                    row.querySelector('.item-product').addEventListener('change', updateTotals);

                    row.querySelector('.remove-item').addEventListener('click', function () {
                        if (tableBody.querySelectorAll('.request-item-row').length === 1) {
                            row.querySelectorAll('input, select').forEach((field) => field.value = '');
                            updateTotals();
                            return;
                        }

                        row.remove();
                        renameRows();
                        updateTotals();
                    });
                }

                [...tableBody.querySelectorAll('.request-item-row')].forEach(bindRow);

                addButton.addEventListener('click', function () {
                    const fragment = template.content.cloneNode(true);
                    const row = fragment.querySelector('.request-item-row');
                    tableBody.appendChild(row);
                    renameRows();
                    bindRow(row);
                });

                renameRows();
                updateTotals();
            });
        </script>
    @endpush
</x-default-layout>
