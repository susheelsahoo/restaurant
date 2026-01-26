<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($banner) ? 'Edit' : 'Create' }} Banner</h3>
            </div>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($banner) ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}" class="form" enctype="multipart/form-data">
                @csrf
                @if(isset($banner)) @method('PUT') @endif

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

                    <!-- Title -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Title</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="title" class="form-control form-control-lg form-control-solid @error('title') is-invalid @enderror" placeholder="Title" value="{{ old('title', $banner->title ?? '') }}" required />
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Content</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="content" id="banner-content" class="form-control form-control-lg form-control-solid @error('content') is-invalid @enderror" rows="5" placeholder="Enter banner content">{{ old('content', $banner->content ?? '') }}</textarea>
                            @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- Active Status -->
                    <div class="row mb-0">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Active</label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <div class="form-check form-check-solid form-switch">
                                <input class="form-check-input w-45px h-30px" type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }} />
                                <label class="form-check-label ms-2">Yes</label>
                            </div>
                            @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                    <button type="submit" class="btn btn-primary">{{ isset($banner) ? 'Update' : 'Create' }} Banner</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.tiny.cloud/1/cismxthoy7vle21aqdwdz4tgkaua80tos798n4cl379djpp4/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#banner-content',
            height: 300,
            plugins: 'link image code lists table fullscreen preview',
            toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code preview fullscreen',
            menubar: false
        });
    </script>
    @endpush
</x-default-layout>