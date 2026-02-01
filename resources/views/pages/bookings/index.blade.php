<x-default-layout>
    @section('title')
    Bookings Management
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bookings.index') }}
    @endsection
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="mb-3">
        <select name="status" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="">All Status</option>
            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
            <option value="confirm" {{ request('status') == 'confirm' ? 'selected' : '' }}>Confirm</option>
            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
    </form>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Search Booking" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Booking
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
                        @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->name }}</td>
                            <td>{{ $booking->email }}</td>
                            <td>{{ $booking->phone ?? 'No Phone' }}</td>
                            <td>{{ $booking->hotel_size ?? 'NA' }}</td>
                            <td>{{ $booking->source ?? 'No Source' }}</td>
                            <td>
                                @php
                                $statusColors = [
                                'new' => 'primary',
                                'confirm' => 'warning',
                                'closed' => 'secondary',
                                ];
                                @endphp

                                <span class="badge bg-{{ $statusColors[$booking->status] ?? 'dark' }}">
                                    {{ ucfirst($booking->status ?? 'NA') }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">View</a>
                                <form method="POST" action="{{ route('admin.bookings.destroy', $booking->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No bookings found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Optional: Add custom scripts for booking management here
    </script>
    @endpush
</x-default-layout>