<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <h3 class="fw-bold m-0">{{ isset($productCategory) ? 'Edit' : 'Create' }} Product Category</h3>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($productCategory) ? route('admin.purchase-orders.product-categories.update', $productCategory->id) : route('admin.purchase-orders.product-categories.store') }}">
                @csrf
                @if(isset($productCategory))
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
                            <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $productCategory->name ?? '') }}" required>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Slug</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="slug" class="form-control form-control-lg form-control-solid" value="{{ old('slug', $productCategory->slug ?? '') }}" placeholder="Optional">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Description</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="description" class="form-control form-control-lg form-control-solid" rows="4">{{ old('description', $productCategory->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Monthly Budget</label>
                        <div class="col-lg-8 fv-row">
                            <input
                                type="number"
                                name="monthly_budget"
                                class="form-control form-control-lg form-control-solid"
                                value="{{ old('monthly_budget', $productCategory->monthly_budget ?? 0) }}"
                                min="0"
                                step="1"
                                required
                            >
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Suppliers</label>
                        <div class="col-lg-8 fv-row">
                            @php
                                $selectedSuppliers = collect(old('supplier_ids', isset($productCategory) ? $productCategory->suppliers->pluck('id')->all() : []))
                                    ->map(fn ($id) => (string) $id)
                                    ->all();
                            @endphp
                            <select name="supplier_ids[]" class="form-select form-select-lg form-select-solid" multiple data-control="select2" data-placeholder="Select suppliers">
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(in_array((string) $supplier->id, $selectedSuppliers, true))>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Products in this category will use the first supplier alphabetically as the preferred supplier in Quick Add.</div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                        <div class="col-lg-8 fv-row">
                            <select name="status" class="form-select form-select-lg form-select-solid">
                                <option value="active" @selected(old('status', $productCategory->status ?? 'active') === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $productCategory->status ?? 'active') === 'inactive')>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.purchase-orders.product-categories.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ isset($productCategory) ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>
