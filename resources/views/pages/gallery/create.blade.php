<x-default-layout>
    @section('title')
    {{ isset($galleryImage) ? 'Edit Gallery Image' : 'Add Gallery Image' }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title">{{ isset($galleryImage) ? 'Edit Gallery Image' : 'Add New Gallery Image' }}</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ isset($galleryImage) ? route('admin.gallery.update', $galleryImage->id) : route('admin.gallery.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($galleryImage))
                @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Image Title"
                        value="{{ old('title', $galleryImage->title ?? '') }}">
                    @error('title')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" name="image" id="image" class="form-control">
                    @if (isset($galleryImage) && $galleryImage->image_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $galleryImage->image_path) }}" alt="Gallery Image" class="img-thumbnail" width="150">
                    </div>
                    @endif
                    @error('image')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                        {{ old('is_active', $galleryImage->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="form-check-label">Active</label>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="frontend_display" id="frontend_display" class="form-check-input"
                        {{ old('frontend_display', $galleryImage->frontend_display ?? true) ? 'checked' : '' }}>
                    <label for="frontend_display" class="form-check-label">Display on Frontend</label>
                </div>

                <button type="submit" class="btn btn-primary">{{ isset($galleryImage) ? 'Update' : 'Save' }}</button>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</x-default-layout>