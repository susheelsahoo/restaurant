<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($tag) ? 'Edit' : 'Create' }} Tag</h3>
            </div>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($tag) ? route('admin.tags.update', $tag->id) : route('admin.tags.store') }}" class="form">
                @csrf
                @if(isset($tag)) @method('PUT') @endif

                <div class="card-body border-top p-9">
                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Name -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Name</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="name"
                                class="form-control form-control-lg form-control-solid @error('name') is-invalid @enderror"
                                placeholder="Tag Name"
                                value="{{ old('name', $tag->name ?? '') }}" required />
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Slug (optional)</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="slug"
                                class="form-control form-control-lg form-control-solid @error('slug') is-invalid @enderror"
                                placeholder="tag-slug"
                                value="{{ old('slug', $tag->slug ?? '') }}" />
                            @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ isset($tag) ? 'Update' : 'Create' }} Tag</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>