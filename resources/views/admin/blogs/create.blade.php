<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($blog) ? 'Edit' : 'Create' }} Blog</h3>
            </div>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($blog) ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store') }}" class="form" enctype="multipart/form-data">
                @csrf
                @if(isset($blog)) @method('PUT') @endif

                <div class="card-body border-top p-9">
                    <!-- Validation Errors -->
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
                            <input type="text" name="title" class="form-control form-control-lg form-control-solid @error('title') is-invalid @enderror" placeholder="Blog Title" value="{{ old('title', $blog->title ?? '') }}" required />
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Slug</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="slug" class="form-control form-control-lg form-control-solid @error('slug') is-invalid @enderror" placeholder="Optional slug" value="{{ old('slug', $blog->slug ?? '') }}" />
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Category</label>
                        <div class="col-lg-8 fv-row">
                            <select name="category_id" class="form-select form-select-lg form-select-solid @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Tags</label>
                        <div class="col-lg-8 fv-row">
                            <select name="tags[]" class="form-select form-select-lg form-select-solid @error('tags') is-invalid @enderror" multiple>
                                @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', isset($blog) ? $blog->tags->pluck('id')->toArray() : [])) ? 'selected' : '' }}>{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            @error('tags')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Content</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="content" class="form-control form-control-lg form-control-solid @error('content') is-invalid @enderror" rows="6" placeholder="Enter blog content">{{ old('content', $blog->content ?? '') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Image</label>
                        <div class="col-lg-8 fv-row">
                            <input type="file" name="image" class="form-control form-control-lg form-control-solid @error('image') is-invalid @enderror" />
                            @if(isset($blog) && $blog->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $blog->image) }}" alt="Blog Image" class="img-thumbnail" width="150">
                            </div>
                            @endif
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Published Status -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Published</label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <div class="form-check form-check-solid form-switch">
                                <input class="form-check-input w-45px h-30px" type="checkbox" name="is_published" value="1" {{ old('is_published', $blog->is_published ?? false) ? 'checked' : '' }} />
                                <label class="form-check-label ms-2">Yes</label>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Title -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Meta Title</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="meta_title" class="form-control form-control-lg form-control-solid @error('meta_title') is-invalid @enderror" placeholder="SEO Meta Title" value="{{ old('meta_title', $blog->meta_title ?? '') }}" />
                            @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Meta Description -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Meta Description</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="meta_description" class="form-control form-control-lg form-control-solid @error('meta_description') is-invalid @enderror" rows="3" placeholder="SEO Meta Description">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
                            @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                    <button type="submit" class="btn btn-primary">{{ isset($blog) ? 'Update' : 'Create' }} Blog</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="{{ config('app.tinymce_api_url') }}" referrerpolicy="origin" crossorigin="anonymous"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'anchor autolink charmap code codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code',
        });
    </script>
    @endpush
</x-default-layout>