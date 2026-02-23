<x-default-layout>

    @section('title')
    Email Templates
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.email-templates.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Search template" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Template
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th>Slug</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @forelse($templates as $template)
                        <tr>
                            <td>
                                <code class="bg-light px-2 py-1">{{ $template->slug }}</code>
                            </td>
                            <td>{{ $template->title }}</td>
                            <td>{{ Str::limit($template->subject, 50) }}</td>
                            <td>
                                @if($template->is_active)
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form method="POST" action="{{ route('admin.email-templates.destroy', $template->id) }}" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            {!! getIcon('trash', 'fs-3', '', 'i') !!}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="text-gray-500">No email templates found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-default-layout>