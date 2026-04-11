<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <h3 class="fw-bold m-0">{{ isset($product) ? 'Edit' : 'Create' }} Product</h3>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($product) ? route('admin.purchase-orders.products.update', $product->id) : route('admin.purchase-orders.products.store') }}">
                @csrf
                @if(isset($product))
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
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Name</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $product->name ?? '') }}" required>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">SKU</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="sku" class="form-control form-control-lg form-control-solid" value="{{ old('sku', $product->sku ?? '') }}" required>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Category</label>
                        <div class="col-lg-8 fv-row">
                            <select name="category_id" class="form-select form-select-lg form-select-solid">
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Unit</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="unit" class="form-control form-control-lg form-control-solid" value="{{ old('unit', $product->unit ?? '') }}" placeholder="kg, pcs, pack">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Barcode</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="barcode" class="form-control form-control-lg form-control-solid" value="{{ old('barcode', $product->barcode ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Suppliers</label>
                        <div class="col-lg-8 fv-row">
                            @php
                                $selectedSuppliers = collect(old('supplier_ids', isset($product) ? $product->suppliers->pluck('id')->all() : []))->map(fn ($id) => (string) $id)->all();
                            @endphp
                            <select name="supplier_ids[]" class="form-select form-select-lg form-select-solid" multiple data-control="select2" data-placeholder="Select suppliers">
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(in_array((string) $supplier->id, $selectedSuppliers, true))>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                        <div class="col-lg-8 fv-row">
                            <select name="status" class="form-select form-select-lg form-select-solid">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', $product->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.purchase-orders.products') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>
