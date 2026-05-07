<x-default-layout>
    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-xl-4 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold">{{ $productsCount }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Catalog Products</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-primary">{{ $barcodeEnabledCount }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Barcode Enabled</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-success">{{ $categoriesCount }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Categories</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div>
                    <h3 class="fw-bold mb-1">Import Products</h3>
                    <div class="text-muted fw-semibold fs-6">Upload a CSV to create or update catalog products by SKU.</div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('admin.purchase-orders.products') }}" class="btn btn-light">
                        Back to Products
                    </a>
                    <a href="{{ route('admin.purchase-orders.products.import.sample') }}" class="btn btn-light-success">
                        Sample CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('admin.purchase-orders.products.import') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="row mb-8">
                    <label class="col-lg-3 col-form-label required fw-semibold fs-6">Products CSV</label>
                    <div class="col-lg-9">
                        <input
                            type="file"
                            name="products_file"
                            class="form-control form-control-lg form-control-solid"
                            accept=".csv,text/csv"
                            required
                        >
                        <div class="form-text">
                            Required columns: name, sku. Optional: category_id, category_name, unit, estimated_price, barcode, status.
                        </div>
                    </div>
                </div>

                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-8">
                    <div class="flex-grow-1">
                        <div class="fw-bold mb-2">Import rules</div>
                        <div class="text-gray-700">
                            SKU is used to update existing products. Barcode is optional, but when provided it must be unique across all products.
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.purchase-orders.products') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Import CSV</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>
