<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <h3 class="fw-bold m-0">{{ isset($menu) ? 'Edit' : 'Create' }} Menu Item</h3>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($menu) ? route('admin.menus.update', $menu->id) : route('admin.menus.store') }}" enctype="multipart/form-data">
                @csrf
                @if(isset($menu)) @method('PUT') @endif

                <div class="card-body border-top p-9">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                    @endif

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Title</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="title" class="form-control form-control-lg form-control-solid"
                                value="{{ old('title', $menu->title ?? '') }}" required />
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Slug</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="slug" class="form-control form-control-lg form-control-solid"
                                value="{{ old('slug', $menu->slug ?? '') }}" placeholder="Optional">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Description</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="description" class="form-control form-control-lg form-control-solid" rows="4">{{ old('description', $menu->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Price</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="price" class="form-control form-control-lg form-control-solid" value="{{ old('price', $menu->price ?? '') }}" placeholder="e.g. 12.50">
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Category</label>
                        <div class="col-lg-8 fv-row">
                            <select name="menu_category_id" class="form-control form-select">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('menu_category_id', $menu->menu_category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Image</label>
                        <div class="col-lg-8 fv-row">
                            <input type="file" name="image" class="form-control form-control-lg form-control-solid">
                            @if(isset($menu) && $menu->image)
                            <div class="mt-2"><img src="{{ asset('storage/' . $menu->image) }}" alt="" width="120"></div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Active</label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menu->is_active ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Position</label>
                        <div class="col-lg-8 fv-row">
                            <input type="number" name="position" class="form-control form-control-lg form-control-solid" value="{{ old('position', $menu->position ?? 0) }}">
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ isset($menu) ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>