<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <h3 class="fw-bold m-0">{{ isset($department) ? 'Edit' : 'Create' }} Department</h3>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($department) ? route('admin.purchase-orders.departments.update', $department->id) : route('admin.purchase-orders.departments.store') }}">
                @csrf
                @if(isset($department)) @method('PUT') @endif

                <div class="card-body border-top p-9">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Name</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="name" class="form-control form-control-lg form-control-solid"
                                value="{{ old('name', $department->name ?? '') }}" required />
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.purchase-orders.departments.index') }}" class="btn btn-light me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        {{ isset($department) ? 'Update' : 'Create' }} Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>