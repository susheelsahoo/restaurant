<x-default-layout>
    @section('title')
        {{ isset($purchaseOrderTemplate) ? 'Edit' : 'Create' }} Purchase Order Template
    @endsection

    @push('styles')
        <style>
            .template-items-table .qty-col,
            .template-items-table .uom-col,
            .template-items-table .action-col {
                white-space: nowrap;
            }

            .template-items-table .qty-col {
                width: 90px;
            }

            .template-items-table .uom-col {
                width: 100px;
            }

            .template-items-table .action-col {
                width: 56px;
                text-align: center;
            }

            .template-items-table .qty-input {
                width: 90px;
                min-width: 90px;
            }

            .template-items-table .uom-input {
                width: 100px;
                min-width: 100px;
            }

            .template-items-table .remove-item {
                width: 36px;
                height: 36px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        </style>
    @endpush

    @php
        $existingItems = old('items', isset($purchaseOrderTemplate)
            ? $purchaseOrderTemplate->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'default_quantity' => $item->default_quantity,
                    'unit' => $item->unit,
                    'note' => $item->note,
                ];
            })->values()->all()
            : []);

        if (empty($existingItems)) {
            $existingItems = [[
                'product_id' => '',
                'default_quantity' => '1',
                'unit' => '',
                'note' => '',
            ]];
        }
    @endphp

    <div class="row g-5 g-xl-8">
        <div class="col-xl-8">
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title m-0">
                        <div>
                            <h3 class="fw-bold m-0">{{ isset($purchaseOrderTemplate) ? 'Edit' : 'Create' }} Purchase Order Template</h3>
                            <div class="text-muted fs-6 mt-1">Build reusable department-wise order templates with default quantities and notes.</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ isset($purchaseOrderTemplate) ? route('admin.purchase-order-templates.update', $purchaseOrderTemplate->id) : route('admin.purchase-order-templates.store') }}">
                    @csrf
                    @if(isset($purchaseOrderTemplate))
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
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Template Name</label>
                            <div class="col-lg-8">
                                <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $purchaseOrderTemplate->name ?? '') }}" placeholder="Daily Kitchen Essentials" required>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Department</label>
                            <div class="col-lg-8">
                                <select name="department_id" class="form-select form-select-solid">
                                    <option value="">All departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @selected((string) old('department_id', $purchaseOrderTemplate->department_id ?? '') === (string) $department->id)>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Priority</label>
                            <div class="col-lg-8">
                                <select name="priority" class="form-select form-select-solid" required>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority }}" @selected(old('priority', $purchaseOrderTemplate->priority ?? 'normal') === $priority)>{{ ucfirst($priority) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                            <div class="col-lg-8">
                                <select name="status" class="form-select form-select-solid" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" @selected(old('status', $purchaseOrderTemplate->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Description</label>
                            <div class="col-lg-8">
                                <textarea name="description" class="form-control form-control-lg form-control-solid" rows="4" placeholder="Reusable daily order template for vegetables, dairy, and prep items.">{{ old('description', $purchaseOrderTemplate->description ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-10"></div>

                        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-4">
                            <div>
                                <h4 class="mb-1">Template Items</h4>
                                <div class="text-muted fs-7">Add catalog products with default quantity, unit, and notes.</div>
                            </div>
                            <button type="button" class="btn btn-light-primary" id="add-template-item">Add Item</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-row-bordered align-middle template-items-table" id="template-items-table">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th style="min-width: 220px;">Product</th>
                                        <th class="qty-col">Qty</th>
                                        <th class="uom-col">UOM</th>
                                        <th style="min-width: 200px;">Note</th>
                                        <th class="action-col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($existingItems as $index => $item)
                                        <tr class="template-item-row">
                                            <td>
                                                <select name="items[{{ $index }}][product_id]" class="form-select form-select-solid item-product">
                                                    <option value="">Select product</option>
                                                    @foreach($products as $product)
                                                        <option
                                                            value="{{ $product->id }}"
                                                            data-name="{{ $product->name }}"
                                                            data-category="{{ $product->category_name }}"
                                                            data-unit="{{ $product->unit }}"
                                                            @selected((string) ($item['product_id'] ?? '') === (string) $product->id)
                                                        >
                                                            {{ $product->name }}{{ $product->category_name ? ' - ' . $product->category_name : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="qty-col"><input type="number" step="0.01" min="0.01" name="items[{{ $index }}][default_quantity]" value="{{ $item['default_quantity'] ?? '1' }}" class="form-control form-control-solid qty-input"></td>
                                            <td class="uom-col"><input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? '' }}" class="form-control form-control-solid item-unit uom-input" placeholder="kg"></td>
                                            <td><input type="text" name="items[{{ $index }}][note]" value="{{ $item['note'] ?? '' }}" class="form-control form-control-solid" placeholder="Optional note"></td>
                                            <td class="action-col">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-item" title="Remove item" aria-label="Remove item">
                                                    {!! getIcon('trash', 'fs-4', '', 'i') !!}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('admin.purchase-order-templates.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">{{ isset($purchaseOrderTemplate) ? 'Update' : 'Create' }} Template</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title">
                        <h3 class="fw-bold m-0">Template Summary</h3>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="border rounded p-4 mb-4">
                        <div class="text-muted fs-7">Line Items</div>
                        <div class="fs-2 fw-bold" id="template-item-count">{{ count($existingItems) }}</div>
                    </div>
                    <div class="border rounded p-4 mb-4">
                        <div class="text-muted fs-7">Catalog Products</div>
                        <div class="fs-2 fw-bold">{{ $products->count() }}</div>
                    </div>
                    <div class="text-muted fs-7">Tip: choose a catalog product to auto-fill the unit and keep template rows compact.</div>
                </div>
            </div>
        </div>
    </div>

    <template id="template-item-template">
        <tr class="template-item-row">
            <td>
                <select class="form-select form-select-solid item-product">
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-category="{{ $product->category_name }}" data-unit="{{ $product->unit }}">
                            {{ $product->name }}{{ $product->category_name ? ' - ' . $product->category_name : '' }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="qty-col"><input type="number" step="0.01" min="0.01" value="1" class="form-control form-control-solid qty-input"></td>
            <td class="uom-col"><input type="text" class="form-control form-control-solid item-unit uom-input" placeholder="kg"></td>
            <td><input type="text" class="form-control form-control-solid" placeholder="Optional note"></td>
            <td class="action-col">
                <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-item" title="Remove item" aria-label="Remove item">
                    {!! getIcon('trash', 'fs-4', '', 'i') !!}
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableBody = document.querySelector('#template-items-table tbody');
                const addButton = document.getElementById('add-template-item');
                const template = document.getElementById('template-item-template');
                const itemCount = document.getElementById('template-item-count');

                function updateCount() {
                    itemCount.textContent = String(tableBody.querySelectorAll('.template-item-row').length);
                }

                function reindexRows() {
                    tableBody.querySelectorAll('.template-item-row').forEach((row, index) => {
                        row.querySelectorAll('input, select').forEach((field) => {
                            if (field.classList.contains('item-product')) {
                                field.name = `items[${index}][product_id]`;
                            } else if (field.classList.contains('item-unit')) {
                                field.name = `items[${index}][unit]`;
                            } else if (field.type === 'number') {
                                field.name = `items[${index}][default_quantity]`;
                            } else {
                                field.name = `items[${index}][note]`;
                            }
                        });
                    });
                }

                function bindRow(row) {
                    row.querySelector('.remove-item')?.addEventListener('click', function () {
                        if (tableBody.querySelectorAll('.template-item-row').length === 1) {
                            row.querySelectorAll('input').forEach((input) => {
                                input.value = input.type === 'number' ? '1' : '';
                            });
                            row.querySelector('.item-product').value = '';
                        } else {
                            row.remove();
                            reindexRows();
                            updateCount();
                        }
                    });

                    row.querySelector('.item-product')?.addEventListener('change', function () {
                        const selected = this.options[this.selectedIndex];

                        if (!selected || !selected.value) {
                            return;
                        }

                        row.querySelector('.item-unit').value = selected.dataset.unit || '';
                    });
                }

                addButton?.addEventListener('click', function () {
                    const fragment = template.content.cloneNode(true);
                    const row = fragment.querySelector('.template-item-row');
                    tableBody.appendChild(fragment);
                    bindRow(tableBody.lastElementChild);
                    reindexRows();
                    updateCount();
                });

                tableBody.querySelectorAll('.template-item-row').forEach(bindRow);
                reindexRows();
                updateCount();
            });
        </script>
    @endpush
</x-default-layout>
