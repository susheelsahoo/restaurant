<x-default-layout>
    @section('title', 'Bookings Management')

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bookings.index') }}
    @endsection

    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="mb-4">
        <select name="status" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="">All Status</option>

            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>
                New
            </option>

            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                Confirmed
            </option>

            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                Cancelled
            </option>

            <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>
                Complete
            </option>
        </select>

    </form>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <input
                    type="text"
                    class="form-control form-control-solid w-250px"
                    placeholder="Search Booking Code / Name" />
            </div>

            <div class="card-toolbar">
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                    Add Booking
                </a>
            </div>
        </div>

        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>Booking Code</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Date & Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->booking_code }}</strong>
                            </td>

                            <td>
                                {{ $booking->customer_name }}
                            </td>

                            <td>
                                {{ $booking->phone }}<br>
                                <small class="text-muted">{{ $booking->email }}</small>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($booking->visit_date)->format('d M Y') }}<br>
                                <small class="text-muted">{{ $booking->visit_time }}</small>
                            </td>

                            <td>
                                {{ $booking->guests }}
                            </td>

                            <td>
                                @php
                                $statusColors = [
                                'new' => 'warning', // yellow
                                'confirmed' => 'success', // green
                                'cancelled' => 'danger', // red
                                'complete' => 'primary', // blue
                                ];
                                @endphp

                                <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">
                                    {{ ucfirst($booking->status) }}
                                </span>

                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                    class="btn btn-sm btn-info">
                                    View
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.bookings.destroy', $booking->id) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-default-layout>