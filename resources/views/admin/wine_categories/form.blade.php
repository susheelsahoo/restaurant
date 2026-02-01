<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <h3 class="fw-bold m-0">{{ isset($wineCategory) ? 'Edit' : 'Create' }} Wine Category</h3>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($wineCategory) ? route('admin.wine-categories.update', $wineCategory->id) : route('admin.wine-categories.store') }}">
                @csrf
                @if(isset($wineCategory)) @method('PUT') @endif

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
                                value="{{ old('name', $wineCategory->name ?? '') }}" required />
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Slug</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="slug" class="form-control form-control-lg form-control-solid"
                                value="{{ old('slug', $wineCategory->slug ?? '') }}" placeholder="Optional">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Description</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="description" class="form-control form-control-lg form-control-solid" rows="4">{{ old('description', $wineCategory->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Active</label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menuCategory->is_active ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.wine-categories.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ isset($wineCategory) ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>