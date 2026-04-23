<x-default-layout>
    @section('title')
        Purchase Order Templates
    @endsection

    <div class="row g-5 g-xl-8 mb-8">
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold">{{ $stats['total'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Total Templates</div>
                    <div class="text-muted fs-7 mt-2">Reusable request templates available in the system.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-success">{{ $stats['active'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Active</div>
                    <div class="text-muted fs-7 mt-2">Templates ready for daily operations.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-warning">{{ $stats['draft'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Draft</div>
                    <div class="text-muted fs-7 mt-2">Templates still being prepared.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-xl-stretch">
                <div class="card-body">
                    <div class="fs-2hx fw-bold text-danger">{{ $stats['archived'] }}</div>
                    <div class="fs-6 fw-semibold text-gray-500">Archived</div>
                    <div class="text-muted fs-7 mt-2">Templates no longer used in operations.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div>
                    <h3 class="fw-bold mb-1">Template Module</h3>
                    <div class="text-muted fs-6">Create, update, and manage reusable purchase order templates.</div>
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.purchase-order-templates.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} Add Template
                </a>
            </div>
        </div>

        <div class="card-body pt-0">
            <form method="GET" class="row g-3 align-items-end mb-6">
                <div class="col-md-6 col-xl-4">
                    <label class="form-label fw-semibold fs-7">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid" placeholder="Template name, description, department...">
                </div>

                <div class="col-md-6 col-xl-3">
                    <label class="form-label fw-semibold fs-7">Department</label>
                    <select name="department_id" class="form-select form-select-solid">
                        <option value="">All departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-xl-3">
                    <label class="form-label fw-semibold fs-7">Status</label>
                    <select name="status" class="form-select form-select-solid">
                        <option value="">All statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-xl-2 d-flex gap-2">
                    <button class="btn btn-light-primary flex-fill">Filter</button>
                    @if(request()->hasAny(['q', 'department_id', 'status']))
                        <a href="{{ route('admin.purchase-order-templates.index') }}" class="btn btn-light">Reset</a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th>Template</th>
                            <th>Department</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    <div class="fw-bold text-gray-900">{{ $template->name }}</div>
                                    <div class="text-muted fs-7">{{ \Illuminate\Support\Str::limit($template->description ?: 'No description added.', 70) }}</div>
                                </td>
                                <td>{{ $template->department->name ?? 'All departments' }}</td>
                                <td><span class="badge badge-light-info">{{ ucfirst($template->priority) }}</span></td>
                                <td>
                                    <span class="badge badge-light-{{ $template->status === 'active' ? 'success' : ($template->status === 'draft' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($template->status) }}
                                    </span>
                                </td>
                                <td>{{ $template->items_count }}</td>
                                <td>{{ optional($template->updated_at)->format('d M Y, h:i A') ?: '-' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.purchase-order-templates.duplicate', $template->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light-primary" title="Duplicate template">
                                            {!! getIcon('copy', 'fs-3', '', 'i') !!}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.purchase-order-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form method="POST" action="{{ route('admin.purchase-order-templates.destroy', $template->id) }}" class="d-inline" onsubmit="return confirm('Delete this template?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            {!! getIcon('trash', 'fs-3', '', 'i') !!}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">No purchase order templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $templates->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</x-default-layout>
