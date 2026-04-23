<x-default-layout>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <form method="GET" class="d-flex flex-wrap gap-3 align-items-center">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-solid w-250px" placeholder="Search category">

                    <select name="status" class="form-select form-select-solid w-180px">
                        <option value="">Status: All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>

                    <button class="btn btn-light-primary">Filter</button>

                    @if(request()->hasAny(['q', 'status']))
                        <a href="{{ route('admin.purchase-orders.product-categories.index') }}" class="btn btn-light">Reset</a>
                    @endif
                </form>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('admin.purchase-orders.product-categories.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!} Add Product Category
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Monthly Budget</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="fw-bold">{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->description ?: '-' }}</td>
                                <td>{{ number_format((int) $category->monthly_budget) }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $category->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($category->status) }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.purchase-orders.product-categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form action="{{ route('admin.purchase-orders.product-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">No product categories found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $categories->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</x-default-layout>
