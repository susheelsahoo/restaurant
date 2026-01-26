<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($page) ? 'Edit' : 'Create' }} Page</h3>
            </div>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($page) ? route('admin.pages.update', $page->id) : route('admin.pages.store') }}" class="form" enctype="multipart/form-data">
                @csrf
                @if(isset($page)) @method('PUT') @endif

                <div class="card-body border-top p-9">

                    {{-- Display Validation Errors --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Title --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Title</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="title" class="form-control form-control-lg form-control-solid @error('title') is-invalid @enderror" placeholder="Title" value="{{ old('title', $page->title ?? '') }}" required />
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Content</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="content" id="page-content" class="form-control form-control-lg form-control-solid @error('content') is-invalid @enderror" rows="10">{{ old('content', $page->content ?? '') }}</textarea>
                            @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Meta Title --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Meta Title</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="meta_title" class="form-control form-control-lg form-control-solid @error('meta_title') is-invalid @enderror" placeholder="Meta Title" value="{{ old('meta_title', $page->meta_title ?? '') }}" />
                            @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Meta Description --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Meta Description</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="meta_description" class="form-control form-control-lg form-control-solid @error('meta_description') is-invalid @enderror" rows="3">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                            @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Meta Keywords --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Meta Keywords</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="meta_keywords" class="form-control form-control-lg form-control-solid @error('meta_keywords') is-invalid @enderror" placeholder="Meta Keywords" value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}" />
                            @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="row mb-0">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Active</label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <div class="form-check form-check-solid form-switch">
                                <input class="form-check-input w-45px h-30px" type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active ?? false) ? 'checked' : '' }} />
                                <label class="form-check-label ms-2">Yes</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                    <button type="submit" class="btn btn-primary">{{ isset($page) ? 'Update' : 'Create' }} Page</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/cismxthoy7vle21aqdwdz4tgkaua80tos798n4cl379djpp4/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'anchor autolink charmap code codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code',
        });
    </script>
    @endpush
</x-default-layout>