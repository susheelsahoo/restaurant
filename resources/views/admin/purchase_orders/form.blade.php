<x-default-layout>
    @php
        $existingItems = old('items', isset($purchaseOrder) ? $purchaseOrder->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'received_qty' => $item->received_qty,
                'unit_price' => $item->unit_price,
            ];
        })->values()->all() : []);

        if (empty($existingItems)) {
            $existingItems = [['product_id' => '', 'quantity' => '', 'received_qty' => '', 'unit_price' => '']];
        }
    @endphp

    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($purchaseOrder) ? 'Edit' : 'Create' }} Purchase Order</h3>
            </div>
        </div>

        <form method="POST" action="{{ isset($purchaseOrder) ? route('admin.purchase-orders.update', $purchaseOrder->id) : route('admin.purchase-orders.store') }}">
            @csrf
            @if(isset($purchaseOrder))
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
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">PO Number</label>
                    <div class="col-lg-8">
                        <input type="text" name="po_number" class="form-control form-control-lg form-control-solid" value="{{ old('po_number', $purchaseOrder->po_number ?? '') }}" placeholder="PO-2026-0001" required>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Linked Request</label>
                    <div class="col-lg-8">
                        <select name="request_id" class="form-select form-select-solid">
                            <option value="">Select request</option>
                            @foreach($requests as $requestItem)
                                <option value="{{ $requestItem->id }}" @selected((string) old('request_id', $purchaseOrder->request_id ?? '') === (string) $requestItem->id)>
                                    {{ $requestItem->request_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Supplier</label>
                    <div class="col-lg-8">
                        <select name="supplier_id" class="form-select form-select-solid">
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $purchaseOrder->supplier_id ?? '') === (string) $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Buyer</label>
                    <div class="col-lg-8">
                        <select name="buyer_id" class="form-select form-select-solid">
                            <option value="">Select buyer</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}" @selected((string) old('buyer_id', $purchaseOrder->buyer_id ?? auth()->id()) === (string) $buyer->id)>
                                    {{ $buyer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                    <div class="col-lg-8">
                        <select name="status" class="form-select form-select-solid" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $purchaseOrder->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Order Date</label>
                    <div class="col-lg-8">
                        <input type="date" name="order_date" class="form-control form-control-lg form-control-solid" value="{{ old('order_date', isset($purchaseOrder) && $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Expected Delivery</label>
                    <div class="col-lg-8">
                        <input type="date" name="expected_delivery" class="form-control form-control-lg form-control-solid" value="{{ old('expected_delivery', isset($purchaseOrder) && $purchaseOrder->expected_delivery ? $purchaseOrder->expected_delivery->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h4 class="mb-1">PO Items</h4>
                        <div class="text-muted fs-7">Add ordered products, quantity, received quantity, and unit price.</div>
                    </div>
                    <button type="button" class="btn btn-light-primary" id="add-po-item">Add Item</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle" id="po-items-table">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Received</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($existingItems as $index => $item)
                                <tr class="po-item-row">
                                    <td>
                                        <select name="items[{{ $index }}][product_id]" class="form-select form-select-solid">
                                            <option value="">Select product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>
                                                    {{ $product->name }}{{ $product->unit ? ' (' . $product->unit . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '' }}" class="form-control form-control-solid item-qty"></td>
                                    <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][received_qty]" value="{{ $item['received_qty'] ?? '' }}" class="form-control form-control-solid"></td>
                                    <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" class="form-control form-control-solid item-price"></td>
                                    <td class="item-total fw-bold text-gray-700">0.00</td>
                                    <td class="text-end"><button type="button" class="btn btn-sm btn-light-danger remove-item">Remove</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ isset($purchaseOrder) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>

    <template id="po-item-template">
        <tr class="po-item-row">
            <td>
                <select class="form-select form-select-solid item-product">
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-unit="{{ $product->unit }}">{{ $product->name }}{{ $product->unit ? ' (' . $product->unit . ')' : '' }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-solid item-qty"></td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-solid item-received"></td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-solid item-price"></td>
            <td class="item-total fw-bold text-gray-700">0.00</td>
            <td class="text-end"><button type="button" class="btn btn-sm btn-light-danger remove-item">Remove</button></td>
        </tr>
    </template>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.querySelector('#po-items-table tbody');
                const addButton = document.getElementById('add-po-item');
                const template = document.getElementById('po-item-template');

                function renameRows() {
                    [...tableBody.querySelectorAll('.po-item-row')].forEach((row, index) => {
                        row.querySelector('.item-product, select[name*="[product_id]"]').setAttribute('name', `items[${index}][product_id]`);
                        row.querySelector('.item-qty').setAttribute('name', `items[${index}][quantity]`);
                        row.querySelector('.item-received, input[name*="[received_qty]"]').setAttribute('name', `items[${index}][received_qty]`);
                        row.querySelector('.item-price').setAttribute('name', `items[${index}][unit_price]`);
                    });
                }

                function updateRowTotal(row) {
                    const qty = parseFloat(row.querySelector('.item-qty').value || 0);
                    const price = parseFloat(row.querySelector('.item-price').value || 0);
                    row.querySelector('.item-total').textContent = (qty * price).toFixed(2);
                }

                function bindRow(row) {
                    row.querySelectorAll('.item-qty, .item-price').forEach((input) => {
                        input.addEventListener('input', function () {
                            updateRowTotal(row);
                        });
                    });

                    row.querySelector('.remove-item').addEventListener('click', function () {
                        if (tableBody.querySelectorAll('.po-item-row').length === 1) {
                            row.querySelectorAll('input, select').forEach((field) => field.value = '');
                            updateRowTotal(row);
                            return;
                        }

                        row.remove();
                        renameRows();
                    });

                    updateRowTotal(row);
                }

                [...tableBody.querySelectorAll('.po-item-row')].forEach(bindRow);

                addButton.addEventListener('click', function () {
                    const fragment = template.content.cloneNode(true);
                    const row = fragment.querySelector('.po-item-row');
                    tableBody.appendChild(row);
                    renameRows();
                    bindRow(row);
                });

                renameRows();
            });
        </script>
    @endpush
</x-default-layout>
