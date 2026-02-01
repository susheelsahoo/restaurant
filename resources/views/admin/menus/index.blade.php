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
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} Add Menu
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                        <tr>
                            <td>{{ $menu->position }}</td>
                            <td>{{ $menu->name }}</td>
                            <td>{{ $menu->category->name ?? '-' }}</td>
                            <td>{{ $menu->price ? number_format($menu->price, 2) : '-' }}</td>
                            <td>
                                {!! $menu->is_active
                                ? '<span class="badge badge-success">Active</span>'
                                : '<span class="badge badge-danger">Inactive</span>' !!}
                            </td>
                            <td>
                                <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <a href="{{ route('admin.menus.show', $menu->id) }}" class="btn btn-sm btn-info">View</a>
                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this menu item?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No menu items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $menus->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</x-default-layout>