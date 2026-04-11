<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <h3 class="fw-bold m-0">{{ isset($supplier) ? 'Edit' : 'Create' }} Supplier</h3>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($supplier) ? route('admin.purchase-orders.suppliers.update', $supplier->id) : route('admin.purchase-orders.suppliers.store') }}">
                @csrf
                @if(isset($supplier))
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
                            <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $supplier->name ?? '') }}" required>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Email</label>
                        <div class="col-lg-8 fv-row">
                            <input type="email" name="email" class="form-control form-control-lg form-control-solid" value="{{ old('email', $supplier->email ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Phone</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="phone" class="form-control form-control-lg form-control-solid" value="{{ old('phone', $supplier->phone ?? '') }}">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                        <div class="col-lg-8 fv-row">
                            <select name="status" class="form-select form-select-lg form-select-solid">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', $supplier->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.purchase-orders.suppliers') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ isset($supplier) ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>
