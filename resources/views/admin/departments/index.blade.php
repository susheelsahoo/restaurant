<x-default-layout>
    @section('title', 'Departments')
    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.purchase-orders.departments.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <input type="text" class="form-control form-control-solid w-250px" placeholder="Search Department">
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.purchase-orders.departments.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} Add Department
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total Requests</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                        <tr>
                            <td>{{ $department->name }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $department->requests()->count() }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.purchase-orders.departments.edit', $department->id) }}" class="btn btn-sm btn-warning">
                                    {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                </a>
                                <form action="{{ route('admin.purchase-orders.departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No departments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-default-layout>