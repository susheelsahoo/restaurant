<x-default-layout>
    @section('title')
    Gallery
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.gallery.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Search Gallery" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Image
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Copy</th>
                            <th>Home Display</th>
                            <th>Gallery Display</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($galleryImages as $image)
                        <tr>
                            <td>{{ $image->title ?? 'No Title' }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->title ?? 'Gallery Image' }}" width="60" height="60" class="img-thumbnail">
                            </td>
                            <td>
                                <button class="badge badge-info" onclick="copyToClipboard('{{ asset('storage/' . $image->image_path) }}')">Copy Path</button>
                            </td>

                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-switch"
                                        type="checkbox"
                                        data-id="{{ $image->id }}"
                                        data-field="home_display"
                                        {{ $image->home_display ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-switch"
                                        type="checkbox"
                                        data-id="{{ $image->id }}"
                                        data-field="gallery_display"
                                        {{ $image->gallery_display ? 'checked' : '' }}>
                                </div>
                            </td>

                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-switch"
                                        type="checkbox"
                                        data-id="{{ $image->id }}"
                                        data-field="is_active"
                                        {{ $image->is_active ? 'checked' : '' }}>
                                </div>
                            </td>

                            <td>
                                <a href="{{ route('admin.gallery.edit', $image->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.gallery.destroy', $image->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No images found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                console.log('Image path copied to clipboard!');
            }).catch((err) => {
                alert('Failed to copy image path.');
                console.error('Error:', err);
            });
        }
        document.addEventListener("change", async function(e) {

            if (!e.target.classList.contains("toggle-switch")) return;

            const checkbox = e.target;

            try {

                await axios.post("{{ route('admin.gallery.toggle') }}", {
                    id: checkbox.dataset.id,
                    field: checkbox.dataset.field
                });

            } catch (error) {

                alert("Update failed");
                checkbox.checked = !checkbox.checked; // rollback UI

                console.error(error);
            }
        });
    </script>
    @endpush

</x-default-layout>