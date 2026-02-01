<x-default-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <form method="GET" class="d-flex">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid w-250px" placeholder="Search Menu">
                    <button class="btn btn-light ms-2">Search</button>
                </form>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.wines.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} Add Wine
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed">
                    <thead>
                        <tr>
                            <!--<th>Position</th>-->
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wines as $wine)
                        <tr>

                            <td>{{ $wine->title }}</td>
                            <td>{{ $wine->category->name ?? '-' }}</td>
                            <td>{{ $wine->price }} {{ config('app.price_sign') }}</td>
                            <td>
                                {!! $wine->is_active
                                ? '<span class="badge badge-success">Active</span>'
                                : '<span class="badge badge-danger">Inactive</span>' !!}
                            </td>
                            <td>
                                <a href="{{ route('admin.wines.edit', $wine->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <a href="{{ route('admin.wines.show', $wine->id) }}" class="btn btn-sm btn-info">View</a>
                                <form action="{{ route('admin.wines.destroy', $wine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this wine item?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No wine items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $wines->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</x-default-layout>