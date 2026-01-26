<x-default-layout>
    @section('title')
    Leads
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.leads.index') }}
    @endsection
    <form method="GET" action="{{ route('admin.leads.index') }}" class="mb-3">
        <select name="status" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="">All Status</option>
            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
    </form>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Search Lead" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Lead
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Hotel Size</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->email }}</td>
                            <td>{{ $lead->phone ?? 'No Phone' }}</td>
                            <td>{{ $lead->hotel_size ?? 'NA' }}</td>
                            <td>{{ $lead->source ?? 'No Source' }}</td>
                            <td>
                                @php
                                $statusColors = [
                                'new' => 'primary',
                                'in_progress' => 'warning',
                                'converted' => 'success',
                                'closed' => 'secondary',
                                ];
                                @endphp

                                <span class="badge bg-{{ $statusColors[$lead->status] ?? 'dark' }}">
                                    {{ ucfirst($lead->status ?? 'NA') }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn btn-sm btn-info">View</a>
                                <form method="POST" action="{{ route('admin.leads.destroy', $lead->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No leads found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Optional: Add custom scripts for lead management here
    </script>
    @endpush
</x-default-layout>