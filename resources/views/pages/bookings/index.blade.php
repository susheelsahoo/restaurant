<x-default-layout>
    @section('title', 'Bookings Management')

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bookings.index') }}
    @endsection

    {{-- FILTER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        {{-- LEFT SIDE — Filters + Search --}}
        <form method="GET"
            action="{{ route('admin.bookings.index') }}"
            class="d-flex gap-3 align-items-center">

            {{-- Status --}}
            <select name="status"
                onchange="this.form.submit()"
                class="form-select w-auto">
                <option value="">All Status</option>
                @foreach (config('app.statuses') as $statusKey => $statusValue)
                <option value="{{ $statusKey }}"
                    {{ request('status') === $statusKey ? 'selected' : '' }}>
                    {{ $statusValue }}
                </option>
                @endforeach
            </select>

            {{-- Date --}}
            <input type="date"
                name="visit_date"
                value="{{ request('visit_date') }}"
                class="form-control w-auto"
                onchange="this.form.submit()">

            {{-- Search --}}
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search Booking Code / Name"
                class="form-control w-250px"
                onkeyup="if(event.key==='Enter'){this.form.submit()}">

            <button type="submit" class="btn btn-sm btn-primary">
                Search
            </button>

            {{-- Clear --}}
            <a href="{{ route('admin.bookings.index') }}"
                class="btn btn-sm btn-dark">
                Clear
            </a>



        </form>

        {{-- RIGHT SIDE — Button --}}
        <div>
            <a href="{{ route('admin.bookings.create') }}"
                class="btn btn-primary">
                Add Booking
            </a>
        </div>

    </div>



    <div class="card">
        <div class="card-header border-0 pt-6">



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
                                {{ $booking->customer->first_name ?? $booking->customer_name }}
                                {{ $booking->customer->last_name ?? '' }}
                            </td>

                            <td>
                                {{ $booking->customer->phone ?? $booking->phone }}<br>
                                <small class="text-muted">
                                    {{ $booking->customer->email ?? $booking->email }}
                                </small>

                            </td>

                            <td>
                                {{ $booking->visit_date->format('d M Y') }}
                                <br>
                                <small class="text-muted">{{ $booking->visit_time }}</small>
                            </td>

                            <td>
                                {{ $booking->guests }}
                            </td>

                            <td>
                                @php
                                $statusColors = [
                                'pending' => 'warning', // yellow
                                'confirmed' => 'success', // green
                                'declined' => 'danger', // red
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

                                <a href="{{ route('admin.bookings.edit', $booking->id) }}"
                                    class="btn btn-sm btn-warning">
                                    Edit
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
                {{ $bookings->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</x-default-layout>