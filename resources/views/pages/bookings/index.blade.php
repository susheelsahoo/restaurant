<x-default-layout>
    @section('title', 'Bookings Management')

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bookings.index') }}
    @endsection
    @php
    // Only default to today if there's an explicit select_date parameter
    $select_date = request('select_date')
    ? \Carbon\Carbon::parse(request('select_date'))
    : null;
    @endphp
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
                @php
                $statuses = \App\Models\ReservationStatus::active()->ordered()->get();
                @endphp
                @foreach ($statuses as $status)
                <option value="{{ $status->name }}"
                    {{ request('status') === $status->name ? 'selected' : '' }}>
                    {{ $status->label }}
                </option>
                @endforeach
            </select>

            {{-- Date --}}
            <input type="date"
                name="select_date"
                value="{{ $select_date?->format('Y-m-d') ?? '' }}"
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
        <div class="d-flex gap-2">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.bookings.export', array_merge(['format' => 'xlsx'], request()->query())) }}">
                            <i class="fas fa-file-excel"></i> Export to Excel (XLSX)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.bookings.export', array_merge(['format' => 'csv'], request()->query())) }}">
                            <i class="fas fa-file-csv"></i> Export to CSV
                        </a>
                    </li>
                </ul>
            </div>
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
                                <strong>{{ $booking->notes }}</strong>
                            </td>

                            <td>
                                {{ $booking->customer->first_name ?? $booking->customer_name }}
                                {{ $booking->customer->last_name ?? '' }}
                                @if($booking->customer)
                                <span class="badge badge-light-info ms-2">
                                    {{ $booking->customer->reservations_count }}
                                </span>
                                @endif
                                @if($booking->customer_id)
                                <span class="view-notes-btn ms-2 text-primary"
                                    style="cursor:pointer"
                                    data-customer-id="{{ $booking->customer_id }}">
                                    <i class="fas fa-sticky-note"></i>
                                </span>
                                @endif
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
                                <small class="text-muted">{{ $booking->visit_time->format('g:i A') }} </small>

                            </td>

                            <td>
                                {{ $booking->guests }}
                            </td>

                            <td>
                                @php
                                $status = $booking->reservationStatus;
                                @endphp

                                <span class="badge bg-{{ $status->color ?? 'secondary' }}">
                                    {{ $status->label ?? 'Unknown' }}
                                </span>

                            </td>

                            <td class="text-end">
                                @php
                                $customerName = trim(($booking->customer->first_name ?? $booking->customer_name ?? '') . ' ' . ($booking->customer->last_name ?? ''));
                                $contactPhone = $booking->customer->phone ?? $booking->phone ?? 'N/A';
                                $contactEmail = $booking->customer->email ?? $booking->email ?? 'N/A';
                                $bookingStatus = $booking->reservationStatus;
                                @endphp
                                <a href="{{ route('admin.bookings.show', array_merge(['booking' => $booking->id], request()->query())) }}"
                                    class="btn btn-sm btn-info booking-view-btn"
                                    data-booking-code="{{ $booking->booking_code }}"
                                    data-customer-name="{{ $customerName }}"
                                    data-contact-phone="{{ $contactPhone }}"
                                    data-contact-email="{{ $contactEmail }}"
                                    data-visit-date="{{ $booking->visit_date->format('d M Y') }}"
                                    data-visit-time="{{ $booking->visit_time->format('g:i A') }}"
                                    data-guests="{{ $booking->guests }}"
                                    data-notes="{{ $booking->notes ?? 'N/A' }}"
                                    data-status-label="{{ $bookingStatus->label ?? 'Unknown' }}"
                                    data-status-color="{{ $bookingStatus->color ?? 'secondary' }}">
                                     {!! getIcon('eye', 'fs-3', '', 'i') !!}
                                </a>
                                <a href="{{ route('admin.bookings.edit', array_merge(['booking' => $booking->id], request()->query())) }}"
                                    class="btn btn-sm btn-warning">
                                    {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                </a>

                                @if(auth()->check() && auth()->user()->getAllPermissions()->contains('name', 'delete'))
                                <form
                                    method="POST"
                                    action="{{ route('admin.bookings.destroy', array_merge(['booking' => $booking->id], request()->query())) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        {!! getIcon('trash', 'fs-3', '', 'i') !!}
                                    </button>
                                </form>
                                @endif
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
    <x-customer-notes />

    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-6">
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Booking Code</label>
                            <div class="fw-bold" id="modal-booking-code">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Status</label>
                            <span class="badge" id="modal-booking-status">-</span>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Customer Name</label>
                            <div id="modal-customer-name">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Guests</label>
                            <div id="modal-booking-guests">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Phone</label>
                            <div id="modal-contact-phone">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Email</label>
                            <div id="modal-contact-email">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Visit Date</label>
                            <div id="modal-visit-date">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted d-block mb-1">Visit Time</label>
                            <div id="modal-visit-time">-</div>
                        </div>
                        <div class="col-12">
                            <label class="fw-semibold text-muted d-block mb-1">Notes</label>
                            <div class="text-gray-800" id="modal-booking-notes">-</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingModalElement = document.getElementById('bookingDetailsModal');

            if (!bookingModalElement) {
                return;
            }

            const bookingModal = new bootstrap.Modal(bookingModalElement);
            const modalFields = {
                bookingCode: document.getElementById('modal-booking-code'),
                customerName: document.getElementById('modal-customer-name'),
                contactPhone: document.getElementById('modal-contact-phone'),
                contactEmail: document.getElementById('modal-contact-email'),
                visitDate: document.getElementById('modal-visit-date'),
                visitTime: document.getElementById('modal-visit-time'),
                guests: document.getElementById('modal-booking-guests'),
                notes: document.getElementById('modal-booking-notes'),
                status: document.getElementById('modal-booking-status'),
            };

            document.querySelectorAll('.booking-view-btn').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    modalFields.bookingCode.textContent = this.dataset.bookingCode || 'N/A';
                    modalFields.customerName.textContent = this.dataset.customerName || 'N/A';
                    modalFields.contactPhone.textContent = this.dataset.contactPhone || 'N/A';
                    modalFields.contactEmail.textContent = this.dataset.contactEmail || 'N/A';
                    modalFields.visitDate.textContent = this.dataset.visitDate || 'N/A';
                    modalFields.visitTime.textContent = this.dataset.visitTime || 'N/A';
                    modalFields.guests.textContent = this.dataset.guests || 'N/A';
                    modalFields.notes.textContent = this.dataset.notes || 'N/A';
                    modalFields.status.textContent = this.dataset.statusLabel || 'Unknown';
                    modalFields.status.className = 'badge bg-' + (this.dataset.statusColor || 'secondary');

                    bookingModal.show();
                });
            });
        });
    </script>
    @endpush

</x-default-layout>
